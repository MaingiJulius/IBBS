<?php
/**
 * ADD_USER.PHP
 * Purpose: This is an internal tool for Administrators to manually register new users.
 * Note: This is an older "Legacy" file. In the current system, we prefer using 
 * 'view_users_sorted.php' for managing the user database.
 */

// --- STEP 1: SESSION MANAGEMENT ---
// session_start() allows us to access the $_SESSION array to identify the logged-in Admin.
session_start();

// Include the database bridge to allow SQL operations.
require_once 'db_connection.php';

/**
 * --- STEP 2: SECURITY CHECK ---
 * We must ensure that only authorized 'admin' accounts can use this manual entry tool.
 * If the user isn't logged in (!isset($_SESSION['user_id'])) OR they are not an 'admin',
 * we block access immediately to prevent unauthorized user creation.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // die() terminates the script and prints a warning.
    die("Security Error: Access denied. Authorized Admins only.");
}

/**
 * --- STEP 3: DATA SUBMISSION (POST) ---
 * This block executes only when the "Add User" button is clicked in the form.
 */
if (isset($_POST['add_user'])) {
    
    // --- STEP 4: COLLECT USER DETAILS ---
    // We grab the text entered into the HTML input boxes.
    $fname    = $_POST['first_name']; // Passenger's First Name
    $lname    = $_POST['last_name'];  // Passenger's Last Name
    $email    = $_POST['email'];      // Passenger's Login Email
    
    /**
     * SECURITY NOTE: In this legacy file, the password is saved exactly as typed.
     * In modern files like account2.php, we always use password_hash() for safety.
     */
    $password = $_POST['password']; 
    
    // By default, manual additions through this specific tool are assigned the 'user' (Passenger) role.
    $role = 'user'; 

    /**
     * --- STEP 5: SAVE TO DATABASE ---
     * We prepare a SQL command to INSERT the data into the 'users' table.
     * We use placeholders (?) to protect against SQL Injection.
     */
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    
    // "sssss" tells PHP we are providing 5 text fragments (strings).
    $stmt->bind_param("sssss", $fname, $lname, $email, $password, $role);
    
    // Execute the insertion.
    $stmt->execute();
    
    // Close the statement tool.
    $stmt->close();

    // --- STEP 6: REDIRECT ---
    // After the user is saved, send the Admin back to the main user management list.
    header('Location: admin_users.php');
    exit(); // Stop current script for redirect.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manually Add User - Wema Travellers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <style>
        /* CSS for the Manual Entry Form */
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f7f7f7; /* Soft grey background */
            margin: 0;
            padding: 0;
        }

        /* Centered form container */
        .container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background-color: white;
            box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        /* Styling for the text boxes */
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box; /* Fixes width issue with padding */
        }

        /* Green submit button */
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            color: white;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }

        button:hover {
            background-color: #388e3c;
        }

        /* Navigation link at the bottom */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'admin') ?>-role">
<script src="js/header2.js"></script>
<div style="height: 100px;"></div>

<div class="container">
    <h2>Add New User (Admin)</h2>
    
    <!-- Signup Form -->
    <form method="POST" id="addUserForm" onsubmit="return validateForm()">
        <!-- Input fields with manual JS validation -->
        <input type="text" name="first_name" id="first_name" placeholder="First Name" onmouseout="validateFirstName()">
        <input type="text" name="last_name" id="last_name" placeholder="Last Name" onmouseout="validateLastName()">
        <input type="text" name="email" id="email" placeholder="Email Address" onmouseout="validateEmail()">
        <input type="text" name="password" id="password" placeholder="Temporary Password" onmouseout="validatePassword()">
        
        <!-- Submission Switch -->
        <button type="submit" name="add_user">Create User Account</button>
    </form>

    <script>
        // Custom JavaScript validation for Admin Add User Form
        // This function validates the first name
        function validateFirstName() {
            var val = document.getElementById("first_name").value;
            if (val.length == 0) {
                alert("First Name is required.");
                document.getElementById("first_name").focus();
                return false;
            }
            return true;
        }

        // This function validates the last name
        function validateLastName() {
            var val = document.getElementById("last_name").value;
            if (val.length == 0) {
                alert("Last Name is required.");
                document.getElementById("last_name").focus();
                return false;
            }
            return true;
        }

        // This function validates the email format
        function validateEmail() {
            var email = document.getElementById("email").value;
            if (email.length == 0 || email.indexOf("@") == -1 || email.indexOf(".") == -1) {
                alert("Please enter a valid email address.");
                document.getElementById("email").focus();
                return false;
            }
            return true;
        }

        // This function validates the password
        function validatePassword() {
            var val = document.getElementById("password").value;
            if (val.length == 0) {
                alert("Please provide a temporary password.");
                document.getElementById("password").focus();
                return false;
            }
            return true;
        }

        // Main caller function for form submission
        function validateForm() {
            var rtned = true;
            rtned = validateFirstName();
            if (rtned == true) rtned = validateLastName();
            if (rtned == true) rtned = validateEmail();
            if (rtned == true) rtned = validatePassword();
            return rtned; // Block submission if any check fails
        }
    </script>

    
    <!-- Back to management list -->
    <a href="admin_users.php" class="back-link">← Cancel & Back to User List</a>
</div>

</body>
</html>
