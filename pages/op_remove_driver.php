<?php
// =================================================================
// OPERATION: REMOVE DRIVER
// =================================================================
// This script is used by Admins to delete a driver from the system.

// Include the database connection.
require_once 'db_connection.php';

// ACTION: This runs when an Admin clicks 'Delete' on a driver.
// It receives the 'driver_id' via a POST request.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $driver_id = $_POST['driver_id'];

    // We use a DELETE command in SQL.
    // NOTE: If this driver is currently assigned to a bus, this will fail for safety.
    $stmt = $conn->prepare("DELETE FROM drivers WHERE driver_id = ?");
    $stmt->bind_param("i", $driver_id);

    if ($stmt->execute()) {
        // If it worked, send a success message.
        echo "Driver removed successfully.";
    } else {
        // If it failed (usually because they are assigned to a bus), send an error.
        echo "Error: Cannot remove driver. They might be assigned to a bus.";
    }
    // Clean up.
    $stmt->close();
}
?>
