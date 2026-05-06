<?php
/**
 * ADMINISTRATIVE OPERATION: REMOVE DRIVER (op_remove_driver.php)
 * Purpose: This script allows the admin to delete a driver from the system.
 * It ensures the driver is removed from the database based on their ID.
 */

// [1] Include the database connection so we can talk to the 'drivers' table.
require_once 'db_connection.php';

// [2] Check if the request is a POST submission (from a management form).
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // [3] Capture the unique ID of the driver that needs to be deleted.
    $driver_id = $_POST['driver_id'];

    // [4] Prepare the SQL DELETE command.
    // Using a '?' (Prepared Statement) is a key security feature to prevent hacking.
    $stmt = $conn->prepare("DELETE FROM drivers WHERE driver_id = ?");
    
    // [5] Bind the captured driver ID as an integer ('i').
    $stmt->bind_param("i", $driver_id);

    // [6] Run the command.
    if ($stmt->execute()) {
        // SUCCESS: The driver was deleted. Show a confirmation message.
        echo "Success: The driver record has been removed from the system.";
    } else {
        // FAIL: Usually happens if the driver is 'In Use' (e.g., assigned to a bus).
        // This is a safety feature of the database.
        echo "Error: Could not remove driver. They may still be assigned to an active bus.";
    }
    
    // [7] Close the statement and clean up server resources.
    $stmt->close();
}
?>

