<?php
/**
 * ACCOUNT2.PHP
 * Purpose: This is the main "Account Engine" script for Wema Travellers.
 * It handles two critical security-related jobs:
 * 1. SIGNING UP: Creating new passenger accounts with validation and hashing.
 * 2. LOGGING IN: Verifying credentials and setting up user sessions for site access.
 */

// --- STEP 1: CONFIGURATION ---
// error_reporting(E_ALL) tells PHP to show absolutely every mistake or hint.
// This is vital for debugging as it prevents bugs from hiding in the background.
error_reporting(E_ALL);

// ini_set('display_errors', '1') ensures that these errors are printed directly 
// onto the webpage so devs can see them instantly.
ini_set('display_errors', '1');

/**
 * session_start() initializes the PHP session mechanism.
 * Think of it as opening a "User Notebook" stored on the server.
 * This notebook follows the user across every page (Home, Profile, etc.) until they log out.
 */
session_start();

// We include the database connection file.
// Without this, the script cannot communicate with MySQL to find or save users.
require_once 'db_connection.php';

// =================================================================
// PART 1: SIGNING UP (CREATE A NEW ACCOUNT)
// =================================================================

/**
 * We check if the 'save' button from the signup form has been clicked.
 * The signup form uses method="POST", so the data arrives in the $_POST array.
 */
if (isset($_POST['save'])) {
    
    // --- STEP 2: DATA COLLECTION AND CLEANUP (SANITIZATION) ---
    // trim() is used to remove accidental spaces typed by the user at the start/end.
    // This prevents errors like " user@email.com" failing login later.
    $first_name   = trim($_POST['first_name']); // User's given name
    $last_name    = trim($_POST['last_name']);  // User's family name
    $email        = trim($_POST['email']);      // User's login email
    $phone_number = trim($_POST['phone_number']); // User's contact number
    $password_raw = trim($_POST['password']);    // The literal password (not yet secure)

    // --- STEP 3: SECURITY CHECK (PASSWORD STRENGTH VALIDATION) ---
    /**
     * This complex pattern (Regex) ensures the user creates a strong password.
     * It requires:
     * - (?=.*[a-z]): At least one lowercase letter
     * - (?=.*[A-Z]): At least one uppercase letter
     * - (?=.*\d): At least one digit (number)
     * - (?=.*[!@#$%^&*()...]): At least one special symbol
     * - .{8,}: Minimum length of 8 characters
     */
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()\-_=+{};:,<.>]).{8,}$/', $password_raw)) {
        // If the password is weak, we stop the registration and show an instruction.
        die("Security Error: Password must contain at least one uppercase, one lowercase, one digit, one special character, and be at least 8 characters long.");
    }

    // --- STEP 4: EMAIL FORMAT VALIDATION ---
    // filter_var checks if the email is structured correctly (e.g., has an @ and a domain).
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Input Error: Invalid email address format.");
    }

    // --- STEP 5: DOMAIN RESTRICTION (BUSINESS LOGIC) ---
    // To minimize spam, we only allow common/trusted email providers in this prototype.
    $allowed_domains = ["gmail.com", "yahoo.com", "hotmail.com"];
    
    // explode("@", $email) splits "test@gmail.com" into ["test", "gmail.com"].
    // [1] selects the second part (the domain).
    $email_domain = explode("@", $email)[1]; 

    // in_array checks if the grabbed domain exists in our "allowed" list.
    if (!in_array($email_domain, $allowed_domains)) {
        die("Access Denied: Email domain not allowed. Only Gmail, Yahoo, and Hotmail accepted.");
    }

    // --- STEP 6: DUPLICATION CHECK (PREVENTING DOUBLE ACCOUNTS) ---
    // We must ensure the Email and Phone number aren't already used by someone else.
    // We use a prepared statement (?) to keep our database safe from hackers.
    $stmt_check = $conn->prepare("SELECT email FROM users WHERE email = ? OR phone_number = ?");
    
    // Bind the email and phone as two strings ("ss").
    $stmt_check->bind_param("ss", $email, $phone_number);
    
    // Run the search.
    $stmt_check->execute();
    
    // Load the matching rows into memory.
    $stmt_check->store_result();
    
    // if num_rows > 0, we found a match, meaning the user already exists.
    if ($stmt_check->num_rows > 0) {
        $stmt_check->close(); // Close the search to free resources.
        die("Registration Error: User with this email or phone number already exists!");
    }
    $stmt_check->close();

    // --- STEP 7: PASSWORD HASHING (ENCRYPTION) ---
    /**
     * We NEVER store real passwords. If the site is hacked, real passwords are dangerous.
     * password_hash converts "MyPass1!" into a long random code like "$2y$10$Abc...".
     * This code is impossible to "reverse" back into the real password.
     */
    $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);
    
    // Public registrations are always assigned the 'PASSENGER' role by default.
    $role = "PASSENGER"; 

    // --- STEP 8: FINAL STORAGE (SAVING THE ACCOUNT) ---
    // We insert all the verified and secured data into the 'users' table.
    $stmt_insert = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    
    // Bind the 6 strings ("ssssss") to the placeholders.
    $stmt_insert->bind_param("ssssss", $first_name, $last_name, $email, $phone_number, $hashed_password, $role);

    // Attempt to save to the database.
    if ($stmt_insert->execute()) {
        /**
         * SUCCESS: The account is created! 
         * Now we perform an "Auto-Login" for a better user experience.
         */
        $new_user_id = $stmt_insert->insert_id; // Capture the unique ID generated by MySQL.
        
        // Write the user's identity into the Session Notebook.
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['role'] = $role;
        $_SESSION['name'] = $first_name . " " . $last_name;
        
        $stmt_insert->close(); // Close the insert tool.

        // Use JavaScript to show a success message and redirect them to their dashboard.
        echo "<script>alert('Sign-up successful! Welcome to Wema Travellers.'); window.location.href='dashboard.php';</script>";
        exit(); // Stop current script to allow redirect.
    } else {
        // FAIL: If something technical happened (like the DB server going offline).
        $stmt_insert->close();
        die("System Error: Signup failed. Please try again later. Detail: " . $conn->error);
    }
}

// =================================================================
// PART 2: LOGGING IN (ENTER THE SITE)
// =================================================================

/**
 * We check if the 'login' button from login.html was clicked.
 * Login also uses POST for security (to hide passwords from the browser URL).
 */
if (isset($_POST['login'])) {
    
    // --- STEP 1: CAPTURE LOGIN DETAILS ---
    $email = trim($_POST['email']);
    $password_input = trim($_POST['password']); // The password they just typed

    // --- STEP 2: SEARCH FOR THE USER ---
    // We look for a user account that matches the typed email.
    $stmt_login = $conn->prepare("SELECT user_id, password, role, first_name, last_name FROM users WHERE email = ?");
    
    // Bind the email string.
    $stmt_login->bind_param("s", $email);
    
    // Run the query.
    $stmt_login->execute();
    
    // Load matching results.
    $stmt_login->store_result();
    
    // Map the database columns to variables (id, scrambled_pass, etc.).
    $stmt_login->bind_result($user_id, $stored_password, $role, $f_name, $l_name);

    // If we found exactly 1 person with that email, we can check their password next.
    if ($stmt_login->num_rows > 0) {
        $stmt_login->fetch(); // Pull the data into our variables.

        // --- STEP 3: PASSWORD VERIFICATION ---
        /**
         * password_verify() is a smart function. 
         * It takes the typed password, scrambles it using the same secret logic as before,
         * and checks if it matches the scrambled code (stored_password) in our database.
         */
        if (password_verify($password_input, $stored_password)) {
            
            // --- SUCCESS: CREDENTIALS VALID ---
            // Register them in the Session so the site knows who is browsing.
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $role;
            $_SESSION['name'] = $f_name . " " . $l_name;
            
            $stmt_login->close();

            /**
             * --- STEP 4: REDIRECT BASED ON STATUS (RBAC) ---
             * ROLE-BASED ACCESS CONTROL (RBAC) ensures users only see what they need.
             * - ADMINs: Go to the System Management panel.
             * - AGENTs: Go to the Ticket Booking workspace.
             * - PASSENGERs: Go to their personal booking dashboard.
             */
            if($role == 'ADMIN') {
                 echo "<script>alert('Login successful! Welcome Admin.'); window.location.href='admin_dashboard.php';</script>";
            } elseif ($role == 'AGENT') {
                 echo "<script>alert('Login successful! Welcome Agent.'); window.location.href='agent_dashboard.php';</script>";
            } else {
                 echo "<script>alert('Login successful!'); window.location.href='dashboard.php';</script>";
            }
            exit(); // End script to allow browser redirect.
            
        } else {
            // --- FAIL: WRONG PASSWORD ---
            $stmt_login->close();
            echo "<script>alert('Incorrect password! Please try again.'); window.location.href='login.html';</script>";
            exit();
        }
    } else {
        // --- FAIL: EMAIL DOESN'T EXIST ---
        $stmt_login->close();
        echo "<script>alert('Error: Account with this email not found.'); window.location.href='login.html';</script>";
        exit();
    }
}

// --- FINAL CLEANUP ---
// Close the connection to the MySQL server to stay efficient.
$conn->close();
?>
