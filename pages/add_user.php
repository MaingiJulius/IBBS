<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side logic engine.

/**
 * ADD_USER.PHP
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// ADD_USER.PHP is the module title. * (asterisk) / (forward slash) closes the block.

session_start();
// session_start (session start) is the command that activates the server's memory 
// to track the user across different pages. ( ) (empty brackets) execute the tool. 
// ; (semicolon) terminates the instruction.

require_once 'db_connection.php';
// require_once (require once) is a directive that imports the database bridge file and 
// ensures it is only loaded one time to prevent errors. 'db_connection.php' (quote db 
// underscore connection dot php quote) is the file path. ; (semicolon) terminates the line.

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
// if (if) starts a logic check for security. ( (opening bracket) starts condition. 
// ! (exclamation mark) is the NOT operator. isset (is set) checks if a memory 
// container exists. ( $ (dollar sign) _ (underscore) SESSION [ 'user_id' ] ) (bracket). 
// || (double pipe) is the logical OR operator. $_SESSION['role'] !== 'admin' 
// (not identical to admin text). ) (closing bracket). { (opening curly bracket) 
// starts the security error logic.

    die("Security Error: Access denied. Authorized Admins only.");
    // die (die) is a terminal function that prints an error message and stops all 
    // further code execution. ( "Security Error..." ) is the message. 
    // ; (semicolon) terminates the line.
}
// } (closing curly bracket) ends the security check block.

if (isset($_POST['add_user'])) {
// if (if) starts a check for the registration form submission. ( (opening bracket) 
// isset (is set) ( $ (dollar sign) _ (underscore) POST [ 'add_user' ] ) (bracket). 
// ) (closing bracket). { (opening curly bracket) starts the user creation logic.

    $fname    = $_POST['first_name'];
    // $ (dollar sign) creates a variable. fname (first name) is the label. 
    // = (equals sign) assigns the form data. $_POST['first_name'] is the 
    // input from the typing box. ; (semicolon) terminates the line.

    $lname    = $_POST['last_name'];
    // $ (dollar sign) variable. lname (last name) label. = (equals sign) 
    // assignment. $_POST['last_name'] is the typed text. ; (semicolon).

    $email    = $_POST['email'];
    // $ (dollar sign) variable. email (email) label. = (equals sign) assignment. 
    // $_POST['email'] is the typed address. ; (semicolon).
    
    $password = $_POST['password']; 
    // $ (dollar sign) variable. password (password) label. = (equals sign) 
    // assignment of the temporary secret key. ; (semicolon).
    
    $role = 'user'; 
    // $ (dollar sign) variable. role (role) label. = (equals sign) assignment 
    // of the 'user' level. ; (semicolon).

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    // $ (dollar sign) creates a variable. stmt (statement) is the handle for the 
    // secure database interaction. = (equals sign) assignment. $ (dollar sign) 
    // conn (connection bridge handle) -> (arrow) points to the prepare (prepare) 
    // tool which is a security function that pre-compiles the command. 
    // Pre-compiling (pre compiling) means the machine creates a structural 
    // blueprint of the command before any data is added. This stops hackers 
    // from injecting malicious code because the machine already knows the 
    // exact shape of the instruction. ( (opening bracket) "INSERT INTO..." 
    // (quote) is the SQL command string ) (closing bracket). ; (semicolon).

    $stmt->bind_param("sssss", $fname, $lname, $email, $password, $role);
    // $ (dollar sign) stmt (statement handle) -> (arrow) points to the 
    // bind_param (bind parameter) tool which links real variables to the 
    // query placeholders. Binding (binding) is the process of safely pouring 
    // the user's data into the pre-compiled blueprint holes (?). This ensures 
    // the data is treated only as text or numbers, never as a command. 
    // ( (opening bracket) "sssss" (types: 5 strings) , (comma) $ (dollar sign) 
    // fname (first name) , (comma) $ (dollar sign) lname (last name) , (comma) 
    // $ (dollar sign) email (address) , (comma) $ (dollar sign) password 
    // (secret) , (comma) $ (dollar sign) role (level) ) (closing bracket). 
    // ; (semicolon).
    
    $stmt->execute();
    // $ (dollar sign) stmt (handle) -> (arrow) execute (execute) tells the database 
    // to run the pre-written insertion command with the bound data. ( (opening 
    // bracket) ) (closing bracket). ; (semicolon).
    
    $stmt->close();
    // $ (dollar sign) stmt (handle) -> (arrow) close (close) is a cleanup tool 
    // that releases the server's memory resources after the command is 
    // finished. ( (opening bracket) ) (closing bracket). ; (semicolon).

    header('Location: admin_users.php');
    // header (header) is the redirection tool. ( 'Location: admin_users.php' ) 
    // tells the browser to refresh the page. ; (semicolon).

    exit();
    // exit (exit) stops the script processing. ; (semicolon).
}
// } (closing curly bracket) ends the creation logic block.
?>
<!-- ? (question mark) > (greater than sign) ends the PHP logic section. -->

<!DOCTYPE html>
<!-- < (less than sign) ! (exclamation mark) DOCTYPE (document type) html (h t m l) 
> (greater than sign) standard modern web document definition. -->

<html lang="en">
<!-- < (less than sign) html (h t m l) starts the web document. lang (language) 
= (equals sign) "en" (English text). > (greater than sign). -->

<head>
<!-- < (less than sign) head (h e a d) > (greater than sign) starts the hidden 
configuration section of the page. -->

    <meta charset="UTF-8">
    <!-- < (less than sign) meta (m e t a) charset (character set) = (equals sign) 
    "UTF-8" (standard encoding) > (greater than sign). -->

    <title>Manually Add User - Wema Travellers</title>
    <!-- < (less than sign) title (t i t l e) > (greater than sign) sets tab text. -->

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- meta tag for responsive design. viewport (view port) allows the page 
    to fit on mobile screens. -->

    <style>
    /* / (forward slash) * (asterisk) starts a CSS design section. */

        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0; }
        /* body (body) selector defines the global font and background. */

        .container { max-width: 400px; margin: 80px auto; padding: 30px; background-color: white; box-shadow: 0px 5px 15px rgba(0,0,0,0.1); border-radius: 10px; }
        /* . (dot) container (class) defines the centered form box with a shadow. */

        h2 { text-align: center; margin-bottom: 25px; color: #333; }
        /* h2 (heading level two) defines title alignment. */

        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 12px; margin-bottom: 18px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        /* rules for different input types to ensure consistent design. */

        button { width: 100%; padding: 12px; background-color: #4CAF50; border: none; color: white; font-size: 1rem; font-weight: bold; border-radius: 6px; cursor: pointer; transition: background 0.2s; }
        /* button (button) selector creates the green interactive element. */

        button:hover { background-color: #388e3c; }
        /* hover rule for visual feedback. */

        .back-link { display: block; text-align: center; margin-top: 20px; color: #666; text-decoration: none; font-size: 0.9rem; }
        /* . (dot) back-link (class) defines the navigation link style. */

        .back-link:hover { text-decoration: underline; }
        /* hover rule for the back link. */

    </style>
    <!-- < / (slash) style (s t y l e) > (greater than sign) ends CSS section. -->

</head>
<!-- < / (slash) head (h e a d) > (greater than sign) ends configuration. -->

<body class="<?= strtolower($_SESSION['role'] ?? 'admin') ?>-role">
<!-- < (less than sign) body (b o d y) class (class) = (equals sign) starts 
the visible part of the page. [echo] [echo] prints the lower-case role name. -->

<script src="js/header2.js"></script>
<!-- < (less than sign) script (s c r i p t) src (source) = (equals sign) 
"js/header2.js" > (greater than sign) imports the navigation header logic. -->

<div style="height: 100px;"></div>
<!-- spacer div for vertical layout padding. -->

<div class="container">
<!-- < (less than sign) div (d i v) class (class) = (equals sign) "container" 
> (greater than sign) is the centered form wrapper. -->

    <h2>Add New User (Admin)</h2>
    <!-- <h2> (heading level two) title for the registration tool. -->
    
    <form method="POST" id="addUserForm" onsubmit="return validateForm()">
    <!-- < (less than sign) form (f o r m) method (method) = (equals sign) 
    "POST" (secure transmission). onsubmit (on submit) = (equals sign) 
    "return validateForm()" (runs browser check). -->

        <input type="text" name="first_name" id="first_name" placeholder="First Name" onmouseout="validateFirstName()">
        <!-- input field for the user's first name with validation trigger. -->

        <input type="text" name="last_name" id="last_name" placeholder="Last Name" onmouseout="validateLastName()">
        <!-- input field for the user's last name with validation trigger. -->

        <input type="text" name="email" id="email" placeholder="Email Address" onmouseout="validateEmail()">
        <!-- input field for the user's login email address. -->

        <input type="text" name="password" id="password" placeholder="Temporary Password" onmouseout="validatePassword()">
        <!-- input field for the temporary secret password. -->
        
        <button type="submit" name="add_user">Create User Account</button>
        <!-- button tag that sends the form data to the server. -->

    </form>
    <!-- < / (slash) form > (greater than sign) ends the entry section. -->

    <script>
    // < (less than sign) script (s c r i p t) > (greater than sign) starts 
    // the browser-side interactive logic.

        function validateFirstName() {
        // function (function) defines a tool named validateFirstName. { (curly 
        // bracket) starts the logic.

            var val = document.getElementById("first_name").value;
            // var (variable) val (label). = (equals sign) assignment of the typing value.

            if (val.length == 0) { alert("First Name is required."); document.getElementById("first_name").focus(); return false; }
            // if (if) check for empty text. alert (alert) shows a pop-up.

            return true;
            // return (return) true (true) means the check passed.
        }

        function validateLastName() {
        // tool for checking the last name typing box.

            var val = document.getElementById("last_name").value;
            if (val.length == 0) { alert("Last Name is required."); document.getElementById("last_name").focus(); return false; }
            return true;
        }

        function validateEmail() {
        // tool for checking the email address structure.

            var email = document.getElementById("email").value;
            if (email.length == 0 || email.indexOf("@") == -1 || email.indexOf(".") == -1) { alert("Please enter a valid email address."); document.getElementById("email").focus(); return false; }
            // check for empty text or missing '@' and '.' symbols.

            return true;
        }

        function validatePassword() {
        // tool for checking the password typing box.

            var val = document.getElementById("password").value;
            if (val.length == 0) { alert("Please provide a temporary password."); document.getElementById("password").focus(); return false; }
            return true;
        }

        function validateForm() {
        // master tool that runs all individual checks before the form is sent.

            var rtned = true;
            rtned = validateFirstName();
            if (rtned == true) rtned = validateLastName();
            if (rtned == true) rtned = validateEmail();
            if (rtned == true) rtned = validatePassword();
            return rtned; 
            // return (return) the final result to allow or block the submission.
        }

    </script>
    <!-- < / (slash) script > (greater than sign) ends the logic. -->

    <a href="admin_users.php" class="back-link">← Cancel & Back to User List</a>
    <!-- a (anchor) tag creates a link back to the user management list. -->

</div>
<!-- < / (slash) div > (greater than sign) ends the centered container. -->

</body>
<!-- < / (slash) body > (greater than sign) ends visible content. -->

</html>
<!-- < / (slash) html > (greater than sign) ends the document structure. -->

