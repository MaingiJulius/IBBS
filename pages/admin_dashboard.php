<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side logic engine.

/**
 * ADMIN DASHBOARD (admin_dashboard.php)
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// ADMIN DASHBOARD is the module title. * (asterisk) / (forward slash) 
// closes the block.

session_start();
// session_start (session start) is the command that activates the server's memory 
// to track the user across different pages. ( ) (empty brackets) execute the tool. 
// ; (semicolon) terminates the instruction.

// --- SECURITY CHECK ---
// / (slash) / (slash) starts a developer's visual separator comment for organization.

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
// if (if) starts a logic check for security. ( (opening bracket) starts condition. 
// ! (exclamation mark) is the NOT operator. isset (is set) checks if a memory 
// container exists. ( $ (dollar sign) _ (underscore) SESSION [ 'role' ] ) (bracket). 
// || (double pipe) is the logical OR operator. $_SESSION['role'] !== 'ADMIN' 
// (not identical to admin text). ) (closing bracket). { (opening curly bracket) 
// starts the redirection logic.

    header("Location: login.html");
    // header (header) is a tool that sends a instruction to the user's browser. 
    // ( "Location: login.html" ) is the redirect command to the login page. 
    // ; (semicolon) terminates the instruction.

    exit();
    // exit (exit) is a function that immediately stops the server from reading any 
    // more code in this file. ( ) (empty brackets). ; (semicolon) terminates the line.
}
// } (closing curly bracket) ends the security check block.
?>
<!-- ? (question mark) > (greater than sign) is the closing tag that ends the 
PHP logic and returns to HTML mode. -->

<!DOCTYPE html>
<!-- < (less than sign) ! (exclamation mark) DOCTYPE (document type) html (h t m l) 
> (greater than sign) is the standard declaration for a modern web page. -->

<html lang="en">
<!-- < (less than sign) html (h t m l) starts the web document. lang (language) 
= (equals sign) "en" (English text). > (greater than sign). -->

<head>
<!-- < (less than sign) head (h e a d) > (greater than sign) starts the hidden 
configuration section of the page. -->

    <meta charset="UTF-8">
    <!-- < (less than sign) meta (m e t a) charset (character set) = (equals sign) 
    "UTF-8" (standard text encoding) > (greater than sign). -->

    <title>Master Control Panel - Wema Travellers</title>
    <!-- < (less than sign) title (t i t l e) > (greater than sign) sets the label 
    on the browser's tab. < / (slash) title > (greater than sign). -->

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- meta tag for responsive design. viewport (view port) allows the page 
    to fit on mobile screens. content="width=device-width" (quote). -->

    <link rel="stylesheet" href="css/style.css">
    <!-- < (less than sign) link (l i n k) rel (relationship) = (equals sign) 
    "stylesheet" href (reference) = (equals sign) "css/style.css" > (greater than sign). -->

    <link rel="stylesheet" href="css/main.css">
    <!-- < (less than sign) link (l i n k) rel (relationship) = (equals sign) 
    "stylesheet" href (reference) = (equals sign) "css/main.css" > (greater than sign). -->

    <style>
    /* / (forward slash) * (asterisk) starts a CSS design section. */

        body { font-family: 'Segoe UI', sans-serif; background-color: #f7f9fc; margin: 0; padding: 0; }
        /* body selector defines the text style and soft blue background color. */

        .container { width: 90%; max-width: 1200px; margin: 30px auto; padding: 20px; }
        /* . (dot) container (class) defines the centered layout box width. */

        .welcome-banner { background: var(--purple); color: #ffffff; padding: 50px 20px; border-radius: 16px; margin-bottom: 40px; text-align: center; }
        /* . (dot) welcome-banner (class) defines the large purple announcement box. */

        .welcome-banner h1 { margin: 0; font-size: 2.5rem; }
        /* design for the main heading inside the banner. */

    </style>
    <!-- < / (slash) style (s t y l e) > (greater than sign) ends the design section. -->

</head>
<!-- < / (slash) head (h e a d) > (greater than sign) ends configuration. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">
<!-- < (less than sign) body (b o d y) class (class) = (equals sign) starts 
the visible part of the page. [echo] prints the lower-case role name 
to apply the correct design theme. -->

    <script src="js/header2.js"></script>
    <!-- < (less than sign) script (s c r i p t) src (source) = (equals sign) 
    "js/header2.js" > (greater than sign) imports the navigation header logic. -->

    <div style="height: 100px;"></div>
    <!-- < (less than sign) div (d i v) style (style) = (equals sign) "height: 100px;" 
    > (greater than sign) creates a vertical spacer. < / (slash) div >. -->

    <div class="container">
    <!-- < (less than sign) div (d i v) class (class) = (equals sign) "container" 
    > (greater than sign) is the main page wrapper. -->

        <div class="welcome-banner">
        <!-- < (less than sign) div (d i v) class (class) = (equals sign) 
        "welcome-banner" > (greater than sign) starts the announcement box. -->

            <h1>Administrative Operations</h1>
            <!-- <h1> (heading level one) is the main title on the dashboard. -->

            <p>Authorized access granted to: <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></p>
            <!-- html (HyperText) special (special) chars (characters) is a security tool 
                 that encodes text for safety. ( starts the tool. $_SESSION (memory array) 
                 ['name'] (user's identity label) ) ends. -->
            <!-- <p> (paragraph) prints the logged-in user's full name securely. -->

        </div>
        <!-- < / (slash) div > (greater than sign) ends the welcome banner. -->

        <div style="text-align: center; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e8ed;">
        <!-- div tag for the main dashboard center piece with a soft shadow effect. -->

            <h3 style="color: var(--purple);">Welcome to the Control Center</h3>
            <!-- <h3> (heading level three) message for the administrator. -->

            <p style="color: #5a6b7d;">All administrative tools and reports are now organized in the navigation bar above.</p>
            <!-- <p> (paragraph) text instructions for the navigation area. -->

            <p style="color: #5a6b7d;">Select a category (<strong>Manage Actors</strong>, <strong>Logistics</strong>, or <strong>Reports</strong>) to begin your operations.</p>
            <!-- <p> (paragraph) text instructions for the operational flow. -->

        </div>
        <!-- < / (slash) div > (greater than sign) ends the center piece. -->

    </div>
    <!-- < / (slash) div > (greater than sign) ends the container. -->

    <div style="height: 100px;"></div>
    <!-- vertical spacer div for bottom padding. -->

    <script src="js/footer.js"></script>
    <!-- script tag that imports the footer logic. -->

</body>
<!-- < / (slash) body > (greater than sign) ends page content. -->

</html>
<!-- < / (slash) html > (greater than sign) ends the document. -->
