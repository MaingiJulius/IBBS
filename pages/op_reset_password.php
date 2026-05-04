<?php
/**
 * OPERATION: PASSWORD RESET (op_reset_password.php)
 * Purpose: Securely resets a user's password to a system default (123456)
 * for academic demonstration and administrative recovery.
 */

require_once 'db_connection.php';
require_once 'logger.php';
session_start();

// Security: Only ADMIN and AGENT can reset passwords
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    die("Unauthorized: Staff access required for credential resets.");
}

// [1] Data Capture: Get the target user ID from the URL parameter.
if (isset($_GET['user_id'])) {
    $uid = $_GET['user_id'];
    
    // [2] Define the global recovery password for demonstrative resets.
    $new_pass = "123456";
    // [3] Securely hash the password using PHP's native BCrypt algorithm.
    $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);

    // [4] Prepare the SQL statement to update the password column for the specific user.
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $hashed_pass, $uid); // [5] Bind the hashed string and integer ID.

    if ($stmt->execute()) {
        // [AUDIT LOG] Record the modification.
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Reset Password for User ID $uid to default");
        // [6] Format success message and redirect back with feedback.
        $msg = "Success: Password for User ID " . $uid . " has been reset to '123456'.";
        header("Location: view_users_sorted.php?msg=" . urlencode($msg));
    } else {
        // [7] Handle potential database failures or connection loss.
        $err = "System Error: Could not reset password. " . $conn->error;
        header("Location: view_users_sorted.php?err=" . urlencode($err));
    }

    $stmt->close(); // [8] Close the prepared statement resource.
} else {
    // [9] Security Boundary: Reject the request if no user identifier is provided.
    header("Location: view_users_sorted.php?err=Invalid Request: No User ID specified.");
}
exit();
?>
