<?php
require_once 'db_connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from = $_POST['from_location'];
    $to = $_POST['to_location'];
    $date = $_POST['departure_date'];
    $time = $_POST['departure_time'];
    $cost = $_POST['cost'];
    $bus_id = $_POST['bus_id'];
    $sql = "INSERT INTO routes (from_location, to_location, departure_date, departure_time, cost, bus_id) VALUES ('$from', '$to', '$date', '$time', $cost, $bus_id)";
    if (mysqli_query($conn, $sql)) {
        echo "Route added successfully. ID: " . mysqli_insert_id($conn);
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>