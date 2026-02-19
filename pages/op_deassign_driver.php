<?php
// =================================================================
// OPERATION: DE-ASSIGN DRIVER FROM BUS
// =================================================================
// This script is used by Admins to remove a driver from a bus (making the bus "driverless").

// Include the database connection.
require_once 'db_connection.php';

// ACTION: This runs when an Admin clicks 'Remove Driver' in the bus manager.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_id = $_POST['bus_id'];

    // We set the 'driver_id' column to NULL (which means "nothing" or "empty").
    // By doing this, we keep the bus and the driver records, but they aren't paired anymore.
    $stmt = $conn->prepare("UPDATE buses SET driver_id = NULL WHERE bus_id = ?");
    $stmt->bind_param("i", $bus_id);

    if ($stmt->execute()) {
        // Success!
        echo "Driver de-assigned successfully.";
    } else {
        // Error!
        echo "Error: " . $conn->error;
    }
    // Clean up.
    $stmt->close();
}
?>
