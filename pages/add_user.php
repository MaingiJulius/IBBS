<?php
session_start();
require_once 'db_connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Security Error: Access denied. Authorized Admins only.");
}
if (isset($_POST['add_user'])) {
    $fname    = $_POST['first_name'];
    $lname    = $_POST['last_name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $role = 'user';
// from injecting malicious code because the machine already knows the
    $stmt->bind_param("sssss", $fname, $lname, $email, $password, $role);
    $stmt->execute();
    header('Location: admin_users.php');
    exit();
}
?>
<!DOCTYPE html>
> (greater than sign) standard modern web document definition. -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manually Add User - Wema Travellers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    to fit on mobile screens. -->
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0; }
        .container { max-width: 400px; margin: 80px auto; padding: 30px; background-color: white; box-shadow: 0px 5px 15px rgba(0,0,0,0.1); border-radius: 10px; }
        h2 { text-align: center; margin-bottom: 25px; color: #333; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 12px; margin-bottom: 18px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #4CAF50; border: none; color: white; font-size: 1rem; font-weight: bold; border-radius: 6px; cursor: pointer; transition: background 0.2s; }
        button:hover { background-color: #388e3c; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #666; text-decoration: none; font-size: 0.9rem; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'admin') ?>-role">
<script src="js/header2.js"></script>
<div style="height: 100px;"></div>
<div class="container">
> (greater than sign) is the centered form wrapper. -->
    <h2>Add New User (Admin)</h2>
    <form method="POST" id="addUserForm" onsubmit="return validateForm()">
        <input type="text" name="first_name" id="first_name" placeholder="First Name" onmouseout="validateFirstName()">
        <input type="text" name="last_name" id="last_name" placeholder="Last Name" onmouseout="validateLastName()">
        <input type="text" name="email" id="email" placeholder="Email Address" onmouseout="validateEmail()">
        <input type="text" name="password" id="password" placeholder="Temporary Password" onmouseout="validatePassword()">
        <button type="submit" name="add_user">Create User Account</button>
    </form>
    <script>
        function validateFirstName() {
            var val = document.getElementById("first_name").value;
            if (val.length == 0) { alert("First Name is required."); document.getElementById("first_name").focus(); return false; }
            return true;
        }
        function validateLastName() {
            var val = document.getElementById("last_name").value;
            if (val.length == 0) { alert("Last Name is required."); document.getElementById("last_name").focus(); return false; }
            return true;
        }
        function validateEmail() {
            var email = document.getElementById("email").value;
            if (email.length == 0 || email.indexOf("@") == -1 || email.indexOf(".") == -1) { alert("Please enter a valid email address."); document.getElementById("email").focus(); return false; }
            return true;
        }
        function validatePassword() {
            var val = document.getElementById("password").value;
            if (val.length == 0) { alert("Please provide a temporary password."); document.getElementById("password").focus(); return false; }
            return true;
        }
        function validateForm() {
            var rtned = true;
            rtned = validateFirstName();
            if (rtned == true) rtned = validateLastName();
            if (rtned == true) rtned = validateEmail();
            if (rtned == true) rtned = validatePassword();
            return rtned;
        }
    </script>
    <a href="admin_users.php" class="back-link">← Cancel & Back to User List</a>
</div>
</body>
</html>