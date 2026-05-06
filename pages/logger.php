<?php
/**
 * SYSTEM AUDIT LOGGER (logger.php)
 * Purpose: This script provides a central 'Black Box' for the system.
 * It records every important change (like deleting a user or cancelling a ticket)
 * so that administrators can see exactly who did what and when.
 */

// [1] We check if the 'logActivity' function already exists to avoid errors.
if (!function_exists('logActivity')) {
    
    /**
     * The LogActivity Function
     * Parameters:
     * - $userId: The ID of the person doing the action.
     * - $userName: Their human name.
     * - $type: The category (e.g., 'UPDATE', 'DELETION').
     * - $description: A short sentence explaining what happened.
     */
    function logActivity($userId, $userName, $type, $description) {
        
        // [2] We use 'global' to access the database connection from other scripts.
        global $conn; 
        
        // [3] Safety Check: If the database isn't connected yet, we connect it here.
        if (!$conn) {
            require_once 'db_connection.php';
        }

        // [4] Get the current time and date from the server.
        $time = date('H:i:s'); // Example: 14:30:05
        $date = date('Y-m-d'); // Example: 2026-05-06

        // [5] Prepare an SQL 'INSERT' command to save the log into the 'Logs' table.
        // We use '?' placeholders to keep the database safe from hacking.
        $stmt = $conn->prepare("INSERT INTO Logs (user_id, type, description, name, time, date) VALUES (?, ?, ?, ?, ?, ?)");
        
        // [6] 'isssss' means: 1 integer (i) followed by 5 strings (s).
        $stmt->bind_param("isssss", $userId, $type, $description, $userName, $time, $date);
        
        // [7] Execute the command. If it fails, we record the error in the server's private error log.
        if (!$stmt->execute()) {
            error_log("Logging Error: " . $stmt->error);
        }
        
        // [8] Clean up and close the statement to save memory.
        $stmt->close();
    }
}
?>

