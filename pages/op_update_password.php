<?php
require_once 'db_connection.php';
require_once 'logger.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    if ($new !== $confirm) {
        header("Location: profile.php?status=error&error=New passwords do not match.");
        exit();
    }
    if (strlen($new) < 6) {
        header("Location: profile.php?status=error&error=Password must be at least 6 characters.");
        exit();
    }
    $result = mysqli_query($conn, "SELECT password FROM users WHERE user_id = $user_id");
    $user = mysqli_fetch_assoc($result);
    if (!$user) {
        header("Location: profile.php?status=error&error=User not found.");
        exit();
    }
    if (!password_verify($current, $user['password'])) {
        header("Location: profile.php?status=error&error=Incorrect current password.");
        exit();
    }
    $new_hashed = password_hash($new, PASSWORD_DEFAULT);
    if (mysqli_query($conn, "UPDATE users SET password = $new_hashed WHERE user_id = $user_id")) {
        logActivity($user_id, $_SESSION['name'], 'UPDATE', "Self-service password change successful.");
        header("Location: profile.php?status=success");
    } else {
        header("Location: profile.php?status=error&error=Database update failed.");
    }
} else {
    header("Location: profile.php");
}
?>