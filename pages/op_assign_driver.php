<?php
require_once 'db_connection.php';
// ACTION: This runs when an Admin selects a driver for a bus in the dropdown.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_id = $_POST['bus_id'];
    $driver_id = $_POST['driver_id'];
    if (mysqli_query($conn, "UPDATE buses SET driver_id = $driver_id WHERE bus_id = $bus_id")) {
// Success!
        echo "Driver assigned successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
// Clean up.
}
?>