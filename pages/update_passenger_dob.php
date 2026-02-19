<?php
// This script updates the Date of Birth (DOB) for a passenger and automatically calculates their age.

require_once 'db_connection.php';
session_start();

// SECURITY: Only Staff can access
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    die("Access Denied");
}

if (isset($_POST['booking_id']) && isset($_POST['dob'])) {
    $bid = $_POST['booking_id'];
    $dob = $_POST['dob']; // Format: YYYY-MM-DD
    
    // Calculate Age from DOB
    // We create two date objects: one for when they were born, one for today.
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    // The 'diff' function tells us the difference (years, months, days)
    $age = $birthDate->diff($today)->y; // We only need the 'y' (Years) part
    
    // Update the database with both the DOB and the calculated Age
    $sql = "UPDATE bookings SET passenger_dob = ?, passenger_age = ? WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $dob, $age, $bid);
    
    if ($stmt->execute()) {
        $msg = "DOB updated and Age set to $age.";
    } else {
        $msg = "Error updating database.";
    }
    $stmt->close();
    
    // Go back to the bookings list
    header("Location: view_admin_bookings.php?msg=" . urlencode($msg));
    exit();
} else {
    // If someone tries to open this file without sending data, kick them back.
    header("Location: view_admin_bookings.php");
    exit();
}
?>
