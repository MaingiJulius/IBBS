<?php
session_start();
require_once 'db_connection.php';
require_once 'logger.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $route_id = $_POST['route_id'];
    $from = $_POST['from_location'];
// Geographic destination point location string.
    $to = $_POST['to_location'];
    $date = $_POST['departure_date'];
// The specific wall-clock time for fleet departure.
    $time = $_POST['departure_time'];
    $cost = $_POST['cost'];
    if (mysqli_query($conn, "UPDATE routes SET from_location=$from, to_location=$to, departure_date=$date, departure_time=$time, cost=$cost WHERE route_id=$route_id")) {
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Modified Travel Route (ID: $route_id)");
        echo "Success: The travel segment has been updated in the master registry.";
    } else {
        echo "System Fault: " . $conn->error;
    }
// --- [5] TEARDOWN & CLEANUP ---
}
?>