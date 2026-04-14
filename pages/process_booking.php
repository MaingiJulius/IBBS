<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
ob_start();                                                          // [1.1] Start output buffering IMMEDIATELY to catch any stray output.
try {                                                                // [1.2] Wrap entire script in a try block for complete error coverage.
    require_once 'db_connection.php';                                    // [12] Import the database bridge object ($conn) for MySQL communication.
    session_start();                                                    // [13] Initialize or resume the user session to identify the requester.

    header('Content-Type: application/json');                           // [14] Set the HTTP response header to JSON for the fetch API requester.

    /* --- [4] AUTHENTICATION GATE --- */                               // [15] Marker for the primary security access control check.
    if (!isset($_SESSION['user_id'])) {                                  // [16] verify if the 'user_id' index exists in the active session.
        ob_get_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized Access: Please login.']); // [17] Package and emit a JSON error for the UI.
        exit();                                                          // [18] Halt all further execution to prevent unauthorized data writes.
    }                                                                    // [19] Close the security validation boundary.

    /* --- [5] INPUT DESERIALIZATION --- */                             // [20] Marker for converting raw HTTP data into usable PHP structures.
    $raw_input = file_get_contents("php://input");                       // [21] Read the raw binary stream from the request body (JSON payload).
    $data = json_decode($raw_input, true);                               // [22] Parse the JSON string into an associative PHP nested array.

    $route_id = $data['route_id'] ?? null;                               // [23] Extract the specific trip identifier using null coalescing safety.
    $target_user_id = $data['user_id'] ?? $_SESSION['user_id'];          // [24] Determine if booking for self or a customer (Agent case).
    $passengersData = $data['passengers'] ?? [];                         // [25] Extract the array of passenger objects (seat, name, age, id).

    /* --- [6] PRE-FLIGHT VALIDATION --- */                             // [26] Marker for ensuring minimal dataset requirements are met.
    if (!$route_id || empty($passengersData)) {                          // [27] Ensure both a trip and at least one passenger were specified.
        echo json_encode(['success' => false, 'message' => 'Selection Error: No seats or trip identified.']); // [28] Emit failure response.
        exit();                                                          // [29] Terminate processing due to insufficient data.
    }                                                                    // [30] Close requirements check.

    /* --- [7] TRIP DATA RETRIEVAL --- */                               // [31] Marker for fetching trip context from the database registry.
    $stmt = $conn->prepare("SELECT r.*, b.max_passengers FROM routes r JOIN buses b ON r.bus_id = b.bus_id WHERE r.route_id = ?"); // [32] Query to fetch route data and vehicle capacity.
    $stmt->bind_param("i", $route_id);                                   // [33] Safely bind the integer route identifier to the prepared statement.
    $stmt->execute();                                                    // [34] Command the database to lookup the specific travel segment.
    $route = $stmt->get_result()->fetch_assoc();                         // [35] Capture the trip record into a descriptive associative array.
    $stmt->close();                                                      // [36] Release the statement resource to prevent memory leaks.

    if (!$route) {                                                       // [37] Check if the query actually returned a valid travel route.
        echo json_encode(['success' => false, 'message' => 'System Error: Trip route is invalid or retired.']); // [38] Emit error for UI.
        exit();                                                          // [39] Halt processing as the trip target does not exist.
    }                                                                    // [40] Close route lookup boundary.

    /* --- [8] ATOMIC TRANSACTION COMMENCEMENT --- */                   // [41] Marker for starting the multi-step data write operation.
    $conn->begin_transaction();                                          // [42] Disable auto-commit to ensure "All or Nothing" record writing.

    foreach ($passengersData as $p) {                                // [44] Iterate through each individual passenger seat reservation.
        $seat_id = $p['seat_id'];                                    // [45] Assign the specific seat marker (e.g. S12) to a variable.
        $bus_id = $route['bus_id'];                                  // [46] Retrieve the parent vehicle ID from the trip metadata.
        $p_name = $p['name'];                                        // [47] Capture the traveller's legal name from the input array.
        $p_age = $p['age'];                                          // [48] Capture the traveller's age for record-keeping/discounts.
        $p_doc = $p['id'];                                           // [49] Capture the government identification number for security.
        $booking_status = 'PAID';                                    // [50] Set the default status to 'PAID' for immediate confirmation.

        /* --- [8.A] CONCURRENCY CHECK (RACE CONDITION) --- */       // [51] Marker for checking real-time availability before saving.
        $stmt_check = $conn->prepare("SELECT booking_id FROM bookings WHERE route_id = ? AND seat_number = ? AND booking_status = 'PAID'"); // [52] Query to see if seat is already occupied.
        $stmt_check->bind_param("is", $route_id, $seat_id);          // [53] Bind route ID and seat string to the availability check.
        $stmt_check->execute();                                      // [54] Run the check against the living bookings table.
        
        if ($stmt_check->get_result()->num_rows > 0) {               // [55] If the row count is greater than zero, the seat is taken.
            throw new Exception("Collision Error: Seat $seat_id was just reserved by another customer."); // [56] Trigger failure.
        }                                                            // [57] Close availability check.
        $stmt_check->close();                                        // [58] Clean up the check statement object.

        /* --- [8.B] SECURITY TOKEN GENERATION (DEPRECATED) --- */
        $qr_token = NULL;                                            // QR generation removed as per system simplification.

        /* --- [8.C] DATABASE PERSISTENCE --- */                    // [61] Marker for writing the finalized record to the system ledger.
        $stmt_insert = $conn->prepare("INSERT INTO bookings (user_id, route_id, bus_id, seat_number, passenger_name, passenger_age, passenger_id_number, booking_status, qr_token, booking_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"); // [62] Prepare insertion SQL.
        $stmt_insert->bind_param("iiississs", $target_user_id, $route_id, $bus_id, $seat_id, $p_name, $p_age, $p_doc, $booking_status, $qr_token); // [63] Map variables to columns.
        
        if (!$stmt_insert->execute()) {                              // [64] Execute the write and check for database-level errors.
            throw new Exception("Persistence Error: Failed to record booking for seat " . $seat_id); // [65] Halt on DB failure.
        }                                                            // [66] Close insertion execution block.
        $stmt_insert->close();                                       // [67] Release the insertion statement resource.
    }                                                                // [68] End of the passenger data loop.

    /* --- [9] FINALIZATION --- */                                   // [69] Marker for committing the entire data cluster to disk.
    $conn->commit();                                                 // [70] Apply all changes made in the try block permanently.

    ob_get_clean();                                                  // [70.1] Discard any accidental output (like warnings) before JSON emission.
    echo json_encode(['success' => true, 'message' => 'Success! All seats reserved.', 'ticket_count' => count($passengersData)]); // [71] Emit success.
} catch (Throwable $e) {                                             // [72] Master catch for any failure (Exception or Error).
    if (isset($conn) && $conn->connect_errno == 0 && ($conn->ping() ?? false)) { // [72.1] Rollback only if connection is still alive.
        $conn->rollback();                                           // [73] UNDO all database changes to maintain data integrity.
    }
    ob_get_clean();                                                  // [73.1] Clean buffer on failure too.
    echo json_encode(['success' => false, 'message' => 'Reservation Aborted: ' . $e->getMessage()]); // [74] Emit failure detail.
}                                                                    // [75] Close the try-catch error handling architecture.

if (isset($conn) && $conn instanceof mysqli && $conn->connect_errno == 0) {
    $conn->close();                                                      // [76] Close the MySQL connection to release server overhead.
}
?>
