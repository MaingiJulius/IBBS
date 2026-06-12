<?php
require_once 'db_connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $driver_id = $_POST['driver_id'];
    if (mysqli_query($conn, "DELETE FROM drivers WHERE driver_id = $driver_id")) {
        echo "Success: The driver record has been removed from the system.";
    } else {
// This is a safety feature of the database.
        echo "Error: Could not remove driver. They may still be assigned to an active bus.";
    }
}
?>