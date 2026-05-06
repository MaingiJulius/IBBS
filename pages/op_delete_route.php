<?php
/**
 * ADMINISTRATIVE OPERATION: DELETE ROUTE (op_delete_route.php)
 * Purpose: This backend script handles the permanent removal of a travel route.
 * It is called when an Admin clicks 'Delete' on the route management table.
 */

// [1] We start the session to identify the administrator performing the deletion.
session_start();

// [2] Import the database connection and the logging utility.
require_once 'db_connection.php';
require_once 'logger.php';

// [3] Security Check: Ensure the user is actually an Administrator.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    die("Unauthorized Access: Only Administrators can delete routes.");
}

// [4] Check if the request came via a 'POST' form submission.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // [5] Capture the Route ID that the admin wants to delete.
    $route_id = $_POST['route_id'];

    // [6] SQL Logic: Prepare a DELETE command to remove the specific route.
    // We use '?' (Prepared Statement) to ensure a hacker can't inject malicious code.
    $stmt = $conn->prepare("DELETE FROM routes WHERE route_id = ?");
    
    // [7] Bind the captured ID as an integer ('i').
    $stmt->bind_param("i", $route_id);

    // [8] Execute the command on the MySQL server.
    if ($stmt->execute()) {
        
        // [9] AUDIT LOG: If deletion was successful, we record it in our activity log.
        // This is important for accountability (we know WHO deleted the route).
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed Travel Route (ID: $route_id)");
        
        // [10] Success Response: Tell the admin it worked.
        echo "Success: The travel route has been permanently removed from the system.";
    } else {
        
        // [11] Error Handling: Deletion might fail if the database has 'Foreign Key' restrictions.
        // For example, you cannot delete a route if passengers have already booked tickets for it.
        echo "System Error: Cannot delete this route. Reason: " . $conn->error;
    }
    
    // [12] Close the statement to release resources.
    $stmt->close();
} else {
    // If someone tries to open this file directly without submitting a form, we show an error.
    echo "Invalid Request: This script only accepts POST data.";
}

// [13] Close the database connection.
$conn->close();
?>

