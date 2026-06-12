<?php
require_once 'db_connection.php';
require_once 'logger.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];
if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
// --- [5] OWNERSHIP DEFENSE ENGINE ---
    $sql = "UPDATE bookings SET booking_status = 'CANCELLED' WHERE booking_id = $booking_id AND user_id = $user_id";
    if (mysqli_query($conn, $sql)) {
        if (mysqli_affected_rows($conn) > 0) {
            logActivity($user_id, $_SESSION['name'], 'UPDATE', "User-initiated Ticket Cancellation (BID: $booking_id)");
            $msg = "Success: Your travel ticket has been voided successfully.";
        } else {
            $msg = "Security Error: Ticket not found or access denied for this record.";
        }
    } else {
        $msg = "System Fault: " . $conn->error;
    }
} else {
    $msg = "Protocol Error: No booking identifier was provided for the operation.";
}
header("Location: view_user_history.php?msg=" . urlencode($msg));
exit();
?>