<?php
// =================================================================
// OPERATION: ASSIGN DRIVER TO BUS
// =================================================================
// This script is used by Admins to pair a driver with a specific bus.

// Include the database connection.
require_once 'db_connection.php';

// ACTION: This runs when an Admin selects a driver for a bus in the dropdown.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // We need both the ID of the bus and the ID of the driver.
    $bus_id = $_POST['bus_id'];
    $driver_id = $_POST['driver_id'];

    // We use an UPDATE command to change the 'driver_id' column inside the 'buses' table.
    $stmt = $conn->prepare("UPDATE buses SET driver_id = ? WHERE bus_id = ?");
    // "ii" means we are sending 2 integers.
    $stmt->bind_param("ii", $driver_id, $bus_id);

    if ($stmt->execute()) {
        // Success!
        echo "Driver assigned successfully.";
    } else {
        // If something went wrong, show the database error.
        echo "Error: " . $conn->error;
    }
    // Clean up.
    $stmt->close();
}
?>
