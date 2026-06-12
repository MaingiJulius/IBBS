<?php
session_start();
require_once 'db_connection.php';
require_once 'logger.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    die("Unauthorized Access: Only Administrators can delete routes.");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $route_id = $_POST['route_id'];
    $sql="DELETE FROM routes WHERE route_id=$route_id";
    if (mysqli_query($conn,$sql)) {
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed Travel Route (ID: $route_id)");
        echo "Success: The travel route has been permanently removed from the system.";
    } else {
        echo "System Error: Cannot delete this route. Reason: " . $conn->error;
    }
} else {
    echo "Invalid Request: This script only accepts POST data.";
}
?>