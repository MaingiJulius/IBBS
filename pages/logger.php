<?php
if (!function_exists('logActivity')) {
    function logActivity($userId, $userName, $type, $description) {
        global $conn;
        if (!$conn) {
            require_once 'db_connection.php';
        }
        $time = date('H:i:s');
        $date = date('Y-m-d');
        $sql = "INSERT INTO Logs (user_id, type, description, name, time, date) VALUES (?, ?, ?, ?, ?, ?)";
        mysqli_stmt_bind_param($stmt, "isssss", $userId, $type, $description, $userName, $time, $date);
        mysqli_query($conn,$sql);
    }
}
?>