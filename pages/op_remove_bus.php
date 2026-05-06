<?php
/**
 * ADMINISTRATIVE OPERATION: REMOVE BUS (op_remove_bus.php)
 * Purpose: This script allows the admin to delete a bus from the fleet.
 * It is a simple backend script that receives the Bus ID and deletes it from MySQL.
 */

// [1] Include the database connection so we can talk to the 'buses' table.
require_once 'db_connection.php';

// [2] Check if the request is a POST submission (from a form).
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // [3] Grab the unique ID of the bus to be removed.
    $bus_id = $_POST['bus_id'];

    // [4] Prepare the SQL DELETE command to remove the bus record.
    // We use a prepared statement (?) to prevent SQL injection attacks.
    $stmt = $conn->prepare("DELETE FROM buses WHERE bus_id = ?");
    
    // [5] Bind the bus ID as an integer.
    $stmt->bind_param("i", $bus_id);

    // [6] Execute the command on the database.
    if ($stmt->execute()) {
        // If it works, send a success message back to the dashboard.
        echo "Success: The bus has been removed from the fleet records.";
    } else {
        // If it fails (e.g., if the bus is currently assigned to a route), show the error.
        echo "Error: Could not remove the bus. Detail: " . $conn->error;
    }
    
    // [7] Close the statement to save server resources.
    $stmt->close();
}
?>

