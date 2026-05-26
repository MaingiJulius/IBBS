<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side logic engine.

/**
 * STAFF MANAGEMENT (admin_drivers.php)
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// STAFF MANAGEMENT is the module title. * (asterisk) / (forward slash) 
// closes the block.

require_once 'db_connection.php';
// require_once (require once) is a directive that imports the database bridge file and 
// ensures it is only loaded one time to prevent errors. 'db_connection.php' (quote db 
// underscore connection dot php quote) is the file path. ; (semicolon) terminates the line.

require_once 'logger.php';
// require_once (require once) imports the activity logging utility. 'logger.php' 
// (quote logger dot php quote) is the file path. ; (semicolon) terminates the instruction.

session_start();
// session_start (session start) is the command that activates the server's memory 
// to track the user across different pages. ( ) (empty brackets) execute the tool. 
// ; (semicolon) terminates the instruction.

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
// if (if) starts a logic check for security. ( (opening bracket) starts condition. 
// ! (exclamation mark) is the NOT operator. isset (is set) checks if a memory 
// container exists. ( $ (dollar sign) _ (underscore) SESSION [ 'user_id' ] ) (bracket). 
// || (double pipe) is the logical OR operator. $_SESSION['role'] !== 'ADMIN' 
// (not identical to admin text). ) (closing bracket). { (opening curly bracket) 
// starts the security error logic.

    die("Security Error: Access denied.");
    // die (die) is a terminal function that prints an error message and stops all 
    // further code execution. ( "Security Error: Access denied." ) is the message. 
    // ; (semicolon) terminates the line.
}
// } (closing curly bracket) ends the security check block.

if (isset($_POST['add_driver'])) {
// if (if) starts a logic check to determine if the driver creation form has 
// been submitted. ( (opening bracket) starts the condition. isset (is set) 
// checks if a variable exists. ( (bracket) $ (dollar sign) _ (underscore) 
// POST [ 'add_driver' ] (submit button) ) (bracket) ) (bracket). 
// { (opening curly bracket) marks the start of the logic.

    $national_id = $_POST['national_id'];
    // $ (dollar sign) variable marker. national_id (n a t i o n a l underscore 
    // i d) is the logical label chosen to identify the identification number. 
    // = (equals sign) assignment operator. $ (dollar sign) _ (underscore) 
    // POST (Superglobal Array that pulls/collects form data) [ 'national_id' ] 
    // retrieves data. ; (semicolon).

    $full_name = $_POST['full_name'];
    // $ (dollar sign) variable marker. full_name label. = (equals sign) 
    // assignment. $ (dollar sign) _ (underscore) POST [ 'full_name' ] 
    // ; (semicolon).

    $phone = $_POST['phone'];
    // $ (dollar sign) variable marker. phone label. = (equals sign) 
    // assignment. $ (dollar sign) _ (underscore) POST [ 'phone' ] ; (semicolon).

    $email = $_POST['email'];
    // $ (dollar sign) variable marker. email label. = (equals sign) 
    // assignment. $ (dollar sign) _ (underscore) POST [ 'email' ] ; (semicolon).
    
    $sql_reg = "INSERT INTO drivers (national_id, full_name, phone, email) VALUES (?, ?, ?, ?)";
    // $ (dollar sign) variable marker. sql_reg (s q l underscore r e g) is a 
    // logical identifier chosen to describe the database creation command. 
    // = (equals sign) assignment. "INSERT INTO..." (quote) starts the SQL 
    // instruction. ? (question marks) are four critical security placeholders 
    // that neutralize SQL Injection by separating the command structure from 
    // user data. ; (semicolon) terminates the command.

    $stmt_reg = mysqli_prepare($conn, $sql_reg);
    /* $stmt_reg (handle) = (assignment). 
       mysqli (MySQL Improved) _ (underscore) prepare (prepare) is the security 
       function that pre-compiles the command blueprint. 
       "Improved" (mysqli) is used because it supports secure "Prepared Statements" 
       that prevent hackers from injecting malicious code into our database. 
       ( starts. $conn (bridge) , (comma) $sql_reg (blueprint) ) ends. ; (semicolon). */

    mysqli_stmt_bind_param($stmt_reg, "ssss", $national_id, $full_name, $phone, $email);
    // mysqli_stmt_bind_param (bind parameter) is the function that securely 
    // attaches the 4 data points to the placeholders. Binding (binding) is the 
    // safe act of pouring the variables into the pre-compiled blueprint holes 
    // (?). ( (opening bracket) $ (dollar sign) stmt_reg (handle) , (comma) 
    // "ssss" (four strings) , (comma) $ (dollar sign) national_id , (comma) 
    // $ (dollar sign) full_name , (comma) $ (dollar sign) phone , (comma) 
    // $ (dollar sign) email ) (closing bracket). ; (semicolon).

    mysqli_stmt_execute($stmt_reg);
    // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
    // execute (execute) is the command that triggers the database creation. 
    // ( (opening bracket) $ (dollar sign) stmt_reg (handle) ) (closing bracket). 
    // ; (semicolon).

    mysqli_stmt_close($stmt_reg);
    // mysqli_stmt_close (close) terminates the tool and releases resources. 
    // ( (bracket) $ (dollar sign) stmt_reg ) (bracket). ; (semicolon).

    header('Location: admin_drivers.php?msg=Driver Added');
    // header (header) redirection tool sends the user back to the list. 
    // ; (semicolon).

    exit();
    // exit (exit) kills the script processing immediately. ; (semicolon).
}
// } (closing curly bracket) ends the creation logic block.

if (isset($_POST['update_driver'])) {
// if (if) starts a logic check to determine if the driver modification form 
// has been submitted. ( (opening bracket) starts the condition. isset (is set) 
// checks if a variable exists. ( (bracket) $ (dollar sign) _ (underscore) 
// POST [ 'update_driver' ] (submit button) ) (bracket) ) (bracket). 
// { (opening curly bracket) marks the start of the logic.

    $driver_id = $_POST['driver_id'];
    // $ (dollar sign) variable. driver_id (d r i v e r underscore i d) label. 
    // = (equals sign) assignment. $ (dollar sign) _ (underscore) POST 
    // (Superglobal Array that pulls form data) [ 'driver_id' ] retrieves 
    // the target ID. ; (semicolon).

    $national_id = $_POST['national_id'];
    // $ (dollar sign) variable marker. national_id label. = (equals sign). 
    // ; (semicolon).

    $full_name = $_POST['full_name'];
    // $ (dollar sign) variable marker. full_name label. = (equals sign). 
    // ; (semicolon).

    $phone = $_POST['phone'];
    // $ (dollar sign) variable marker. phone label. = (equals sign). 
    // ; (semicolon).

    $email = $_POST['email'];
    // $ (dollar sign) variable marker. email label. = (equals sign). 
    // ; (semicolon).

    $sql_upd = "UPDATE drivers SET national_id=?, full_name=?, phone=?, email=? WHERE driver_id=?";
    // $ (dollar sign) variable marker. sql_upd (s q l underscore u p d) is a 
    // logical identifier chosen to describe the database modification command. 
    // = (equals sign) assignment operator. "UPDATE..." (quote) starts the 
    // SQL instruction. ? (question marks) are the five security placeholders 
    // that neutralize SQL Injection by separating the command structure from 
    // user data. ; (semicolon) terminates the line.

    $stmt_upd = mysqli_prepare($conn, $sql_upd);
    // $ (dollar sign) variable marker. stmt_upd (s t m t underscore u p d) is 
    // the handle for the update tool object. = (equals sign) assignment. 
    // mysqli_prepare (prepare) pre-compiles the update blueprint. Pre-compiling 
    // (pre compiling) locks the structural shape of the UPDATE command in the 
    // database before data is introduced. ( (opening bracket) $ (dollar sign) 
    // conn (bridge handle) , (comma) $ (dollar sign) sql_upd (the command 
    // blueprint) ) (closing bracket). ; (semicolon).

    mysqli_stmt_bind_param($stmt_upd, "ssssi", $national_id, $full_name, $phone, $email, $driver_id);
    // mysqli_stmt_bind_param (bind parameter) attaches the 4 strings and 1 integer 
    // to the placeholders. Binding (binding) is the process of safely pouring 
    // the variables into the pre-compiled blueprint holes (?). ( (opening 
    // bracket) $ (dollar sign) stmt_upd (handle) , (comma) "ssssi" (types) 
    // , (comma) $ (dollar sign) national_id , (comma) $ (dollar sign) full_name 
    // , (comma) $ (dollar sign) phone , (comma) $ (dollar sign) email , (comma) 
    // $ (dollar sign) driver_id ) (closing bracket). ; (semicolon).
    // safely pouring the user's data into the pre-compiled blueprint holes (?). 
    // This ensures the data is treated only as text or numbers, never as a 
    // command. ( (opening bracket) $ (dollar sign) stmt_upd (handle) , (comma) 
    // "ssssi" (types) , (comma) $ (dollar sign) national_id , (comma) $ (dollar sign) 
    // full_name , (comma) $ (dollar sign) phone , (comma) $ (dollar sign) email 
    // , (comma) $ (dollar sign) driver_id ) (closing bracket). ; (semicolon).

    logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Updated Driver: $driver_id");
    // logActivity (log activity) is the tool that records the change in the audit 
    // logs. ( ID , Name , Action , Message ) (bracket). ; (semicolon).

    mysqli_stmt_execute($stmt_upd);
    // mysqli_stmt_execute runs the update command against the database. ( (opening 
    // bracket) $ (dollar sign) stmt_upd (handle) ) (closing bracket). ; (semicolon).

    mysqli_stmt_close($stmt_upd);
    // mysqli_stmt_close releases resources. ( (opening bracket) $ (dollar sign) 
    // stmt_upd (handle) ) (closing bracket). ; (semicolon).

    header('Location: admin_drivers.php?msg=Updated');
    // header (header) tool sends refresh command. ; (semicolon).

    exit();
    // exit (exit) stops the script. ( ) (empty brackets). ; (semicolon).
}
// } (closing curly bracket) ends the update logic block.

if (isset($_GET['remove_driver'])) {
// if (if) starts a check for the deletion link. ( (opening bracket) isset (is set) 
// ( $ (dollar sign) _ (underscore) GET [ 'remove_driver' ] ) (bracket). ) (closing 
// bracket). { (opening curly bracket) starts the removal logic.

    $driver_id = $_GET['remove_driver'];
    // $ (dollar sign) variable. driver_id (driver id) label. = (equals sign) 
    // assignment. $_GET['remove_driver'] is the ID from the URL. ; (semicolon).

    $stmt_rem = mysqli_prepare($conn, "DELETE FROM drivers WHERE driver_id = ?");
    // $ (dollar sign) variable. stmt_rem (statement removal) handle. = (equals sign) 
    // assignment. mysqli_prepare pre-compiles the deletion blueprint. Pre-compiling 
    // (pre compiling) means the machine creates a structural blueprint of the command 
    // before any data is added. This stops hackers from injecting malicious 
    // code because the machine already knows the exact shape of the instruction. 
    // ( (opening bracket) $ (dollar sign) conn (bridge) , (comma) "DELETE..." 
    // (SQL command) ) (closing bracket). ; (semicolon).

    mysqli_stmt_bind_param($stmt_rem, "i", $driver_id);
    // mysqli_stmt_bind_param (MySQL Improved statement bind parameter) attaches 
    // the ID to the placeholder. "i" means integer. Binding (binding) is the 
    // process of safely pouring the user's data into the pre-compiled blueprint 
    // holes (?). This ensures the data is treated only as text or numbers, 
    // never as a command. ( (opening bracket) $ (dollar sign) stmt_rem (handle) 
    // , (comma) "i" (integer type) , (comma) $ (dollar sign) driver_id (data) 
    // ) (closing bracket). ; (semicolon).

    logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed Driver: $driver_id");
    // logActivity (log activity) records the deletion event in the security logs. 
    // ( ID , Name , Action , Message ) (bracket). ; (semicolon).

    mysqli_stmt_execute($stmt_rem);
    // mysqli_stmt_execute runs the deletion command against the database. 
    // ( (opening bracket) $ (dollar sign) stmt_rem (handle) ) (closing bracket). 
    // ; (semicolon).

    mysqli_stmt_close($stmt_rem);
    // mysqli_stmt_close releases the server resources used for the deletion tool. 
    // ( (opening bracket) $ (dollar sign) stmt_rem (handle) ) (closing bracket). 
    // ; (semicolon).

    header("Location: admin_drivers.php?msg=Deleted");
    // header (header) tool sends the refresh command with a success message. 
    // ; (semicolon).

    exit();
    // exit (exit) stops the script processing. ; (semicolon).
}
// } (closing curly bracket) ends the removal logic block.
?>
<!-- [47] ? (question mark) > (greater than) closing delimiter for PHP. -->

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

    <title>Manage Drivers - Wema Travellers</title>
    <!-- < (less than sign) title (t i t l e) > (greater than sign) sets the label 
    on the browser's tab. < / (slash) title > (greater than sign). -->

    <link rel="stylesheet" href="css/main.css">
    <!-- < (less than sign) link (l i n k) rel (relationship) = (equals sign) 
    "stylesheet" href (reference) = (equals sign) "css/main.css" > (greater than sign). -->

    <link rel="stylesheet" href="css/style.css">
    <!-- < (less than sign) link (l i n k) rel (relationship) = (equals sign) 
    "stylesheet" href (reference) = (equals sign) "css/style.css" > (greater than sign). -->

</head>
<!-- < / (slash) head (h e a d) > (greater than sign) ends configuration. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">
<!-- < (less than sign) body (b o d y) class (class) = (equals sign) starts 
the visible part of the page. [echo] [echo] prints the lower-case role name 
to apply the correct design theme. -->

    <script src="js/header2.js"></script>
    <!-- < (less than sign) script (s c r i p t) src (source) = (equals sign) 
    "js/header2.js" > (greater than sign) imports the navigation header logic. -->

    <div style="height: 100px;"></div>
    <!-- < (less than sign) div (d i v) style (style) = (equals sign) "height: 100px;" 
    > (greater than sign) creates a vertical spacer. < / (slash) div >. -->

    <div class="container" style="margin: 0 auto;">
    <!-- < (less than sign) div (d i v) class (class) = (equals sign) "container" 
    > (greater than sign) is the main page wrapper. -->

        <div class="view-container">
        <!-- < (less than sign) div (d i v) class (class) = (equals sign) 
        "view-container" > (greater than sign) is the white content box. -->

            <h2 style="color: var(--purple);">Staff Registry</h2>
            <!-- < (less than sign) h2 (heading level two) > (greater than sign) 
            is the main title of the module. -->

            <div style="background: #fdfdfd; padding: 20px; border: 1px solid #eee; margin-bottom: 30px;">
            <!-- < (less than sign) div (d i v) style (style) = (equals sign) starts 
             the registration form box with a light background. -->

                <h3>New Staff Registration</h3>
                <!-- < (less than sign) h3 (heading level three) > (greater than sign) 
                is the sub-title for the addition section. -->

                <form method="POST" onsubmit="return validateForm()">
                <!-- < (less than sign) form (f o r m) method (method) = (equals sign) 
                "POST" (secure transmission) onsubmit (on submit) = (equals sign) 
                "return validateForm()" (run browser check). -->

                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <!-- < (less than sign) div (d i v) style (style) = (equals sign) 
                    "display: flex;" (flexible layout) starts the input row. -->

                        <div style="flex:1;"><label>Full Name</label><input type="text" name="full_name" id="full_name" class="input"></div>
                        <!-- input field for the staff member's name. label (label) 
                        provides the text description. -->

                        <div style="flex:1;"><label>ID Number</label><input type="text" name="national_id" id="national_id" class="input"></div>
                        <!-- input field for the national identification number. -->

                        <div style="flex:1;"><label>Phone</label><input type="text" name="phone" id="phone" class="input"></div>
                        <!-- input field for the contact telephone number. -->

                        <div style="flex:1;"><label>Email</label><input type="text" name="email" id="email" class="input"></div>
                        <!-- input field for the electronic mail address. -->

                    </div>
                    <!-- < / (slash) div > (greater than sign) ends the input row. -->

                    <button type="submit" name="add_driver" class="button pink-background" style="margin-top: 15px;">Register Staff</button>
                    <!-- button tag that triggers the 'add_driver' logic in the PHP 
                    engine. pink-background applies the theme. -->

                </form>
                <!-- < / (slash) form > (greater than sign) ends the registration form. -->

            </div>
            <!-- < / (slash) div > (greater than sign) ends the form box. -->

            <script>
            // < (less than sign) script (s c r i p t) > (greater than sign) starts 
            // the browser-side interactive logic.

                function toggleDriverEdit(did) {
                // function (function) defines a reusable tool named toggleDriverEdit. 
                // ( did ) is the identification number of the staff member. { (curly 
                // bracket) starts the execution block.

                    var views = document.querySelectorAll('.view-driver-' + did);
                    // var (variable) creates a list. document (web page) . (dot) 
                    // querySelectorAll (find multiple) tool collects all read-only labels. 
                    // ; (semicolon) terminates the instruction.

                    var edits = document.querySelectorAll('.edit-driver-' + did);
                    // var (variable) creates a list of all hidden input boxes for the 
                    // specific staff member. ; (semicolon).

                    views.forEach(v => v.style.display = (v.style.display === 'none' ? 'inline' : 'none'));
                    // views (list) . (dot) forEach (for each) runs a loop. v (item) 
                    // => (arrow) logic. if display is none, show it, otherwise hide it.

                    edits.forEach(e => e.style.display = (e.style.display === 'none' ? 'inline' : 'none'));
                    // edits (list) . (dot) forEach (for each) toggles the visibility 
                    // of all typing boxes.

                    document.getElementById('ops-m-' + did).style.display = (document.getElementById('ops-m-' + did).style.display === 'none' ? 'inline-block' : 'none');
                    // document (page) . (dot) getElementById (find one) toggles the 
                    // main action buttons (Edit/Delete).

                    document.getElementById('ops-s-' + did).style.display = (document.getElementById('ops-s-' + did).style.display === 'none' ? 'inline-block' : 'none');
                    // document (page) . (dot) getElementById (find one) toggles the 
                    // secondary action buttons (Update/Cancel).
                }
                // } (closing curly bracket) ends the toggle function.

                function validateForm() {
                // function (function) defines the validateForm tool. { (curly bracket) 
                // starts the data verification logic.

                    var n = document.getElementById("full_name").value.trim();
                    // var (variable) n (label). = (equals sign) assignment. trim() 
                    // removes empty spaces from the name.

                    if (n == "") { alert("Name Required"); return false; }
                    // if (if) check for empty name. alert (alert) shows a pop-up. 
                    // return false (return false) stops the form submission.
                    
                    return true;
                    // return (return) true (true) allows the data to be sent.
                }
                // } (closing curly bracket) ends the validation function.

                function filterTable() {
                // function (keyword) filterTable (name) starts local search algorithm.
                    var input = document.getElementById("search-input");
                    // var (variable) input (label) gets reference to search input.
                    var filter = input.value.toLowerCase();
                    // var (variable) filter (label) holds lowercase search filter text query.
                    var select = document.getElementById("search-column");
                    // var (variable) select (label) holds search target column dropdown menu element.
                    var colIndex = select.value;
                    // var (variable) colIndex (label) gets selected field search option.
                    
                    var table = document.querySelector(".crud-table");
                    // var (variable) table (label) gets crud table grid element.
                    var tbody = table.getElementsByTagName("tbody")[0];
                    // var (variable) tbody (label) gets first tbody of the table grid.
                    var trs = tbody.getElementsByTagName("tr");
                    // var (variable) trs (label) gets all driver rows within table body.
                    
                    for (var i = 0; i < trs.length; i++) {
                    // loops through table rows indexing variable i from 0 up to trs length.
                        var tr = trs[i];
                        // var (variable) tr (label) holds current row.
                        var match = false;
                        // var (variable) match (label) set to false initially.
                        
                        if (colIndex === "all") {
                        // if searching all cells.
                            var tds = tr.getElementsByTagName("td");
                            // var (variable) tds (label) holds all td elements inside row.
                            for (var j = 0; j < tds.length - 1; j++) { // exclude actions column
                            // loops through td elements with index variable j, excluding last column.
                                var td = tds[j];
                                // var (variable) td (label) holds current cell.
                                if (td) {
                                // if td exists.
                                    var txtValue = getCellText(td);
                                    // var (variable) txtValue isolates cell's display value.
                                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                    // if matches search filter.
                                        match = true;
                                        // match set to true.
                                        break;
                                        // break stops checking other cells.
                                    }
                                }
                            }
                        } else {
                        // else specific column filter is active.
                            var td = tr.getElementsByTagName("td")[colIndex];
                            // var (variable) td gets cell element at exact index.
                            if (td) {
                            // if td exists.
                                var txtValue = getCellText(td);
                                // var (variable) txtValue isolates cell display text.
                                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                // if matches filter query.
                                    match = true;
                                    // match set to true.
                                }
                            }
                        }
                        
                        if (match) {
                        // if matched.
                            tr.style.display = "";
                            // row display set to visible.
                        } else {
                        // else.
                            tr.style.display = "none";
                            // row display set to hidden.
                        }
                    }
                }
                // } ends filterTable function.

                function getCellText(td) {
                // function getCellText extracts cell's visible display value.
                    var selectElement = td.querySelector("select");
                    // var selectElement checks for select dropdown.
                    if (selectElement && selectElement.style.display !== "none") {
                    // if select exists and is currently visible.
                        if (selectElement.selectedIndex >= 0) {
                        // if valid selection index.
                            return selectElement.options[selectElement.selectedIndex].text;
                            // return selected text value.
                        }
                        return "";
                        // return blank.
                    }
                    var viewSpan = td.querySelector("span[class^='view-']");
                    // var viewSpan checks for display view span element.
                    if (viewSpan) {
                    // if viewSpan exists.
                        return viewSpan.textContent || viewSpan.innerText;
                        // return displayed text content.
                    }
                    return td.innerText || td.textContent || "";
                    // fallback return cell raw text.
                }
                // } ends getCellText function.

            </script>
            <!-- < / (slash) script > (greater than sign) ends the interactive logic. -->

            <!-- Search Bar -->
            <div class="search-container no-print" style="margin-bottom: 20px; display: flex; gap: 15px; align-items: center; background: rgba(255, 255, 255, 0.9); padding: 15px 20px; border-radius: 25px; border: 2px solid var(--button-border); box-shadow: 3px 3px 0px rgba(0,0,0,1);">
            <!-- < div style="..." > starts search bar container with retro theme styles. -->

                <span style="font-weight: bold; color: var(--text-color); font-size: 1.1rem; display: flex; align-items: center; gap: 5px;">
                <!-- < span > sets text styles. -->
                    🔍 Search By:
                </span>
                <!-- < / span > ends label. -->

                <select id="search-column" style="padding: 10px 15px; border-radius: 20px; border: 2px solid var(--button-border); background-color: var(--input-bg); color: var(--text-color); font-weight: bold; outline: none; cursor: pointer;">
                <!-- < select > starts select dropdown element. -->
                    <option value="all">All Fields</option>
                    <!-- option for searching all cells. -->
                    <option value="0">ID</option>
                    <!-- option for ID search. -->
                    <option value="1">Full Name</option>
                    <!-- option for Full Name search. -->
                    <option value="2">ID Number</option>
                    <!-- option for ID Number search. -->
                    <option value="3">Phone</option>
                    <!-- option for Phone search. -->
                    <option value="4">Email</option>
                    <!-- option for Email search. -->
                    <option value="5">Vehicle</option>
                    <!-- option for Vehicle search. -->
                </select>
                <!-- < / select > ends dropdown menu. -->

                <input type="text" id="search-input" placeholder="Type to filter drivers..." onkeyup="filterTable()" style="flex: 1; padding: 10px 20px; border-radius: 20px; border: 2px solid var(--button-border); background-color: var(--input-bg); color: var(--text-color); font-size: 1rem; outline: none;">
                <!-- < input > element takes user search query and triggers filterTable JS on keystroke. -->

            </div>
            <!-- < / div > ends container. -->

            <table class="crud-table">
            <!-- < (less than sign) table (t a b l e) class (class) = (equals sign) 
            "crud-table" > (greater than sign) starts the data grid. -->

                <thead><tr><th>ID</th><th>Full Name</th><th>ID Number</th><th>Phone</th><th>Email</th><th>Vehicle</th><th>Actions</th></tr></thead>
                <!-- thead (table head) defines the labels for each column. -->

                <tbody>
                <!-- tbody (table body) starts the container for the data rows. -->

                    <?php 
                    $res = mysqli_query($conn, "SELECT d.*, b.bus_name FROM drivers d LEFT JOIN buses b ON d.driver_id = b.driver_id ORDER BY d.driver_id DESC");
                    /* $res (result list) = (assignment). 
                       mysqli_query (MySQL Improved query) is the command that sends 
                       the instruction to the database. "Improved" (mysqli) is used 
                       everywhere in the system to ensure modern security and 
                       faster performance. ( starts. $conn (bridge) , (comma) 
                       "SELECT..." (instruction) ) ends. ; (semicolon). */

                    while($row = mysqli_fetch_assoc($res)): $did = $row['driver_id'];
                        /* while (while) starts a loop. $row (row container) pulls data. 
                           mysqli_fetch_assoc (fetch associative) converts raw data 
                           into labeled pieces for easy use. ( starts. $res (result source). ) ends. */
                    ?>

                    <tr>
                    <!-- tr (table row) starts the display row for one staff member. -->

                        <form method="POST">
                        <!-- form (f o r m) tag inside the row allows editing individual 
                        staff data. method (method) = (equals sign) "POST". -->

                            <input type="hidden" name="driver_id" value="<?= $did ?>">
                            <!-- input (i n p u t) of type (type) "hidden" (invisible) 
                            stores the user ID to ensure the correct record is updated. -->

                            <td><?= $did ?></td>
                            <!-- td (table data) displaying the unique staff ID number. -->

                            <td>
                                <span class="view-driver-<?= $did ?>"><?= htmlspecialchars($row['full_name']) ?></span>
                                <!-- html (HyperText) special (special) chars (characters) is a security tool 
                                     that converts dangerous symbols like < into safe text so hackers cannot 
                                     run scripts. ( starts the tool. $row (row variable) ['full_name'] (column name) 
                                     is the data being protected. ) ends the tool. -->

                                <input type="text" name="full_name" value="<?= htmlspecialchars($row['full_name']) ?>" class="edit-driver-<?= $did ?>" style="display:none;">
                                <!-- value (initial text) = [echo] htmlspecialchars (security tool) 
                                     ( $row ['full_name'] ) ensures the text in the box is safe. -->
                            </td>

                            <td>
                                <span class="view-driver-<?= $did ?>"><?= htmlspecialchars($row['national_id']) ?></span>
                                <!-- htmlspecialchars (security tool) ( $row ['national_id'] (ID number data) ) -->

                                <input type="text" name="national_id" value="<?= htmlspecialchars($row['national_id']) ?>" class="edit-driver-<?= $did ?>" style="display:none;">
                                <!-- value = [echo] htmlspecialchars (security tool) ( $row ['national_id'] ) -->
                            </td>

                            <td>
                                <span class="view-driver-<?= $did ?>"><?= htmlspecialchars($row['phone']) ?></span>
                                <!-- htmlspecialchars (security tool) ( $row ['phone'] (contact number data) ) -->

                                <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" class="edit-driver-<?= $did ?>" style="display:none;">
                                <!-- value = [echo] htmlspecialchars (security tool) ( $row ['phone'] ) -->
                            </td>

                            <td>
                                <span class="view-driver-<?= $did ?>"><?= htmlspecialchars($row['email']) ?></span>
                                <!-- htmlspecialchars (security tool) ( $row ['email'] (electronic mail data) ) -->

                                <input type="text" name="email" value="<?= htmlspecialchars($row['email']) ?>" class="edit-driver-<?= $did ?>" style="display:none;">
                                <!-- value = [echo] htmlspecialchars (security tool) ( $row ['email'] ) -->
                            </td>

                            <td><?= $row['bus_name'] ? htmlspecialchars($row['bus_name']) : 'None' ?></td>
                            <!-- cell for vehicle assignment. prints 'None' if the staff 
                                 member is not linked to a bus. htmlspecialchars (security tool) 
                                 protects the vehicle name. -->

                            <td>
                                <div id="ops-m-<?= $did ?>">
                                <!-- < (less than sign) div (box) id (identity) = "ops-m- [echo] $did" (unique number) > starts the visible action box. -->

                                    <button type="button" class="action-btn btn-update" onclick="toggleDriverEdit(<?= $did ?>)">Edit</button>
                                    <!-- < (less than sign) button (clickable item) type (nature) = "button" (does not submit) 
                                         class (style) = "action-btn (standard look) btn-update (blue color)" 
                                         onclick (on click event) = "toggleDriverEdit (run the toggle tool) ( [echo] $did (for this driver) )" 
                                         > (greater than sign) Edit (label) < / (slash) button > (ends item). -->

                                    <a href="?remove_driver=<?= $did ?>" class="action-btn btn-delete" onclick="return confirm('Delete?')">Del</a>
                                    <!-- < (less than sign) a (anchor link) href (destination) = "?remove_driver = [echo] $did" (sends ID to URL) 
                                         class (style) = "action-btn (standard look) btn-delete (red color)" 
                                         onclick (on click event) = "return confirm (ask a question) ( 'Delete?' )" 
                                         > (greater than sign) Del (label) < / (slash) a > (ends link). -->

                                </div>
                                <!-- < / (slash) div > ends main button box. -->

                                <div id="ops-s-<?= $did ?>" style="display:none;">
                                <!-- < (less than sign) div (box) id (identity) = "ops-s- [echo] $did" (unique number) 
                                     style (visual) = "display:none;" (starts hidden) > starts the edit action box. -->

                                    <button type="submit" name="update_driver" class="action-btn btn-update">Update</button>
                                    <!-- < (less than sign) button (clickable item) type (nature) = "submit" (sends the form data) 
                                         name (server label) = "update_driver" (tells PHP which logic to run) 
                                         class (style) = "action-btn (standard look) btn-update (blue color)" 
                                         > (greater than sign) Update (label) < / (slash) button > (ends item). -->

                                    <button type="button" class="action-btn btn-delete" onclick="toggleDriverEdit(<?= $did ?>)">Cancel</button>
                                    <!-- < (less than sign) button (clickable item) type (nature) = "button" (does not submit) 
                                         class (style) = "action-btn (standard look) btn-delete (red color)" 
                                         onclick (on click event) = "toggleDriverEdit (run the toggle tool) ( [echo] $did (for this driver) )" 
                                         > (greater than sign) Cancel (label) < / (slash) button > (ends item). -->

                                </div>
                                <!-- < / (slash) div > ends edit button box. -->
                            </td>

                        </form>
                        <!-- < / (slash) form > (greater than sign) ends the row form. -->

                    </tr>
                    <!-- < / (slash) tr > (greater than sign) ends the staff row. -->

                    <?php endwhile; ?>
                    <!-- endwhile (end while) ends the staff list loop. -->

                </tbody>
                <!-- < / (slash) tbody > (greater than sign) ends data rows. -->

            </table>
            <!-- < / (slash) table > (greater than sign) ends the data grid. -->

        </div>
        <!-- < / (slash) div > (greater than sign) ends the content box. -->

    </div>
    <!-- < / (slash) div > (greater than sign) ends the wrapper. -->

    <div style="height: 100px;"></div>
    <!-- spacer div for vertical padding at the bottom. -->

    <script src="js/footer.js"></script>
    <!-- script tag that imports the footer logic. -->

</body>
<!-- < / (slash) body > (greater than sign) ends page content. -->

</html>
<!-- < / (slash) html > (greater than sign) ends the document. -->

<?php mysqli_close($conn); ?>
<!-- mysqli (MySQL Improved) _ (underscore) close (close) tool that shuts the 
bridge to the database. ( $conn (bridge) ) (bracket). ; (semicolon). -->
