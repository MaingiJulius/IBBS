<?php
/* // < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side logic engine.

/**
 * SYSTEM AUDIT DASHBOARD (admin_logs.php)
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// SYSTEM AUDIT DASHBOARD is the module title. * (asterisk) / (forward slash) closes the block. */

require_once 'db_connection.php';
/* // require_once (require once) is a directive that imports the database bridge file and 
// ensures it is only loaded one time to prevent errors. 'db_connection.php' (quote db 
// underscore connection dot php quote) is the file path. ; (semicolon) terminates the line. */

session_start();
/* // session_start (session start) is the command that activates the server's memory 
// to track the user across different pages. ( ) (empty brackets) execute the tool. 
// ; (semicolon) terminates the instruction. */

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
/* // if (if) starts a logic check for security. ( (opening bracket) starts condition. 
// ! (exclamation mark) is the NOT operator. isset (is set) checks if a memory 
// container exists. ( (opening bracket) $ (dollar sign) _ (underscore) SESSION 
// (session memory array) [ 'role' ] (key for user type) ) (closing bracket). 
// || (double pipe) is the logical OR operator. $_SESSION['role'] !== 'ADMIN' 
// (not identical to admin text). ) (closing bracket). { (opening curly bracket) 
// starts the redirection logic. */

    header("Location: login.html");
   /*  // header (header) is a tool that sends a instruction to the user's browser. 
    // ( "Location: login.html" ) is the redirect command to the login page. 
    // ; (semicolon) terminates the instruction. */

    exit();
   /*  // exit (exit) is a function that immediately stops the server from reading any 
    // more code in this file. ( ) (empty brackets). ; (semicolon) terminates the line. */
}
/* // } (closing curly bracket) ends the security check block. */

$sql = "SELECT * FROM Logs ORDER BY log_id DESC";
/* // $ (dollar sign) variable marker. sql (s q l) is the logical label chosen 
// for the database retrieval command. = (equals sign) assignment operator. 
// "SELECT * FROM Logs..." (quote) starts the SQL instruction. * (asterisk) 
// is the wildcard character that tells the database to select ALL columns. 
// FROM (from) identifies the source table. Logs (L o g s) is the specific 
// table holding the audit data. ORDER BY (order by) log_id DESC (descending) 
// ensures the newest logs appear first. ; (semicolon) terminates the line. */

$result = mysqli_query($conn, $sql);
/* // $ (dollar sign) variable marker. result (r e s u l t) is the identifier 
// for the data container that will hold the found logs. = (equals sign) 
// assignment. mysqli (MySQL Improved) _ (underscore) query (query) is the 
// function that transmits the command to the database server. ( (opening 
// bracket) $ (dollar sign) conn (the bridge handle) , (comma) $ (dollar sign) 
// sql (the command text) ) (closing bracket). ; (semicolon). */
?>
<!-- ? (question mark) > (greater than sign) is the closing tag that ends the 
PHP logic and returns to HTML mode. -->

<!DOCTYPE html>
<!-- < (less than sign) ! (exclamation mark) DOCTYPE (document type) html (h t m l) 
> (greater than sign) is the standard declaration that tells the browser this 
is a modern web page. -->

<html lang="en">
<!-- < (less than sign) html (h t m l) starts the web document. lang (language) 
= (equals sign) "en" (English text). > (greater than sign). -->

<head>
<!-- < (less than sign) head (h e a d) > (greater than sign) starts the hidden 
configuration section of the page. -->

    <meta charset="UTF-8">
    <!-- < (less than sign) meta (m e t a) charset (character set) = (equals sign) 
    "UTF-8" (standard text encoding) > (greater than sign). -->

    <title>System Audit Logs - Wema Travellers</title>
    <!-- < (less than sign) title (t i t l e) > (greater than sign) sets the label 
    on the browser's tab. < / (slash) title > (greater than sign). -->

    <link rel="stylesheet" href="css/main.css">
    <!-- < (less than sign) link (l i n k) rel (relationship) = (equals sign) 
    "stylesheet" href (reference) = (equals sign) "css/main.css" > (greater than sign). -->

    <link rel="stylesheet" href="css/style.css">
    <!-- < (less than sign) link (l i n k) rel (relationship) = (equals sign) 
    "stylesheet" href (reference) = (equals sign) "css/style.css" > (greater than sign). -->

    <style>
    /* / (forward slash) * (asterisk) starts a CSS design comment block. */

        .logs-container { max-width: 1200px; margin: 30px auto; padding: 25px; background: white; border-radius: 12px; }
        /* . (dot) logs-container (class name) defines the look of the main box. */

        .logs-table { width: 100%; border-collapse: collapse; }
        /* . (dot) logs-table (class name) defines the table layout. */

        .logs-table th, .logs-table td { padding: 15px; border-bottom: 1px solid #eee; }
        /* cells design rules. */

        .logs-table th { background: var(--purple); color: white; }
        /* header cells color rules. */

    </style>
    <!-- < / (slash) style (s t y l e) > (greater than sign) ends the design section. -->

</head>
<!-- < / (slash) head (h e a d) > (greater than sign) ends configuration. -->

<body class="admin-role">
<!-- < (less than sign) body (b o d y) class (class) = (equals sign) "admin-role" 
> (greater than sign) starts the visible page content. -->

    <script src="js/header2.js"></script>
    <!-- < (less than sign) script (s c r i p t) src (source) = (equals sign) 
    "js/header2.js" > (greater than sign) imports the navigation logic. < / 
    (slash) script > (greater than sign). -->

    <div style="height: 100px;"></div>
    <!-- < (less than sign) div (d i v) style (style) = (equals sign) "height: 100px;" 
    > (greater than sign) is a spacer element. < / (slash) div > (greater than sign). -->

    <div class="logs-container">
    <!-- < (less than sign) div (d i v) class (class) = (equals sign) "logs-container" 
    > (greater than sign) starts the audit log display area. -->

        <h2 style="color: var(--purple);">📜 System Audit Trail</h2>
        <!-- < (less than sign) h2 (heading level two) style (style) = (equals sign) 
        "color: var(--purple);" > (greater than sign) title with purple color. -->

        <table class="logs-table">
        <!-- < (less than sign) table (t a b l e) class (class) = (equals sign) 
        "logs-table" > (greater than sign) starts the data grid structure. -->

            <thead>
            <!-- < (less than sign) thead (t h e a d) > (greater than sign) starts 
            the table header section. -->

                <tr>
                <!-- < (less than sign) tr (t a b l e space r o w) > (greater than sign) 
                starts the heading row. -->

                    <th>Ref ID</th><th>Performer</th><th>Action</th><th>Description</th><th>Time</th><th>Date</th>
                    <!-- < (less than sign) th (t a b l e space h e a d i n g) > (greater 
                    than sign) defines a header cell. -->

                </tr>
                <!-- < / (slash) tr > (greater than sign) ends the heading row. -->

            </thead>
            <!-- < / (slash) thead > (greater than sign) ends the header section. -->

            <tbody>
            <!-- < (less than sign) tbody (t b o d y) > (greater than sign) starts 
            the table body where the data will be listed. -->

                <?php if (mysqli_num_rows($result) > 0): ?>
            <!--     // if (if) check for the existence of records. ( (opening bracket) 
                // mysqli (MySQL Improved) _ (underscore) num (number) _ (underscore) 
                // rows (rows) is a tool that counts the results. ( (bracket) 
                // $ (dollar sign) result (data container) ) (bracket) > (greater than 
                // sign) 0 (zero) matches ) (closing bracket). : (colon) starts the block. -->

                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                  <!--   // while (while) starts a loop that repeats for every record. 
                    // ( (opening bracket) $ (dollar sign) row (current row) = (equals sign) 
                    // assignment. mysqli (MySQL Improved) _ (underscore) fetch (fetch) 
                    // _ (underscore) assoc (associative) is the tool that gets data as 
                    // a list. ( (bracket) $ (dollar sign) result (source container) 
                    // ) (bracket) ) (closing bracket). : (colon) starts the loop block. -->

                        <tr>
                        <!-- < (less than sign) tr (t r) > (greater than sign) starts 
                        a new data row for the current log. -->

                            <td>#<?= $row['log_id'] ?></td>
                            <!-- < (less than sign) td (t a b l e space d a t a) > (greater 
                            than sign) is a standard data cell. # (hash sign). < (less 
                            than sign) ? (question mark) = (equals sign) is the short-hand 
                            command to echo (print) data. $ (dollar sign) row [ 'log_id' ] 
                            (key for log identification). ? (question mark) > (greater 
                            than sign). < / (slash) td > (greater than sign). -->

                            <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                            <!-- html (HyperText) special (special) chars (characters) is a security tool 
                                 that encodes text so symbols like < are safe. ( starts the tool. 
                                 $row (log data) ['name'] (performer label) ) ends. -->

                            <td><?= $row['type'] ?></td>
                            <!-- cell for the action type (DELETION, REGISTRATION, etc). -->

                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <!-- htmlspecialchars (security tool) ( $row ['description'] (activity label) ) -->

                            <td><?= $row['time'] ?></td>
                            <!-- cell for the specific clock time of the action. -->

                            <td><?= $row['date'] ?></td>
                            <!-- cell for the calendar date of the action. -->

                        </tr>
                        <!-- < / (slash) tr > (greater than sign) ends the data row. -->

                    <?php endwhile; ?>
                    <!-- endwhile (end while) is the command that closes the record loop. -->

                <?php endif; ?>
                <!-- endif (end if) is the command that closes the record existence check. -->

            </tbody>
            <!-- < / (slash) tbody > (greater than sign) ends the data section. -->

        </table>
        <!-- < / (slash) table > (greater than sign) ends the data grid. -->

    </div>
    <!-- < / (slash) div > (greater than sign) ends the logs container. -->

    <div style="height: 100px;"></div>
    <!-- bottom spacer element. -->

    <script src="js/footer.js"></script>
    <!-- script tag that imports the footer logic. -->

</body>
<!-- < / (slash) body > (greater than sign) ends the visible page content. -->

</html>
<!-- < / (slash) html > (greater than sign) ends the document structure. -->

<?php mysqli_close($conn); ?>
<!-- // mysqli (MySQL Improved) _ (underscore) close (close) is the tool that 
// formally shuts the bridge to the database to free up server resources. 
// ( (opening bracket) $ (dollar sign) conn (connection bridge handle) 
// ) (closing bracket). ; (semicolon) terminates the instruction. -->
