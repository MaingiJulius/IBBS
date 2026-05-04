<?php
// =================================================================
// OPERATION: DELETE ROUTE
// =================================================================
// This script is used by staff to remove a travel schedule from the database.

// Include the database connection.
session_start();
require_once 'db_connection.php';
require_once 'logger.php';

// ACTION: This runs when staff clicks 'Delete' on a route.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $route_id = $_POST['route_id'];

    // We use a DELETE command in SQL to wipe out this route record.
    // NOTE: This will fail if there are already bookings made for this route (Safety first!).
    $stmt = $conn->prepare("DELETE FROM routes WHERE route_id = ?");
    $stmt->bind_param("i", $route_id);

    if ($stmt->execute()) {
        // [AUDIT LOG] Record the operation.
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed Travel Route (ID: $route_id)");
        // Success!
        echo "Route deleted successfully.";
    } else {
        // Error! (Usually because people have booked seats on this bus already).
        echo "Error: Cannot delete route. It might have active bookings.";
    }
    // Clean up.
    $stmt->close();
}
?>
