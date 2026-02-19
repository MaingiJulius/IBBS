<?php
// =================================================================
// BOOKING PROCESSING ENGINE (process_booking.php)
// =================================================================
// This script runs in the background (via AJAX fetch) to handle ticket bookings.
// It receives data from 'book.php', validates it, and saves it to the database.
// It returns a JSON response indicating success or failure.
// =================================================================

// Include the database connection so we can talk to MySQL.
require_once 'db_connection.php';

// Start the session to identify the currently logged-in user.
session_start();

// Tell the browser that the output of this script is strictly JSON data.
// This ensures the JavaScript on the frontend parses the response correctly.
header('Content-Type: application/json');

// --- SECURITY CHECK ---
// We verify if the user is logged in by checking the session variable.
if (!isset($_SESSION['user_id'])) {
    // If not logged in, return a failure message.
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    // Stop the script immediately.
    exit();
}

// --- RECEIVE INPUT DATA ---
// We read the raw data sent in the request body (because it's JSON, not a standard form POST).
$raw_input = file_get_contents("php://input");
// We decode the JSON string into a PHP associative array.
$data = json_decode($raw_input, true);

// Extract specific pieces of information from the decoded data.
// We use the '??' operator (null coalescing) to provide defaults if data is missing.
$route_id = $data['route_id'] ?? null;              // The ID of the trip being booked
$target_user_id = $data['target_user_id'] ?? $_SESSION['user_id']; // ID of user who gets the ticket
$bookings_to_process = $data['bookings'] ?? [];     // List of seats and passenger details

// --- VALIDATION ---
// Check if we have the essential information needed to proceed.
if (!$route_id || empty($bookings_to_process)) {
    // If route ID is missing or the list of bookings is empty...
    echo json_encode(['success' => false, 'message' => 'Missing booking selection']);
    exit();
}

// --- FETCH TRIP DETAILS ---
// We need to know which bus is used for this route to get its Bus ID.
// We Prepare a SQL statement to prevent SQL Injection attacks.
$stmt = $conn->prepare("SELECT r.*, b.bus_id, b.max_passengers 
                        FROM routes r 
                        JOIN buses b ON r.bus_id = b.bus_id 
                        WHERE r.route_id = ?");
// Bind the route_id parameter (integer type 'i').
$stmt->bind_param("i", $route_id);
// Execute the query.
$stmt->execute();
// Get the result and fetch it as an associative array.
$route = $stmt->get_result()->fetch_assoc();
// Close the statement to free up resources.
$stmt->close();

// If the route ID did not match any trip in the database...
if (!$route) {
    echo json_encode(['success' => false, 'message' => 'Route not found']);
    exit();
}

// --- NEW PRIVILEGE CHECK: PREVENT DOUBLE BOOKING ---
// We check if this passenger (target_user_id) already has an active booking for this specific trip.
// This prevents users from hoarding seats or booking the same trip multiple times by mistake.
$stmt_check = $conn->prepare("SELECT booking_id FROM bookings WHERE user_id = ? AND route_id = ? AND booking_status != 'CANCELLED'");
$stmt_check->bind_param("ii", $target_user_id, $route_id);
$stmt_check->execute();
$existing_res = $stmt_check->get_result();
if ($existing_res->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This passenger already has an active booking for this route. To book again, the previous ticket must be cancelled.']);
    $stmt_check->close();
    exit();
}
$stmt_check->close();

// --- START TRANSACTION ---
// A transaction ensures that either ALL seats are booked successfully, or NONE are.
// This prevents "partial bookings" if an error occurs halfway through.
$conn->begin_transaction();

try {
    // Loop through each seat requested by the user.
    foreach ($bookings_to_process as $b) {
        // Extract details for this specific seat.
        $seat_no = $b['seat_number'];
        $p_name = $b['passenger_name']; // Passenger Name
        $p_age = $b['passenger_age'];   // Passenger Age
        $p_id = $b['passenger_id_number']; // Passenger ID

        // --- STEP A: CHECK FOR DOUBLE BOOKING ---
        // Check if this specific seat was taken by someone else just seconds ago.
        // We look for any active booking (not cancelled) for this route and seat.
        $stmt = $conn->prepare("SELECT booking_id FROM bookings WHERE route_id = ? AND seat_number = ? AND booking_status != 'CANCELLED'");
        $stmt->bind_param("is", $route_id, $seat_no);
        $stmt->execute();
        
        // If the query returns any rows, it means the seat is occupied.
        if ($stmt->get_result()->num_rows > 0) {
            // We throw an 'Exception' to jump straight to the 'catch' block.
            throw new Exception("Seat $seat_no is already taken.");
        }
        $stmt->close();

        // --- STEP B: GENERATE DIGITAL TOKEN ---
        // --------------------------------------------------------------------------------------
        // DETAILED TOKEN GENERATION EXPLANATION (Line 137):
        // 1. random_bytes(16): 
        //    This function generates 16 bytes (128 bits) of cryptographically secure pseudo-random bytes. 
        //    It pulls "entropy" (unpredictable data) from the operating system's kernel (like /dev/urandom on Linux 
        //    or the CryptGenRandom API on Windows). This makes the output statistically unique and impossible 
        //     for an attacker to predict or reverse-engineer.
        //
        // 2. bin2hex(...):
        //    The 16 bytes generated are raw binary data (not readable by humans). 
        //    bin2hex() converts each byte into its two-character hexadecimal representation (0-9, a-f).
        //    Result: A 32-character unique string (e.g., "7f3e8a2b...") that serves as the ticket's "Hash" or 
        //    Digital Fingerprint. This token is stored in the database and used to generate the QR code.
        //
        // WHY NOT USE md5() or sha1()?
        //    Simple hashes of predictable data (like user_id + time) can be "brute-forced." 
        //    By using random_bytes, we ensure that even if two people book at the exact same microsecond, 
        //    their tokens will be completely different.
        // --------------------------------------------------------------------------------------
        // ENCRYPTION / SECURITY EXPLANATION:
        // We use the function 'random_bytes(16)' to generate 16 bytes of cryptographically secure random data.
        // Unlike 'rand()', which is predictable, 'random_bytes()' uses the operating system's entropy source.
        // This makes it impossible for a hacker to guess the next token.
        // 'bin2hex()' converts these raw binary bytes into a readable 32-character hexadecimal string.
        // This string (e.g., "a3f9...") becomes the unique digital fingerprint for this ticket.
        // --------------------------------------------------------------------------------------
        $qr_token = bin2hex(random_bytes(16));
        
        // Get the current date and time for the booking record.
        $booking_time = date('Y-m-d H:i:s'); 
        
        // Set the status. Since this is a prototype, we assume instant payment verification.
        $status = 'PAID'; 
        $bus_id = $route['bus_id'];

        // --- STEP C: INSERT BOOKING RECORD ---
        // We construct the SQL command to save all the data into the 'bookings' table.
        $sql = "INSERT INTO bookings (booking_time, seat_number, booking_status, qr_token, user_id, route_id, bus_id, passenger_name, passenger_age, passenger_id_number) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Prepare the statement.
        $stmt = $conn->prepare($sql);
        
        // Bind all 10 parameters.
        // Types: s=string, i=integer.
        // Ordering matches the '?' placeholders exactly.
        $stmt->bind_param("ssssiiisis", $booking_time, $seat_no, $status, $qr_token, $target_user_id, $route_id, $bus_id, $p_name, $p_age, $p_id);
        
        // Execute the insertion. If it fails (returns false), we throw an error.
        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }
        // Close the statement for this iteration.
        $stmt->close();
    }

    // --- COMMIT TRANSACTION ---
    // If the loop finishes without throwing any exceptions, it means all seats are valid.
    // We 'permit' the changes to be permanently saved to the database.
    $conn->commit();

    // Determine where to redirect the user.
    // If the admin booked it ($target_user_id != session ID), stay on admin page.
    $is_self = ($target_user_id == $_SESSION['user_id']);

    // Send back a success JSON response.
    echo json_encode([
        'success' => true, 
        'message' => 'Booking successfully confirmed for ' . count($bookings_to_process) . ' seat(s)!',
        'redirect' => $is_self ? 'view_user_history.php' : 'view_admin_bookings.php'
    ]);

} catch (Exception $e) {
    // --- ROLLBACK TRANSACTION ---
    // If ANY error occurred (like a taken seat or DB failure), we jump here.
    // 'rollback()' undoes ANY changes made during this transaction block.
    // This ensures we don't end up with one booked seat and one failed seat.
    $conn->rollback();
    
    // Return a failure JSON response with the error message.
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close the main database connection.
$conn->close();
?>
