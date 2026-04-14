<?php
/**
 * OPERATION: PASSWORD RESET (op_reset_password.php)
 * Purpose: Securely resets a user's password to a system default (123456)
 * for academic demonstration and administrative recovery.
 */

require_once 'db_connection.php';
session_start();

// Security: Only ADMIN and AGENT can reset passwords
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    die("Unauthorized: Staff access required for credential resets.");
}

// Data Capture: Get target user ID from URL
if (isset($_GET['user_id'])) {
    $uid = $_GET['user_id'];
    
    // Default Password for Reset
    $new_pass = "123456";
    $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);

    // Prepare Update Statement
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $hashed_pass, $uid);

    if ($stmt->execute()) {
        $msg = "Success: Password for User ID " . $uid . " has been reset to '123456'.";
        header("Location: view_users_sorted.php?msg=" . urlencode($msg));
    } else {
        $err = "System Error: Could not reset password. " . $conn->error;
        header("Location: view_users_sorted.php?err=" . urlencode($err));
    }

    $stmt->close();
} else {
    header("Location: view_users_sorted.php?err=Invalid Request: No User ID specified.");
}
exit();
?>
