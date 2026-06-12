<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    die("Access Denied: High-Level Personnel Authentication Required for Data Modification.");
}
if (isset($_POST['booking_id'])) {
    $bid = $_POST['booking_id'];        // The unique database record index.
    $dob = $_POST['dob'] ?? '';         // The date string provided.
    $national_id = $_POST['national_id'] ?? ''; // The ID number provided.
// Initialize segments for the dynamic query.
    $update_parts = [];
    $params = [];
    $types = "";
    if (!empty($dob)) {
        $birthDate = new DateTime($dob);
        $today = new DateTime('today');
        $age = $birthDate->diff($today)->y;
        $update_parts[] = "passenger_dob = '$dob', passenger_age = $age";
    }
    if (!empty($national_id)) {
        $update_parts[] = "passenger_id_number = '$national_id'";
    }
    if (empty($update_parts)) {
        header("Location: view_admin_bookings.php?msg=" . urlencode("No data provided for update."));
        exit();
    }
    $sql = "UPDATE bookings SET " . implode(", ", $update_parts) . " WHERE booking_id = $bid";
    if (mysqli_query($conn, $sql)) {
        $msg = "Success: Passenger dossier updated.";
        if (!empty($dob)) $msg .= " Calculated Age: " . $age . " years.";
    } else {
        $msg = "System Fault: The database record could not be updated. " . $conn->error;
    }
    header("Location: view_admin_bookings.php?msg=" . urlencode($msg));
    exit();
} else {
// --- [8] PROTOCOL BLOCKER ---
    header("Location: view_admin_bookings.php");
// HALT.
    exit();
}
?>