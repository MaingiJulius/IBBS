<?php
// =================================================================
// OPERATION: ADD ROUTE
// =================================================================
// Simulates a stored procedure to add a new route.
// =================================================================
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from = $_POST['from_location'];
    $to = $_POST['to_location'];
    $date = $_POST['departure_date'];
    $time = $_POST['departure_time'];
    $cost = $_POST['cost']; // In KES
    $bus_id = $_POST['bus_id'];

    $stmt = $conn->prepare("INSERT INTO routes (from_location, to_location, departure_date, departure_time, cost, bus_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdi", $from, $to, $date, $time, $cost, $bus_id);

    if ($stmt->execute()) {
        echo "Route added successfully. ID: " . $stmt->insert_id;
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
?>
