<?php
// =================================================================
// OPERATION: EDIT ROUTE
// =================================================================
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $route_id = $_POST['route_id'];
    $from = $_POST['from_location'];
    $to = $_POST['to_location'];
    $date = $_POST['departure_date'];
    $time = $_POST['departure_time'];
    $cost = $_POST['cost'];

    $stmt = $conn->prepare("UPDATE routes SET from_location=?, to_location=?, departure_date=?, departure_time=?, cost=? WHERE route_id=?");
    $stmt->bind_param("ssssdi", $from, $to, $date, $time, $cost, $route_id);

    if ($stmt->execute()) {
        echo "Route updated successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
?>
