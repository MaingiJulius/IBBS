<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side processing engine.

/**
 * ADMINISTRATION: STRATEGIC INSIGHTS HUB (admin_insights.php)
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// ADMINISTRATION: STRATEGIC INSIGHTS HUB is the module title. * (asterisk) / 
// (forward slash) closes the block.

session_start();
// session_start (session start) is the command that activates the server's memory 
// to track the user across different pages. ( ) (empty brackets) execute the tool. 
// ; (semicolon) terminates the instruction.

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

    <title>Business Intel - Wema Travellers</title>
    <!-- < (less than sign) title (t i t l e) > (greater than sign) sets the label 
    on the browser's tab. < / (slash) title > (greater than sign). -->

    <link rel="stylesheet" href="css/style.css">
    <!-- < (less than sign) link (l i n k) rel (relationship) = (equals sign) 
    "stylesheet" href (reference) = (equals sign) "css/style.css" > (greater than sign). -->

    <link rel="stylesheet" href="css/main.css">
    <!-- < (less than sign) link (l i n k) rel (relationship) = (equals sign) 
    "stylesheet" href (reference) = (equals sign) "css/main.css" > (greater than sign). -->

    <style>
    /* / (forward slash) * (asterisk) starts a CSS design section. */

        .container { width: 90%; max-width: 1000px; margin: 50px auto; text-align: center; }
        /* . (dot) container (class) defines the look of the main centered content box. */

        .report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 40px; }
        /* . (dot) report-grid (class) defines a flexible 2D layout for the reports. */

        .report-link { display: block; padding: 30px; background: white; border: 1px solid #ddd; border-radius: 10px; text-decoration: none; color: black; font-weight: bold; }
        /* . (dot) report-link (class) defines the cards for each report. */

        .report-link:hover { background: var(--purple); color: white; }
        /* hover rule that changes card color when the mouse pointer is over it. */

    </style>
    <!-- < / (slash) style (s t y l e) > (greater than sign) ends the design section. -->

</head>
<!-- < / (slash) head (h e a d) > (greater than sign) ends configuration. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">
<!-- < (less than sign) body (b o d y) class (class) = (equals sign) 
< (less than sign) ? (question mark) = (equals sign for echo) strtolower 
(lowercase conversion) ( $_SESSION['role'] ) (user role memory). ? (question 
mark) > (greater than sign) -role (text) > (greater than sign). -->

    <script src="js/header2.js"></script>
    <!-- < (less than sign) script (s c r i p t) src (source) = (equals sign) 
    "js/header2.js" > (greater than sign) imports the navigation bar logic. < / 
    (slash) script > (greater than sign). -->

    <div style="height: 100px;"></div>
    <!-- < (less than sign) div (d i v) style (style) = (equals sign) "height: 100px;" 
    > (greater than sign) is a spacer element. < / (slash) div > (greater than sign). -->

    <div class="container">
    <!-- < (less than sign) div (d i v) class (class) = (equals sign) "container" 
    > (greater than sign) starts the main wrapper box. -->

        <a href="admin_dashboard.php" class="button" style="width:auto; padding:10px 20px;">← Back to Panel</a>
        <!-- < (less than sign) a (anchor link) href (reference) = (equals sign) 
        "admin_dashboard.php" > (greater than sign) return button. -->

        <h1>Data Insights Hub</h1>
        <!-- <h1> (heading level one) main title. -->

        <p>Access business intelligence reports and filtered analytics.</p>
        <!-- <p> (paragraph) descriptive text. -->

        <div class="report-grid">
        <!-- < (less than sign) div (d i v) class (class) = (equals sign) "report-grid" 
        > (greater than sign) starts the reports list layout. -->

            <a href="report_this_week.php" class="report-link">📊 Weekly Report</a>
            <!-- a (link) card for the current week's data. -->

            <a href="report_last_week.php" class="report-link">⏮️ Last Week Audit</a>
            <!-- a (link) card for the previous week's audit records. -->

            <a href="report_this_month.php" class="report-link">📅 Monthly Sales</a>
            <!-- a (link) card for the current month's sales volume. -->

            <a href="report_last_month.php" class="report-link">🕒 Previous Month</a>
            <!-- a (link) card for the history of the previous month. -->

            <a href="report_this_year.php" class="report-link">💰 Annual Report</a>
            <!-- a (link) card for the yearly financial summary. -->

        </div>
        <!-- < / (slash) div > (greater than sign) ends the grid. -->

    </div>
    <!-- < / (slash) div > (greater than sign) ends the container. -->

    <script src="js/footer.js"></script>
    <!-- script tag that imports the footer logic. -->

</body>
<!-- < / (slash) body > (greater than sign) ends the visible page content. -->

</html>
<!-- < / (slash) html > (greater than sign) ends the document structure. -->
