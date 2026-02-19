<?php
// =================================================================
// OPERATION: DELETE ROUTE
// =================================================================
// This script is used by staff to remove a travel schedule from the database.

// Include the database connection.
require_once 'db_connection.php';

// ACTION: This runs when staff clicks 'Delete' on a route.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $route_id = $_POST['route_id'];

    // We use a DELETE command in SQL to wipe out this route record.
    // NOTE: This will fail if there are already bookings made for this route (Safety first!).
    $stmt = $conn->prepare("DELETE FROM routes WHERE route_id = ?");
    $stmt->bind_param("i", $route_id);

    if ($stmt->execute()) {
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
