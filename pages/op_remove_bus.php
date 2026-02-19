<?php
// =================================================================
// OPERATION: REMOVE BUS
// =================================================================
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_id = $_POST['bus_id'];

    $stmt = $conn->prepare("DELETE FROM buses WHERE bus_id = ?");
    $stmt->bind_param("i", $bus_id);

    if ($stmt->execute()) {
        echo "Bus removed successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
?>
