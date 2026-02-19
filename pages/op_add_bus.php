<?php
// =================================================================
// OPERATION: ADD BUS
// =================================================================
// Simulates a stored procedure to add a new bus.
// Usage: POST with reg_no, bus_name, capacity, seat_layout, driver_id
// =================================================================
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reg_no = $_POST['reg_no'];
    $bus_name = $_POST['bus_name'];
    $capacity = $_POST['capacity'];
    $seat_layout = $_POST['seat_layout'];
    $driver_id = !empty($_POST['driver_id']) ? $_POST['driver_id'] : NULL;

    $stmt = $conn->prepare("INSERT INTO buses (reg_no, bus_name, max_passengers, seat_layout, driver_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $reg_no, $bus_name, $capacity, $seat_layout, $driver_id);

    if ($stmt->execute()) {
        echo "Bus added successfully. ID: " . $stmt->insert_id;
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
?>
