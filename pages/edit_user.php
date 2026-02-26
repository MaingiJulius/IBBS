<?php
/**
 * USER EDIT INTERFACE (edit_user.php)
 * Purpose: This page allows Staff (Admins and Agents) to modify existing system profiles.
 * It handles profile updates, role elevation/demotion, and password resets.
 */

// 1. DATA BRIDGE: Include the database configuration file.
require_once 'db_connection.php';

// 2. IDENTITY: Start session to identify the current operative.
session_start();

// --- SECURITY CHECK: STAFF AUTHORIZATION ---
// We restrict access to this sensitive management tool to 'ADMIN' or 'AGENT' roles only.
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    die("Access Denied: Staff Only Area.");
}

// --- ID VALIDATION ---
// We check if a 'user_id' was passed in the URL (GET parameter).
if (!isset($_GET['user_id'])) {
    die("Error: No user ID specified for modification.");
}

// Store the target user's ID in a variable.
$target_id = $_GET['user_id'];
$err = ""; // Placeholder for any database error messages.

// --- STEP 1: FETCH CURRENT DATA ---
// We need to pull the current profile data to pre-fill the form fields.
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $target_id); // Bind the ID as integer.
$stmt->execute(); // Execute the search.
$result = $stmt->get_result(); // Get the single row returned.
$user = $result->fetch_assoc(); // Associate the data into an array.
$stmt->close(); // Close the statement.

// If the ID doesn't exist in our table, stop.
if (!$user) {
    die("Error: The requested user profile was not found in our records.");
}

// --- STEP 2: HANDLE UPDATE SUBMISSION ---
// This block runs if the form is submitted via POST.
if (isset($_POST['update_user'])) {
    // Capture the modified input values.
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $role = $_POST['role'];
    
    // Capturing the 'optional' password field.
    $new_pass = $_POST['password'];
    
    // LOGIC: Conditional Update
    // We only update the password field if the staff member typed something in that box.
    if (!empty($new_pass)) {
        // SCENE A: Update WITH a new password hash.
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT); // Secure the new password.
        $sql = "UPDATE users SET first_name=?, last_name=?, email=?, phone_number=?, role=?, password=? WHERE user_id=?";
        $stmt_upd = $conn->prepare($sql);
        $stmt_upd->bind_param("ssssssi", $fname, $lname, $email, $phone, $role, $hashed, $target_id);
    } else {
        // SCENE B: Update everything EXCEPT the password.
        $sql = "UPDATE users SET first_name=?, last_name=?, email=?, phone_number=?, role=? WHERE user_id=?";
        $stmt_upd = $conn->prepare($sql);
        $stmt_upd->bind_param("sssssi", $fname, $lname, $email, $phone, $role, $target_id);
    }
    
    // EXECUTE: Try to save the changes to the database.
    if ($stmt_upd->execute()) {
        // If successful, redirect the staff member back to the main sorted user list.
        header("Location: view_users_sorted.php?msg=System: User profile updated successfully.");
        exit(); // Stop PHP processing.
    } else {
        // If it failed (e.g. duplicate email), capture the technical error.
        $err = "Critical Database Failure: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .form-group input, .form-group select { 
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; 
        }
    </style>
</head>
<body>
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <div class="form-container">
        <h2 style="color: var(--purple); text-align: center;">Edit User Details</h2>
        
        <?php if($err): ?>
            <p style="color: red; text-align: center;"><?= $err ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="PASSENGER" <?= ($user['role'] == 'PASSENGER') ? 'selected' : '' ?>>PASSENGER</option>
                    <option value="AGENT" <?= ($user['role'] == 'AGENT') ? 'selected' : '' ?>>AGENT</option>
                    <option value="ADMIN" <?= ($user['role'] == 'ADMIN') ? 'selected' : '' ?>>ADMIN</option>
                </select>
            </div>
            <div class="form-group">
                <label>New Password (Leave blank to keep current)</label>
                <input type="password" name="password" placeholder="Enter new password only if changing">
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" name="update_user" class="button regular-button pink-background" style="flex: 1;">Update User</button>
                <a href="view_users_sorted.php" class="button regular-button" style="background: #999; text-decoration: none; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>

    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
