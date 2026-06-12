<?php
require_once 'db_connection.php';
require_once 'logger.php';
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    die("Unauthorized: Staff access required for credential resets.");
}
if (isset($_GET['user_id'])) {
    $uid = $_GET['user_id'];
    $new_pass = "123456";
    $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
    $sql="UPDATE users SET password='$hashed_pass' WHERE user_id=$uid";
    if (mysqli_query($conn,$sql)) {
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Reset Password for User ID $uid to default");
        $msg = "Success: Password for User ID " . $uid . " has been reset to '123456'.";
        header("Location: view_users_sorted.php?msg=" . urlencode($msg));
    } else {
        $err = "System Error: Could not reset password. " . $conn->error;
        header("Location: view_users_sorted.php?err=" . urlencode($err));
    }
} else {
    header("Location: view_users_sorted.php?err=Invalid Request: No User ID specified.");
}
exit();
?>