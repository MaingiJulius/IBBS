<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Security Error: Unauthorized access attempt blocked.");
}
if (!isset($_POST['booking_id']) || !isset($_POST['passenger_id_number']) || !isset($_POST['passenger_age'])) {
    header("Location: dashboard.php?msg=Error: Missing required update parameters.");
    exit();
}
// [DATA CACHING & SANITIZATION]
$bid         = intval($_POST['booking_id']);              // Ensure ID is a strict integer.
$pass_name   = trim($_POST['passenger_name'] ?? '');
$id_number   = trim($_POST['passenger_id_number']);
$age         = intval($_POST['passenger_age']);           // Ensure age is a strict integer.
$redirect_to = $_POST['redirect_to'] ?? 'dashboard.php'; // Map return destination.
$role = $_SESSION['role'] ?? 'USER';
$current_user_id = $_SESSION['user_id'];
if ($role !== 'ADMIN' && $role !== 'AGENT') {
    $check_sql = "SELECT user_id FROM bookings WHERE booking_id = $bid";
    $res = mysqli_query($conn, $check_sql);
    $booking = mysqli_fetch_assoc($res);
    if (!$booking || $booking['user_id'] != $current_user_id) {
        die("Authorization Error: You do not have permission to modify this record.");
    }
}
$update_sql = "UPDATE bookings SET passenger_name = '$pass_name', passenger_id_number = '$id_number', passenger_age = $age WHERE booking_id = $bid";
if (mysqli_query($conn, $update_sql)) {
    $msg = "Passenger details updated successfully.";
} else {
    $msg = "Error: Failed to update passenger details. " . mysqli_error($conn);
}
// [RETURN TO SOURCE]
header("Location: $redirect_to?msg=" . urlencode($msg));
exit();
?>