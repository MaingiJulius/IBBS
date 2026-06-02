<?php
/**
 * =================================================================
 * OPERATION: RETROACTIVE PASSENGER DATA REPAIR (op_update_passenger_details.php)
 * =================================================================
 * Purpose: Allows travelers and administrative staff to fill in 
 * missing identity documents and age metrics for legacy bookings.
 * =================================================================
 */

require_once 'db_connection.php';             // [1] Import DB bridge handle ($conn).
session_start();                               // [2] Initialize session to identify user/staff.

// [AUTHENTICATION CHECK]
// Ensure a guest cannot trigger this script.
if (!isset($_SESSION['user_id'])) {
    die("Security Error: Unauthorized access attempt blocked.");
}

// [INPUT VALIDATION]
// Check if the four core pieces of data are present: Booking ID, Name, Passenger ID, and Age.
if (!isset($_POST['booking_id']) || !isset($_POST['passenger_id_number']) || !isset($_POST['passenger_age'])) {
    header("Location: dashboard.php?msg=Error: Missing required update parameters.");
    exit();
}

// [DATA CACHING & SANITIZATION]
$bid         = intval($_POST['booking_id']);              // Ensure ID is a strict integer.
$pass_name   = trim($_POST['passenger_name'] ?? '');      // Capture passenger full name.
$id_number   = trim($_POST['passenger_id_number']);       // Remove accidental whitespace.
$age         = intval($_POST['passenger_age']);           // Ensure age is a strict integer.
$redirect_to = $_POST['redirect_to'] ?? 'dashboard.php'; // Map return destination.

// [AUTHORIZATION & OWNERSHIP VERIFICATION]
$role = $_SESSION['role'] ?? 'USER';           // Identify the active user's permissions.
$current_user_id = $_SESSION['user_id'];       // Identify the active user's index.

// STAFF BYPASS: Admins and Agents are authorized to modify any ticket in the system.
if ($role !== 'ADMIN' && $role !== 'AGENT') {
    // PASSENGER LOCK: For standard users, we must verify they actually own the booking.
    $check_sql = "SELECT user_id FROM bookings WHERE booking_id = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("i", $bid);
    $stmt_check->execute();
    $res = $stmt_check->get_result();
    $booking = $res->fetch_assoc();
    
    // SECURITY TERMINATION: If ownership doesn't match, stop execution.
    if (!$booking || $booking['user_id'] != $current_user_id) {
        die("Authorization Error: You do not have permission to modify this record.");
    }
    $stmt_check->close();
}

// [DATABASE PERSISTENCE]
// Formulate and execute the update command for all 3 traveler details: name, age, and ID/passport.
$update_sql = "UPDATE bookings SET passenger_name = ?, passenger_id_number = ?, passenger_age = ? WHERE booking_id = ?";
$stmt_update = $conn->prepare($update_sql);
$stmt_update->bind_param("ssii", $pass_name, $id_number, $age, $bid);

if ($stmt_update->execute()) {
    $msg = "Passenger details updated successfully.";
} else {
    $msg = "Error: Failed to update passenger details. " . $conn->error;
}

$stmt_update->close();                         // Cleanup statement object.
$conn->close();                                // Close database connection.

// [RETURN TO SOURCE]
// Return the user to the page they came from with a success/failure feedback message.
header("Location: $redirect_to?msg=" . urlencode($msg));
exit();
?>
