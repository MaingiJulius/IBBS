<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side logic engine.

/**
 * BUS FLEET MANAGEMENT (admin_buses_report.php)
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// BUS FLEET MANAGEMENT is the module title. * (asterisk) / (forward slash) closes 
// the block.

require_once 'db_connection.php';
// require_once (require once) is a directive that imports the database bridge file and 
// ensures it is only loaded one time to prevent errors. 'db_connection.php' (quote db 
// underscore connection dot php quote) is the file path. ; (semicolon) terminates the line.

require_once 'logger.php';
// require_once (require once) is a directive that imports the activity tracking tool. 
// 'logger.php' is the file path. ; (semicolon) terminates the instruction.

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
    // further code execution. ( "Security Error..." ) is the message. 
    // ; (semicolon) terminates the line.
}
// } (closing curly bracket) ends the security check block.

if (isset($_POST['add_bus'])) {
// if (if) starts a logic check to determine if the bus registration form has 
// been submitted. ( (opening bracket) starts the condition. isset (is set) 
// checks if a variable exists. ( (bracket) $ (dollar sign) _ (underscore) 
// POST [ 'add_bus' ] (submit button) ) (bracket) ) (bracket). 
// { (opening curly bracket) marks the start of the logic.

    $reg_no = $_POST['reg_no'];
    // $ (dollar sign) variable marker. reg_no (r e g underscore n o) is the 
    // logical label chosen to identify the registration number. = (equals 
    // sign) assignment operator. $ (dollar sign) _ (underscore) POST 
    // (Superglobal Array used by the server to collect and pull data from an 
    // HTML form sent via the secure HTTP POST method. It does NOT send data 
    // to the database directly; it only captures what the user typed) 
    // [ 'reg_no' ] retrieves data. ; (semicolon).

    $bus_name = $_POST['bus_name'];
    // $ (dollar sign) variable marker. bus_name label. = (equals sign) 
    // assignment. $ (dollar sign) _ (underscore) POST [ 'bus_name' ] 
    // ; (semicolon).

    $max_passengers = $_POST['max_passengers'];
    // $ (dollar sign) variable marker. max_passengers label. = (equals sign) 
    // assignment. $ (dollar sign) _ (underscore) POST [ 'max_passengers' ] 
    // ; (semicolon).
    
    $sql_add = "INSERT INTO buses (reg_no, bus_name, max_passengers) VALUES (?, ?, ?)";
    // $ (dollar sign) variable marker. sql_add (s q l underscore a d d) is a 
    // logical identifier chosen to describe the database creation command. 
    // = (equals sign) assignment. "INSERT INTO..." (quote) starts the SQL 
    // instruction. ? (question marks) are three critical security placeholders 
    // that neutralize SQL Injection by separating the command structure from 
    // user data. ; (semicolon) terminates the command.

    $stmt_add = mysqli_prepare($conn, $sql_add);
    /* $stmt_add (handle) = (assignment). 
       mysqli (MySQL Improved) _ (underscore) prepare (prepare) is the security 
       function that pre-compiles the command blueprint. 
       "Improved" (mysqli) is used because it supports secure "Prepared Statements" 
       that prevent hackers from injecting malicious code into our database. 
       ( starts. $conn (bridge) , (comma) $sql_add (blueprint) ) ends. ; (semicolon). */

    mysqli_stmt_bind_param($stmt_add, "ssi", $reg_no, $bus_name, $max_passengers);
    /* mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
       bind (bind) _ (underscore) param (parameter) is the function that 
       securely attaches the data to the query blueprint. 
       ( starts. $stmt_add is the tool handle. , (comma) separates info. 
       "ssi" (string, string, integer) defines the types. 
       $reg_no, $bus_name, $max_passengers are the variables being safely poured in. 
       ; (semicolon) terminates the line. */

    mysqli_stmt_execute($stmt_add);
    // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
    // execute (execute) is the command that triggers the database creation. 
    // ( (opening bracket) $ (dollar sign) stmt_add (handle) ) (closing 
    // bracket). ; (semicolon).

    mysqli_stmt_close($stmt_add);
    // mysqli_stmt_close (close) terminates the tool and releases resources. 
    // ( (bracket) $ (dollar sign) stmt_add ) (bracket). ; (semicolon).

    header('Location: admin_buses_report.php?msg=Bus Added');
    // header (header) redirection tool sends the user to the list page with a 
    // success message. ; (semicolon).

    exit();
    // exit (exit) stops the script processing. ; (semicolon).
}
// } (closing curly bracket) ends the creation block.

if (isset($_POST['update_bus'])) {
// if (if) check for the bus update form submission. ( (opening bracket) isset 
// (is set) ( $ (dollar sign) _ (underscore) POST [ 'update_bus' ] ) (bracket). 
// ) (closing bracket). { (opening curly bracket) starts the modification logic.

    $bus_id = $_POST['bus_id'];
    // $ (dollar sign) variable. bus_id (bus id) label. = (equals sign) assignment 
    // of the database ID. ; (semicolon).

    $reg_no = $_POST['reg_no'];
    // $ (dollar sign) variable. reg_no label. = (equals sign) assignment. ; (semicolon).

    $bus_name = $_POST['bus_name'];
    // $ (dollar sign) variable. bus_name label. = (equals sign) assignment. ; (semicolon).

    $max_passengers = $_POST['max_passengers'];
    // $ (dollar sign) variable. max_passengers label. = (equals sign) assignment. ; (semicolon).

    $driver_id = !empty($_POST['driver_id']) ? $_POST['driver_id'] : null;
    // $ (dollar sign) variable. driver_id label. = (equals sign) assignment. 
    // ! (not) empty (empty) checks if data exists. ? (question mark) is ternary 
    // IF true. : (colon) is ternary ELSE (null). ; (semicolon).

    $sql_upd = "UPDATE buses SET reg_no=?, bus_name=?, max_passengers=?, driver_id=? WHERE bus_id=?";
    // $ (dollar sign) variable. sql_upd (sql update) label. = (equals sign). 
    // "UPDATE..." (quote) SQL command. ; (semicolon).

    $stmt_upd = mysqli_prepare($conn, $sql_upd);
    // $ (dollar sign) variable. stmt_upd (statement update) handle. = (equals sign). 
    // mysqli_prepare (MySQL Improved prepare) pre-compiles the update blueprint. 
    // Pre-compiling (pre compiling) means the machine creates a structural 
    // blueprint of the command before any data is added. This stops hackers 
    // from injecting malicious code because the machine already knows the 
    // exact shape of the instruction. ( (opening bracket) $ (dollar sign) 
    // conn (bridge) , (comma) $ (dollar sign) sql_upd (command) ) (closing bracket). 
    // ; (semicolon).

    mysqli_stmt_bind_param($stmt_upd, "ssiii", $reg_no, $bus_name, $max_passengers, $driver_id, $bus_id);
    /* mysqli_stmt_bind_param (MySQL Improved statement bind parameter) 
       securely pours 2 pieces of text (s) and 3 numbers (i) into the query. 
       This prevents hackers from changing the database structure. 
       ; (semicolon). */
    // mysqli_stmt_bind_param attaches the 2 strings and 3 integers to the query. 
    // Binding (binding) is the process of safely pouring the user's data into the 
    // pre-compiled blueprint holes (?). This ensures the data is treated only as 
    // text or numbers, never as a command. ( (opening bracket) $ (dollar sign) 
    // stmt_upd (handle) , (comma) "ssiii" (types) , (comma) $ (dollar sign) 
    // reg_no , (comma) $ (dollar sign) bus_name , (comma) $ (dollar sign) 
    // max_passengers , (comma) $ (dollar sign) driver_id , (comma) $ (dollar sign) 
    // bus_id ) (closing bracket). ; (semicolon).

    logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Updated Bus: $bus_id");
    // logActivity (log activity) records the modification in the audit logs. 
    // ( performer_id , name , action , message ) (bracket). ; (semicolon).

    mysqli_stmt_execute($stmt_upd);
    // mysqli_stmt_execute runs the update command against the database. ( (opening 
    // bracket) $ (dollar sign) stmt_upd (handle) ) (closing bracket). ; (semicolon).

    mysqli_stmt_close($stmt_upd);
    // mysqli_stmt_close releases the server resources. ( (opening bracket) 
    // $ (dollar sign) stmt_upd (handle) ) (closing bracket). ; (semicolon).

    header('Location: admin_buses_report.php?msg=Updated');
    // header redirection to the list with success message. ; (semicolon).

    exit();
    // exit (exit) stops the script processing. ; (semicolon).
}
// } (closing curly bracket) ends the update block.

if (isset($_GET['remove_bus'])) {
// if (if) check for the bus removal request in the URL. ( (opening bracket) isset 
// (is set) ( $ (dollar sign) _ (underscore) GET [ 'remove_bus' ] ) (bracket). 
// ) (closing bracket). { (opening curly bracket) starts the removal logic.

    $bus_id = $_GET['remove_bus'];
    // $ (dollar sign) variable. bus_id (bus id) label. = (equals sign) assignment 
    // of the URL data. ; (semicolon).

    $stmt_del = mysqli_prepare($conn, "DELETE FROM buses WHERE bus_id = ?");
    // $ (dollar sign) variable. stmt_del (statement delete) handle. = (equals sign) 
    // assignment. mysqli_prepare pre-compiles the deletion blueprint. Pre-compiling 
    // (pre compiling) means the machine creates a structural blueprint of the command 
    // before any data is added. This stops hackers from injecting malicious 
    // code because the machine already knows the exact shape of the instruction. 
    // ( (opening bracket) $ (dollar sign) conn (bridge) , (comma) "DELETE..." 
    // (SQL command) ) (closing bracket). ; (semicolon).

    mysqli_stmt_bind_param($stmt_del, "i", $bus_id);
    // mysqli_stmt_bind_param attaches the integer ID to the placeholder. 
    // Binding (binding) is the process of safely pouring the user's data into 
    // the pre-compiled blueprint holes (?). This ensures the data is treated 
    // only as numbers, never as a command. ( (opening bracket) $ (dollar sign) 
    // stmt_del (handle) , (comma) "i" (integer type) , (comma) $ (dollar sign) 
    // bus_id (target data) ) (closing bracket). ; (semicolon).

    logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed Bus: $bus_id");
    // logActivity records the deletion event in the security logs. ; (semicolon).

    mysqli_stmt_execute($stmt_del);
    // mysqli_stmt_execute runs the deletion command against the database. 
    // ( (opening bracket) $ (dollar sign) stmt_del (handle) ) (closing bracket). 
    // ; (semicolon).

    mysqli_stmt_close($stmt_del);
    // mysqli_stmt_close releases the server resources. ( (opening bracket) 
    // $ (dollar sign) stmt_del (handle) ) (closing bracket). ; (semicolon).

    header("Location: admin_buses_report.php?msg=Deleted");
    // header redirection to the list with success message. ; (semicolon).

    exit();
    // exit (exit) stops the script. ; (semicolon).
}
// [48] } ends removal block.
?>
<!-- [49] [?] (closing tag) stops the PHP engine. -->

<!DOCTYPE html>
<!-- [50] <!DOCTYPE html> defines a standard modern web document. -->

<html lang="en">
<!-- [51] <html> (html tag) starts the web page structure. lang="en" (English). -->

<head>
<!-- [52] <head> contains hidden configuration for the page. -->

    <meta charset="UTF-8">
    <!-- [53] <meta> sets text encoding to UTF-8. -->

    <title>Fleet Management - Wema Travellers</title>
    <!-- [54] <title> sets the text on the browser tab. -->

    <link rel="stylesheet" href="css/main.css">
    <!-- [55] <link> imports design rules from 'main.css'. -->

    <link rel="stylesheet" href="css/style.css">
    <!-- [56] <link> imports design rules from 'style.css'. -->

</head>
<!-- [64] </head> ends configuration. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">
<!-- [65] <body> starts the visible content. (echo) prints the user role. -->

    <script src="js/header2.js"></script>
    <!-- [66] <script> imports the navigation bar logic. -->

    <div style="height: 100px;"></div>
    <!-- [67] <div> is a spacer box with height. -->

    <div class="container" style="margin: 0 auto;">
    <!-- [68] Main wrapper box. -->

        <div class="view-container">
        <!-- [69] Content box. -->

            <h2 style="color: var(--purple);">Fleet Management</h2>
            <!-- [70] <h2> is a large heading. style sets purple color. -->

            <div style="background: #fdfdfd; padding: 20px; border: 1px solid #eee; margin-bottom: 30px;">
            <!-- [71] Form box for adding buses. -->

                <h3>Add New Bus</h3>
                <!-- [72] <h3> is a smaller heading. -->

                <form method="POST" onsubmit="return validateForm()">
                <!-- [73] <form> (form). method="POST" (secure). onsubmit checks before saving. -->

                    <div style="display: flex; gap: 10px;">
                    <!-- [74] <div> row with gaps. -->

                        <div style="flex:1;"><label>Plate No.</label><input type="text" name="reg_no" id="reg_no" class="input"></div>
                        <!-- [75] Plate No input box. -->

                        <div style="flex:1;"><label>Name</label><input type="text" name="bus_name" id="bus_name" class="input"></div>
                        <!-- [76] Name input box. -->

                        <div style="flex:1;"><label>Capacity</label><input type="text" name="max_passengers" id="max_passengers" class="input" value="40"></div>
                        <!-- [77] Capacity input box. -->

                    </div>
                    <!-- [78] </div> ends row. -->

                    <button type="submit" name="add_bus" class="button pink-background" style="margin-top: 15px;">Save Bus</button>
                    <!-- [79] <button> saves the form data. -->

                </form>
                <!-- [80] </form> ends. -->

            </div>
            <!-- [81] </div> ends form box. -->

            <script>
            // [82] <script> starts JavaScript logic.

                function toggleBusEdit(bid) {
                // [83] function is a tool for logic. toggleBusEdit name. ( bid ) is ID. { (curly) starts.

                    var views = document.querySelectorAll('.view-bus-' + bid);
                    // [84] var (variable). views label. = (assign). document.querySelectorAll finds elements with class. ; (semicolon).

                    var edits = document.querySelectorAll('.edit-bus-' + bid);
                    // [85] var (variable). edits label. finds textbox elements. ; (semicolon).

                    views.forEach(v => v.style.display = (v.style.display === 'none' ? 'inline' : 'none'));
                    // [86] views.forEach repeats for each label. Toggle visibility logic. ; (semicolon).

                    edits.forEach(e => e.style.display = (e.style.display === 'none' ? 'inline' : 'none'));
                    // [87] edits.forEach repeats for each textbox. Toggle visibility logic. ; (semicolon).

                    document.getElementById('ops-m-' + bid).style.display = (document.getElementById('ops-m-' + bid).style.display === 'none' ? 'inline-block' : 'none');
                    // [88] document.getElementById finds main buttons. Toggle visibility logic. ; (semicolon).

                    document.getElementById('ops-s-' + bid).style.display = (document.getElementById('ops-s-' + bid).style.display === 'none' ? 'inline-block' : 'none');
                    // [89] document.getElementById finds save buttons. Toggle visibility logic. ; (semicolon).
                }
                // [90] } ends function.

                function validateForm() {
                // [91] function for checking form data. { (curly) starts.

                    var r = document.getElementById("reg_no").value.trim();
                    // [92] var (variable). r label. = (assign). get values and trim gaps. ; (semicolon).

                    if (r == "") { alert("Plate Required"); return false; }
                    // [93] if empty, show alert and stop. ; (semicolon).

                    return true;
                    // [94] return true allows saving. ; (semicolon).
                }
                // [95] } ends function.

            </script>
            <!-- [96] </script> ends JavaScript. -->

            <table class="crud-table">
            <!-- [97] <table> starts the data grid. -->

                <thead><tr><th>ID</th><th>Name</th><th>Plate</th><th>Capacity</th><th>Driver</th><th>Actions</th></tr></thead>
                <!-- [98] <thead> (header). <tr> (row). <th> (cell headings). -->

                <tbody>
                <!-- [99] <tbody> starts the data rows. -->

                    <?php 
                    $drvs = mysqli_query($conn, "SELECT driver_id, full_name FROM drivers ORDER BY full_name ASC");
                    /* $drvs (result list) = (assignment). 
                       mysqli_query (MySQL Improved query) is the command that sends 
                       the instruction to the database. "Improved" (mysqli) is used 
                       everywhere in the system to ensure modern security and 
                       faster performance. ( starts. $conn (bridge) , (comma) 
                       "SELECT..." (instruction) ) ends. ; (semicolon). */

                    $dlist = []; while($d = mysqli_fetch_assoc($drvs)) { $dlist[] = $d; }
                    // [101] $ (dollar) variable. dlist array. while loop builds the list of drivers. ; (semicolon).

                    $res = mysqli_query($conn, "SELECT b.*, d.full_name as driver_name FROM buses b LEFT JOIN drivers d ON b.driver_id = d.driver_id ORDER BY b.bus_id DESC");
                    // [102] $ (dollar) variable. res label. gets buses and driver names from database. ; (semicolon).

                    while($row = mysqli_fetch_assoc($res)): $bid = $row['bus_id'];
                        /* while (while) starts a loop. $row (row container) pulls data. 
                           mysqli_fetch_assoc (fetch associative) converts raw data 
                           into labeled pieces for easy use. ( starts. $res (result source). ) ends. */
                    ?>

                    <tr>
                    <!-- [104] <tr> starts a data row. -->

                        <form method="POST">
                        <!-- [105] <form> for updating this row. -->

                            <input type="hidden" name="bus_id" value="<?= $bid ?>">
                            <!-- [106] <input> (hidden) stores the bus ID. -->

                            <td><?= $bid ?></td>
                            <!-- [107] <td> (cell) prints the ID. -->

                            <td>
                                <span class="view-bus-<?= $bid ?>"><?= htmlspecialchars($row['bus_name']) ?></span>
                                <!-- html (HyperText) special (special) chars (characters) is a security tool 
                                     that encodes text for safety. ( starts the tool. $row (data row) 
                                     ['bus_name'] (vehicle label) ) ends. -->
                                <!-- [108] <span> (label) shows bus name. -->

                                <input type="text" name="bus_name" value="<?= htmlspecialchars($row['bus_name']) ?>" class="edit-bus-<?= $bid ?>" style="display:none;">
                                <!-- [109] <input> (textbox) for editing name (hidden by default). -->
                            </td>

                            <td>
                                <span class="view-bus-<?= $bid ?>"><?= htmlspecialchars($row['reg_no']) ?></span>
                                <!-- [110] <span> shows plate number. -->

                                <input type="text" name="reg_no" value="<?= htmlspecialchars($row['reg_no']) ?>" class="edit-bus-<?= $bid ?>" style="display:none;">
                                <!-- [111] <input> for editing plate. -->
                            </td>

                            <td>
                                <span class="view-bus-<?= $bid ?>"><?= $row['max_passengers'] ?></span>
                                <!-- [112] <span> shows capacity. -->

                                <input type="text" name="max_passengers" value="<?= $row['max_passengers'] ?>" class="edit-bus-<?= $bid ?>" style="display:none; width:50px;">
                                <!-- [113] <input> for editing capacity. -->
                            </td>

                            <td>
                                <select name="driver_id" class="input" style="padding:5px; width:auto;">
                                <!-- [114] <select> (dropdown) for driver. -->

                                    <option value="">-- No Driver --</option>
                                    <!-- [115] <option> (choice) for no driver. -->

                                    <?php foreach($dlist as $d): ?>
                                    <!-- [116] foreach loops through the driver list. -->

                                    <option value="<?= $d['driver_id'] ?>" <?= ($d['driver_id'] == $row['driver_id'] ? 'selected' : '') ?>><?= htmlspecialchars($d['full_name']) ?></option>
                                    <!-- [117] <option> choice with logic to mark as 'selected'. -->

                                    <?php endforeach; ?>
                                    <!-- [118] End driver loop. -->

                                </select>
                                <!-- [119] </select> ends dropdown. -->

                                <button type="submit" name="update_bus" class="action-btn" style="background:#48bb78; margin:0;">Save</button>
                                <!-- [120] <button> saves driver assignment. -->
                            </td>

                            <td>
                                <div id="ops-m-<?= $bid ?>">
                                <!-- < (less than sign) div (box) id (identity) = "ops-m- [echo] $bid" (unique number) > starts the visible action box. -->

                                    <button type="button" class="action-btn btn-update" onclick="toggleBusEdit(<?= $bid ?>)">Edit</button>
                                    <!-- < (less than sign) button (clickable item) type (nature) = "button" (does not submit) 
                                         class (style) = "action-btn (standard look) btn-update (blue color)" 
                                         onclick (on click event) = "toggleBusEdit (run the toggle tool) ( [echo] $bid (for this bus) )" 
                                         > (greater than sign) Edit (label) < / (slash) button > (ends item). -->

                                    <a href="?remove_bus=<?= $bid ?>" class="action-btn btn-delete" onclick="return confirm('Delete?')">Del</a>
                                    <!-- < (less than sign) a (anchor link) href (destination) = "?remove_bus = [echo] $bid" (sends ID to URL) 
                                         class (style) = "action-btn (standard look) btn-delete (red color)" 
                                         onclick (on click event) = "return confirm (ask a question) ( 'Delete?' )" 
                                         > (greater than sign) Del (label) < / (slash) a > (ends link). -->

                                </div>
                                <!-- < / (slash) div > ends main button box. -->

                                <div id="ops-s-<?= $bid ?>" style="display:none;">
                                <!-- < (less than sign) div (box) id (identity) = "ops-s- [echo] $bid" (unique number) 
                                     style (visual) = "display:none;" (starts hidden) > starts the edit action box. -->

                                    <button type="submit" name="update_bus" class="action-btn btn-update">Update</button>
                                    <!-- < (less than sign) button (clickable item) type (nature) = "submit" (sends the form data) 
                                         name (server label) = "update_bus" (tells PHP which logic to run) 
                                         class (style) = "action-btn (standard look) btn-update (blue color)" 
                                         > (greater than sign) Update (label) < / (slash) button > (ends item). -->

                                    <button type="button" class="action-btn btn-delete" onclick="toggleBusEdit(<?= $bid ?>)">Cancel</button>
                                    <!-- < (less than sign) button (clickable item) type (nature) = "button" (does not submit) 
                                         class (style) = "action-btn (standard look) btn-delete (red color)" 
                                         onclick (on click event) = "toggleBusEdit (run the toggle tool) ( [echo] $bid (for this bus) )" 
                                         > (greater than sign) Cancel (label) < / (slash) button > (ends item). -->

                                </div>
                                <!-- < / (slash) div > ends edit button box. -->
                            </td>

                        </form>
                        <!-- [129] </form> ends inline form. -->

                    </tr>
                    <!-- [130] </tr> ends data row. -->

                    <?php endwhile; ?>
                    <!-- [131] End of fleet while loop. -->

                </tbody>
                <!-- [132] </tbody> ends data body. -->

            </table>
            <!-- [133] </table> ends data grid. -->

        </div>
        <!-- [134] </div> ends content box. -->

    </div>
    <!-- [135] </div> ends main wrapper. -->

    <div style="height: 100px;"></div>
    <!-- [136] <div> bottom spacer box. -->

    <script src="js/footer.js"></script>
    <!-- [137] <script> imports footer logic. -->

</body>
<!-- [138] </body> ends visible content. -->

</html>
<!-- [139] </html> ends document structure. -->

<?php mysqli_close($conn); ?>
<!-- [140] mysqli_close shuts the database bridge. ; (semicolon). -->
