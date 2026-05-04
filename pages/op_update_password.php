<?php
/**
 * OP_UPDATE_PASSWORD.PHP
 * Purpose: Securely handles password update requests from the user profile page.
 */

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

    // [1] Integrity Verification: Ensure that the new password and confirmation match.
    if ($new !== $confirm) {
        header("Location: profile.php?status=error&error=New passwords do not match.");
        exit();
    }

    // [2] Complexity Requirement: Enforce a minimum length for security.
    if (strlen($new) < 6) {
        header("Location: profile.php?status=error&error=Password must be at least 6 characters.");
        exit();
    }

    // [3] Database Query: Fetch the user's current hashed credential for verification.
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id); // [4] Bind only the logged-in user's session identifier.
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // [5] Existence Check: Confirm the user account still exists in the system.
    if (!$user) {
        header("Location: profile.php?status=error&error=User not found.");
        exit();
    }

    // [6] Cryptographic Validation: Use password_verify to check the raw entry against the DB hash.
    if (!password_verify($current, $user['password'])) {
        header("Location: profile.php?status=error&error=Incorrect current password.");
        exit();
    }

    // [7] Credential Transformation: Generate a fresh BCrypt hash for the new password.
    $new_hashed = password_hash($new, PASSWORD_DEFAULT);
    // [8] Persistence: Write the new securely hashed password to the database record.
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_hashed, $user_id);
    
    if ($stmt->execute()) {
        // [AUDIT LOG] Record the self-service password update.
        logActivity($user_id, $_SESSION['name'], 'UPDATE', "Self-service password change successful.");

        // [9] Feedback: Redirect with success status on completion.
        header("Location: profile.php?status=success");
    } else {
        // [10] Error Handling: Report database persistence failures.
        header("Location: profile.php?status=error&error=Database update failed.");
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: profile.php");
}
?>
