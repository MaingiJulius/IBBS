<?php
require_once 'db_connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reg_no = $_POST['reg_no'];
    $bus_name = $_POST['bus_name'];
    $capacity = $_POST['capacity'];
    $seat_layout = $_POST['seat_layout'];
    $driver_id = !empty($_POST['driver_id']) ? $_POST['driver_id'] : NULL;
    $driver_val = $driver_id !== null ? $driver_id : "NULL";
    $sql = "INSERT INTO buses (reg_no, bus_name, max_passengers, seat_layout, driver_id) VALUES ('$reg_no', '$bus_name', $capacity, '$seat_layout', $driver_val)";
    if (mysqli_query($conn, $sql)) {
        echo "Bus added successfully. ID: " . mysqli_insert_id($conn);
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>