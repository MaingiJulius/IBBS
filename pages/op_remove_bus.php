<?php
require_once 'db_connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_id = $_POST['bus_id'];
    if (mysqli_query($conn, "DELETE FROM buses WHERE bus_id = $bus_id")) {
        echo "Success: The bus has been removed from the fleet records.";
    } else {
        echo "Error: Could not remove the bus. Detail: " . $conn->error;
    }
}
?>