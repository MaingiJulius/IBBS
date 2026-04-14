<?php
/**
 * OP_UPDATE_PASSWORD.PHP
 * Purpose: Securely handles password update requests from the user profile page.
 */

require_once 'db_connection.php';
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

    // 1. Basic validation
    if ($new !== $confirm) {
        header("Location: profile.php?status=error&error=New passwords do not match.");
        exit();
    }

    if (strlen($new) < 6) {
        header("Location: profile.php?status=error&error=Password must be at least 6 characters.");
        exit();
    }

    // 2. Fetch current hashed password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        header("Location: profile.php?status=error&error=User not found.");
        exit();
    }

    // 3. Verify current password
    // Note: Assuming the system uses password_hash() for security.
    if (!password_verify($current, $user['password'])) {
        header("Location: profile.php?status=error&error=Incorrect current password.");
        exit();
    }

    // 4. Update password
    $new_hashed = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_hashed, $user_id);
    
    if ($stmt->execute()) {
        header("Location: profile.php?status=success");
    } else {
        header("Location: profile.php?status=error&error=Database update failed.");
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: profile.php");
}
?>
