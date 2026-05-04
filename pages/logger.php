<?php
/**
 * LOGGER UTILITY (logger.php)
 * Purpose: Centralized function to record sensitive data mutations (Updates/Deletions).
 */

if (!function_exists('logActivity')) {
    function logActivity($userId, $userName, $type, $description) {
        global $conn; // Uses the existing database connection from the calling script.
        
        // Ensure connection is active
        if (!$conn) {
            require_once 'db_connection.php';
        }

        $time = date('H:i:s');
        $date = date('Y-m-d'); // MySQL standard date format (YYYY-MM-DD)

        $stmt = $conn->prepare("INSERT INTO Logs (user_id, type, description, name, time, date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $userId, $type, $description, $userName, $time, $date);
        
        if (!$stmt->execute()) {
            error_log("Logging Error: " . $stmt->error);
        }
        
        $stmt->close();
    }
}
?>
