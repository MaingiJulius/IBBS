<?php
// This page allows Staff (Admins/Agents) to edit an existing user's details.

// Include DB connection
require_once 'db_connection.php';
session_start();

// SECURITY CHECK: staff only
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    die("Access Denied: Staff Only Area.");
}

// Check if an ID is provided
if (!isset($_GET['user_id'])) {
    die("Error: No user ID specified.");
}

$target_id = $_GET['user_id'];
$msg = "";
$err = "";

// --- FETCH USER DATA ---
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $target_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("Error: User not found.");
}

// --- HANDLE FORM SUBMISSION ---
if (isset($_POST['update_user'])) {
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $role = $_POST['role'];
    
    // Optional password update
    $new_pass = $_POST['password'];
    
    if (!empty($new_pass)) {
        // Update WITH password
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET first_name=?, last_name=?, email=?, phone_number=?, role=?, password=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $fname, $lname, $email, $phone, $role, $hashed, $target_id);
    } else {
        // Update WITHOUT password
        $sql = "UPDATE users SET first_name=?, last_name=?, email=?, phone_number=?, role=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssyi", $fname, $lname, $email, $phone, $role, $target_id);
    }
    
    if ($stmt->execute()) {
        // Redirect back to the list with success message
        header("Location: view_users_sorted.php?msg=User details updated successfully.");
        exit();
    } else {
        $err = "Database Error: " . $conn->error;
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
