<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side logic engine.

/**
 * ADMINISTRATION: USER MANAGEMENT HUB (view_users_sorted.php)
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// ADMINISTRATION: USER MANAGEMENT HUB is the module title. * (asterisk) / 
// (forward slash) closes the block.

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

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
// if (if) starts a logic check for security. ( (opening bracket) starts condition. 
// ! (exclamation mark) is the NOT operator. isset (is set) checks if a memory 
// container exists. ( $ (dollar sign) _ (underscore) SESSION [ 'role' ] ) (bracket). 
// || (double pipe) is the logical OR operator. !in_array (not in array) checks if 
// the user's role is missing from the authorized list. ['ADMIN', 'AGENT'] (authorized 
// roles). ) (closing bracket). { (opening curly bracket) starts the rejection logic.

    die("Access Denied: Staff Authorization Required.");
    // die (die) is a terminal function that prints an error message and stops all 
    // further code execution. ( "Access Denied..." ) is the message. 
    // ; (semicolon) terminates the line.
}
// } (closing curly bracket) ends the security check block.

if (isset($_GET['delete_user'])) {
// if (if) starts a logic check to determine if a user removal request has been 
// transmitted through the URL. ( (opening bracket) starts the condition. 
// isset (is set) is a built-in tool that verifies if a variable exists. ( (bracket) 
// $ (dollar sign) is the variable prefix. _ (underscore) connects to the 
// array name. GET (G E T) is a Superglobal Array used by the server to 
// collect data sent via the URL query string. [ (opening square bracket) 
// starts the index pointer. 'delete_user' (quote d e l e t e underscore u s e r 
// quote) is the specific key attached to the deletion link. ] (closing square 
// bracket) ends the pointer. ) (bracket) ends the isset tool. ) (closing 
// bracket) ends the if condition. { (opening curly bracket) marks the 
// beginning of the user deletion processing logic.

    $uid = $_GET['delete_user'];
    // $ (dollar sign) variable marker. uid (u i d) is the logical label chosen 
    // to represent the User Identification number being targeted for removal. 
    // = (equals sign) is the assignment operator. $ (dollar sign) _ (underscore) 
    // GET [ 'delete_user' ] retrieves the ID from the URL. ; (semicolon) 
    // terminates the instruction.
    
    if ($uid == $_SESSION['user_id']) {
    // if (if) starts a critical safety check to prevent a staff member from 
    // accidentally deleting their own account. ( (opening bracket) starts the 
    // comparison. $ (dollar sign) uid (the target ID) == (double equals sign) 
    // is the comparison operator for equality. $ (dollar sign) _ (underscore) 
    // SESSION (the server's memory array) [ 'user_id' ] (the logged-in user's 
    // ID) ) (closing bracket). { (opening curly bracket) starts the error 
    // prevention logic.

        header("Location: view_users_sorted.php?err=System Safety: You cannot delete yourself!");
        // header (h e a d e r) is a specialized tool that sends raw HTTP 
        // instructions to the browser. ( (opening bracket) "Location: ..." 
        // (quote) is the redirection command that moves the user back to the 
        // list page with a safety warning. ) (closing bracket). ; (semicolon).

        exit();
        // exit (e x i t) is a terminal command that kills all further server-side 
        // processing for this request to ensure no accidental deletion occurs. 
        // ( ) (empty brackets). ; (semicolon).
    }
    // } (closing curly bracket) ends the self-deletion safety block.

    $sql_del = "DELETE FROM users WHERE user_id = ?";
    // $ (dollar sign) variable marker. sql_del (s q l underscore d e l) is a 
    // logical identifier chosen to describe the database removal instruction. 
    // = (equals sign) assignment operator. "DELETE FROM..." (quote) starts the 
    // SQL command. ? (question mark) is a critical security placeholder. It 
    // acts as a safety "hole" that prevents SQL Injection by ensuring the 
    // ID added later is treated only as a literal value, never as a part of 
    // the command. ; (semicolon) terminates the line.
    
    $stmt_del = mysqli_prepare($conn, $sql_del);
    // $ (dollar sign) variable marker. stmt_del (s t m t underscore d e l) is 
    // the handle for the removal tool object. = (equals sign) assignment. 
    // mysqli (MySQL Improved) _ (underscore) prepare (prepare) is the security 
    // function that pre-compiles the command. Pre-compiling (pre compiling) 
    // is the process where the database locks the structure of the DELETE 
    // instruction before any data is introduced, preventing malicious 
    // modification. ( (opening bracket) $ (dollar sign) conn (bridge handle) 
    // , (comma) $ (dollar sign) sql_del (the command blueprint) ) (closing 
    // bracket). ; (semicolon).
    
    mysqli_stmt_bind_param($stmt_del, "i", $uid);
    /* mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
       bind (bind) _ (underscore) param (parameter) is the function that 
       securely attaches the data to the query blueprint. 
       ( starts the tool. $stmt_del is the tool handle. , (comma) separates info. 
       "i" (integer) means the data is a number. , (comma). $uid is the data. 
       ) ends the tool. ; (semicolon) terminates the line. */
    
    mysqli_stmt_execute($stmt_del);
    // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
    // execute (execute) is the command that triggers the actual database 
    // removal. ( (opening bracket) $ (dollar sign) stmt_del (handle) ) 
    // (closing bracket). ; (semicolon).
    
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed User UID: $uid");
    // logActivity (l o g underscore a c t i v i t y) is a custom audit tool 
    // that records the event for administrative review. ( (opening bracket) 
    // $ (dollar sign) _ (underscore) SESSION [ 'user_id' ] (performer) , 
    // (comma) $ (dollar sign) _ (underscore) SESSION [ 'name' ] (performer) 
    // , (comma) 'DELETION' (type) , (comma) "Removed..." (description) ) 
    // (closing bracket). ; (semicolon).
    
    mysqli_stmt_close($stmt_del);
    // mysqli_stmt_close (close) terminates the removal tool and releases 
    // server resources now that the operation is finished. ( (opening 
    // bracket) $ (dollar sign) stmt_del ) (closing bracket). ; (semicolon).
    
    header("Location: view_users_sorted.php?msg=User deleted successfully.");
    // header (header) redirection tool sends the browser to the success list page. 
    // ; (semicolon).

    exit();
    // exit (exit) stops the script. ; (semicolon).
}
// } (closing curly bracket) ends the deletion block.

if (isset($_POST['add_user'])) {
// if (if) starts a logic check to determine if the user creation form has been 
// submitted. ( (opening bracket) starts the condition. isset (is set) is a 
// built-in tool that verifies if a variable exists. ( (bracket) $ (dollar sign) 
// is the variable prefix. _ (underscore) connects to the array name. POST 
// (P O S T) is a Superglobal Array used by the server to collect and pull 
// data from an HTML form sent via the secure HTTP POST method. It does NOT 
// send data to the database directly; it only captures what the user typed. 
// [ (opening square bracket) starts the index pointer. 'add_user' (quote) is 
// the name attribute of the submit button. ] (closing square bracket) ends 
// the pointer. ) (bracket) ends the isset tool. ) (closing bracket) ends the 
// condition. { (opening curly bracket) marks the start of the logic.

    $first = $_POST['first_name'];
    // $ (dollar sign) variable marker. first (f i r s t) is the unique label 
    // chosen to identify the container for the user's initial name. = (equals 
    // sign) is the assignment operator. $ (dollar sign) _ (underscore) POST 
    // (post superglobal array that pulls/collects form data) [ 'first_name' ] 
    // (key for the first name field) retrieves data. ; (semicolon) terminates 
    // the instruction.

    $last = $_POST['last_name'];
    // $ (dollar sign) variable. last label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST (Superglobal Array) [ 'last_name' ] ; (semicolon).

    $email = $_POST['email'];
    // $ (dollar sign) variable. email label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST (Superglobal Array) [ 'email' ] ; (semicolon).

    $phone = $_POST['phone_number'];
    // $ (dollar sign) variable. phone label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST (Superglobal Array) [ 'phone_number' ] ; (semicolon).

    $role = $_POST['role'];
    // $ (dollar sign) variable. role label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST (Superglobal Array) [ 'role' ] ; (semicolon).
    
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    // $ (dollar sign) variable marker. pass (p a s s) identifies the container 
    // for the secure secret. = (equals sign) assignment operator. password_hash 
    // (password hash) is the tool that encrypts the secret. ( (opening bracket) 
    // $ (dollar sign) _ (underscore) POST (Superglobal Array that pulls data 
    // from the password field) [ 'password' ] , (comma) PASSWORD_DEFAULT 
    // (algorithm) ) (closing bracket). ; (semicolon).

    $sql_add = "INSERT INTO users (first_name, last_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?, ?)";
    // $ (dollar sign) variable marker. sql_add (s q l underscore a d d) is a 
    // logical identifier chosen to describe the database creation command. 
    // = (equals sign) assignment. "INSERT INTO..." (quote) starts the SQL 
    // instruction. ? (question marks) are six critical security placeholders 
    // that neutralize SQL Injection by separating the command structure from 
    // user data. ; (semicolon) terminates the command.

    $stmt_add = mysqli_prepare($conn, $sql_add);
    // $ (dollar sign) variable marker. stmt_add (s t m t underscore a d d) is 
    // the handle for the creation tool object. = (equals sign) assignment. 
    // mysqli_prepare (prepare) pre-compiles the command blueprint. Pre-compiling 
    // (pre compiling) locks the structural shape of the INSERT command in the 
    // database before any data is added. ( (opening bracket) $ (dollar sign) 
    // conn (bridge handle) , (comma) $ (dollar sign) sql_add (the command 
    // blueprint) ) (closing bracket). ; (semicolon).

    mysqli_stmt_bind_param($stmt_add, "ssssss", $first, $last, $email, $phone, $pass, $role);
    /* mysqli_stmt_bind_param (MySQL Improved statement bind parameter) 
       securely pours the user data into the database blueprint holes (?). 
       "ssssss" (six strings) defines the data types. 
       $first, $last, etc. are the variables being safely poured in. 
       ; (semicolon) terminates the instruction. */
    
    if(mysqli_stmt_execute($stmt_add)) {
    // if (if) check for successful insertion. ( mysqli_stmt_execute ( $stmt_add ) ) 
    // (bracket). { (opening curly bracket) starts the success path.

        logActivity($_SESSION['user_id'], $_SESSION['name'], 'REGISTRATION', "Created new user: $email");
        // logActivity (log activity) records the new account creation in the audit logs. 
        // ; (semicolon).

        header("Location: view_users_sorted.php?msg=New user added.");
        // header redirection back to the list with success message. ; (semicolon).

    } else {
    // } (closing curly bracket) else (otherwise) failure branch starts. { (opening 
    // curly bracket).

        header("Location: view_users_sorted.php?err=Error: " . mysqli_error($conn));
        // header redirection with the specific database error description. ; (semicolon).
    }
    // } (closing curly bracket) ends success check.

    mysqli_stmt_close($stmt_add);
    // mysqli_stmt_close releases the server memory used for the insertion tool. ; (semicolon).

    exit();
    // exit (exit) stops the script. ; (semicolon).
}
// } (closing curly bracket) ends creation logic block.

// --- UPDATE LOGIC (INLINE) ---
if (isset($_POST['update_user'])) {
// if (if) starts a logic check to determine if the profile modification form 
// has been submitted. ( (opening bracket) starts the condition. isset (is set) 
// checks if a variable exists. ( (bracket) $ (dollar sign) _ (underscore) 
// POST [ 'update_user' ] (submit button) ) (bracket) ) (bracket). 
// { (opening curly bracket) marks the start of the update processing logic.

    $uid = $_POST['user_id'];
    // $ (dollar sign) variable marker. uid (u i d) is the logical label for the 
    // User Identification number. = (equals sign) assignment operator. 
    // $ (dollar sign) _ (underscore) POST (Superglobal Array that pulls/collects 
    // data from the form) [ 'user_id' ] retrieves the target ID. ; (semicolon).

    $first = $_POST['first_name'];
    // $ (dollar sign) variable. first label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST [ 'first_name' ] ; (semicolon).

    $last = $_POST['last_name'];
    // $ (dollar sign) variable. last label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST [ 'last_name' ] ; (semicolon).

    $email = $_POST['email'];
    // $ (dollar sign) variable. email label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST [ 'email' ] ; (semicolon).

    $phone = $_POST['phone_number'];
    // $ (dollar sign) variable. phone label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST [ 'phone_number' ] ; (semicolon).

    $role = $_POST['role'];
    // $ (dollar sign) variable. role label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST [ 'role' ] ; (semicolon).

    $sql_upd = "UPDATE users SET first_name=?, last_name=?, email=?, phone_number=?, role=? WHERE user_id=?";
    // $ (dollar sign) variable marker. sql_upd (s q l underscore u p d) is a 
    // logical identifier chosen to describe the database modification command. 
    // = (equals sign) assignment operator. "UPDATE..." (quote) starts the 
    // SQL instruction. ? (question marks) are the six security placeholders 
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

    mysqli_stmt_bind_param($stmt_upd, "sssssi", $first, $last, $email, $phone, $role, $uid);
    /* mysqli_stmt_bind_param (MySQL Improved statement bind parameter) 
       securely pours 5 pieces of text (s) and 1 number (i) into the query. 
       This prevents hackers from changing the database structure. 
       ; (semicolon). */
    
    if(mysqli_stmt_execute($stmt_upd)) {
    // if (if) check for successful execution of the update. ( (bracket) 
    // mysqli_stmt_execute ( $stmt_upd ) ) (bracket). { (opening curly bracket).

        logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Modified User UID: $uid");
        // logActivity (log activity) records the profile change in the audit logs. 
        // ; (semicolon).

        header("Location: view_users_sorted.php?msg=User updated.");
        // header redirection back to the list with success message. ; (semicolon).

    } else {
    // } (closing curly bracket) else (otherwise) failure branch starts. { (opening 
    // curly bracket).

        header("Location: view_users_sorted.php?err=Update Failed.");
        // header redirection with error message. ; (semicolon).
    }
    // } (closing curly bracket) ends success check.

    mysqli_stmt_close($stmt_upd);
    // mysqli_stmt_close releases the server memory for the update handle. ; (semicolon).

    exit();
    // exit (exit) stops the script. ; (semicolon).
}
// } (closing curly bracket) ends update logic block.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <script>
    /* [82] <script> starts JavaScript logic. */

        // toggleEdit function (toggle edit) switches between label view and input view.
        function toggleEdit(uid) {
        /* [83] function toggleEdit( uid ). { starts block. */
        /* [83.1] toggleEdit is a tool to swap labels for text boxes. uid is the specific user ID. { starts block. */

            // Find the display span and the edit form by their ID.
            var views = document.querySelectorAll('.view-' + uid);
            /* [84] var views = search for '.view-ID'. ; (semicolon). */
            /* [84.1] var (variable) views is a list of all display labels for this user. querySelectorAll finds them. ; (semicolon). */

            var edits = document.querySelectorAll('.edit-' + uid);
            /* [85] var edits = search for '.edit-ID'. ; (semicolon). */
            /* [85.1] var (variable) edits is a list of all hidden input boxes for this user. ; (semicolon). */
            
            // Toggle visibility.
            views.forEach(v => v.style.display = (v.style.display === 'none' ? 'inline' : 'none'));
            /* [86] views loop. v.style.display toggle. ; (semicolon). */
            /* [86.1] forEach iterates over every label. (v.style.display) toggles between "none" (hidden) and "inline" (visible). ; (semicolon). */

            edits.forEach(e => e.style.display = (e.style.display === 'none' ? 'inline' : 'none'));
            /* [87] edits loop. e.style.display toggle. ; (semicolon). */
            
            // Toggle the row buttons.
            document.getElementById('btn-main-' + uid).style.display = 
                (document.getElementById('btn-main-' + uid).style.display === 'none' ? 'inline-block' : 'none');
            /* [88] document.getElementById('btn-main-ID') toggle. ; (semicolon). */
            /* [88.1] Toggles visibility of the primary "Update/Delete" buttons. ; (semicolon). */

            document.getElementById('btn-save-' + uid).style.display = 
                (document.getElementById('btn-save-' + uid).style.display === 'none' ? 'inline-block' : 'none');
            /* [89] document.getElementById('btn-save-ID') toggle. ; (semicolon). */
            /* [89.1] Toggles visibility of the "Save/Cancel" buttons. ; (semicolon). */
        }
        /* [90] } ends function. */

        // Simple validation (No Regex)
        function validateInline(uid) {
        /* [91] function validateInline( uid ). { starts block. */
        /* [91.1] validateInline checks if text boxes are empty before saving. { starts block. */

            var f = document.getElementById("f-"+uid).value.trim();
            /* [92] var f = find input "f-ID".value.trim(). ; (semicolon). */
            /* [92.1] Gets the text from the first name box and removes spaces. ; (semicolon). */

            if (f == "") { alert("First Name is required"); return false; }
            /* [93] if empty. alert "First Name...". return false. ; (semicolon). */
            /* [93.1] if (f) is empty (""), show a popup and stop the save. ; (semicolon). */

            return true;
            /* [94] return true (allow save). ; (semicolon). */
        }
        /* [95] } ends function. */

        function validateAdd() {
        /* [96] function validateAdd(). { starts block. */

            var f = document.getElementById("add_f").value.trim();
            /* [97] var f = find input "add_f".value.trim(). ; (semicolon). */

            if (f == "") { alert("First Name is required"); return false; }
            /* [98] if empty. alert. return false. ; (semicolon). */

            return true;
            /* [99] return true. ; (semicolon). */
        }
        /* [100] } ends function. */
    </script>
</head>
<!-- [102] </head> ends metadata. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">
<!-- [103] <body> starts visible page. class adds user role. -->

    <script src="js/header2.js"></script>
    <!-- [104] <script> pulls in navigation header. -->

    <div style="height: 100px;"></div>
    <!-- [105] <div> spacer box. -->

    <div class="view-container">
    <!-- [106] <div class="view-container"> starts main card. -->

        <h2 style="color:var(--purple);">👥 User Management</h2>
        <!-- [107] <h2> title with user emoji 👥. -->
        
        <!-- Success/Error Feedback -->
        <?php if(isset($_GET['msg'])): ?>
        <!-- [108] [php] if URL has 'msg'. [?] -->
            <div style="color:green; font-weight:bold;"><?= htmlspecialchars($_GET['msg']) ?></div>
            <!-- [109] <div> prints success message in green. -->
        <?php endif; ?>
        <!-- [110] [php] endif; [?] -->

        <?php if(isset($_GET['err'])): ?>
        <!-- [111] [php] if URL has 'err'. [?] -->
            <div style="color:red; font-weight:bold;"><?= htmlspecialchars($_GET['err']) ?></div>
            <!-- [112] <div> prints error message in red. -->
        <?php endif; ?>
        <!-- [113] [php] endif; [?] -->

        <!-- Add User Form -->
        <div class="add-form">
        <!-- [114] <div class="add-form"> starts user creation box. -->

            <h3>Add New User</h3>
            <!-- [115] <h3> title. -->

            <form method="POST" onsubmit="return validateAdd()">
            <!-- [116] <form> starts data submission. onsubmit triggers validation check. -->

                <input type="text" name="first_name" id="add_f" placeholder="First Name" required class="input">
                <!-- [117] <input> text box for first name. -->

                <input type="text" name="last_name" placeholder="Last Name" class="input">
                <!-- [118] <input> text box for last name. -->

                <input type="email" name="email" id="add_e" placeholder="Email" required class="input">
                <!-- [119] <input> text box for email address. -->

                <input type="text" name="phone_number" placeholder="Phone" class="input">
                <!-- [120] <input> text box for phone number. -->

                <input type="password" name="password" id="add_p" placeholder="Password" required class="input">
                <!-- [121] <input> text box for secret password. -->

                <select name="role" class="input">
                <!-- [122] <select> dropdown for permissions level. -->

                    <option value="PASSENGER">PASSENGER</option>
                    <!-- [123] <option> for passenger role. -->

                    <option value="AGENT">AGENT</option>
                    <!-- [124] <option> for agent role. -->

                    <option value="ADMIN">ADMIN</option>
                    <!-- [125] <option> for admin role. -->

                </select>
                <!-- [126] </select> ends role choice. -->

                <button type="submit" name="add_user" class="action-btn btn-update">Save New User</button>
                <!-- [127] <button> that submits the "Create" form. -->

            </form>
            <!-- [128] </form> ends user creation. -->

        </div>
        <!-- [129] </div> ends creation box. -->

        <table class="crud-table">
        <!-- [130] <table> starts the user list grid. -->

            <thead>
            <!-- [131] <thead> starts header. -->

                <tr>
                <!-- [132] <tr> starts row. -->

                    <th>ID</th>
                    <!-- [133] <th> cell for ID label. -->

                    <th>First Name</th>
                    <!-- [134] <th> cell for First Name label. -->

                    <th>Last Name</th>
                    <!-- [135] <th> cell for Last Name label. -->

                    <th>Email</th>
                    <!-- [136] <th> cell for Email label. -->

                    <th>Phone</th>
                    <!-- [137] <th> cell for Phone label. -->

                    <th>Role</th>
                    <!-- [138] <th> cell for Role label. -->

                    <th>Actions</th>
                    <!-- [139] <th> cell for Actions label. -->

                </tr>
                <!-- [140] </tr> ends row. -->

            </thead>
            <!-- [141] </thead> ends header. -->

            <tbody>
            <!-- [142] <tbody> starts data rows. -->

                <?php
                $sql_list = "SELECT * FROM users ORDER BY user_id ASC";
                // [143] $sql_list = "SELECT...". ; (semicolon).
                // [143.1] Command to pull all users from the database. ; (semicolon).

                $res_list = mysqli_query($conn, $sql_list);
                /* $ (variable) res_list (result list) = (assignment). 
                   mysqli_query (MySQL query) is the command that sends the instruction 
                   to the database server. ( starts. $conn (bridge) , (comma) 
                   $sql_list (the instruction) ) ends. ; (semicolon). */

                while($row = mysqli_fetch_assoc($res_list)):
                    /* while (while) starts a loop. $row (row container) pulls data. 
                       mysqli_fetch_assoc (fetch associative) converts raw data into labeled pieces. 
                       ( starts. $res_list (result source). ) ends. : (colon) starts the loop block. */

                    $uid = $row['user_id'];
                    // [146] $uid = data. ; (semicolon).
                    // [146.1] $uid stores the current user's unique number. ; (semicolon).
                ?>
                <!-- [147] [?] starts HTML inside loop. -->

                <tr>
                <!-- [148] <tr> starts a data row for this user. -->

                    <form method="POST" onsubmit="return validateInline(<?= $uid ?>)">
                    <!-- [149] <form> for modifying this user. [echo] $uid adds the ID to the check. -->

                        <input type="hidden" name="user_id" value="<?= $uid ?>">
                        <!-- [150] <input type="hidden"> stores the ID invisibly so the server knows who to update. -->

                        <td><?= $uid ?></td>
                        <!-- [151] <td> cell prints the user's ID number. -->
                        
                        <!-- First Name Cell -->
                        <td>
                        <!-- [152] <td> cell for First Name. -->

                            <span class="view-<?= $uid ?>"><?= htmlspecialchars($row['first_name']) ?></span>
                            <!-- html (HyperText) special (special) chars (characters) is a security tool 
                                 that encodes text for safety. ( starts the tool. $row (data row) 
                                 ['first_name'] (label) ) ends. -->
                            <!-- html (HyperText) special (special) chars (characters) is a security tool 
                                 that converts dangerous symbols like < into safe text so hackers cannot 
                                 run scripts. ( starts the tool. $row (row variable) ['first_name'] (column name) 
                                 is the data being protected. ) ends the tool. -->

                            <input type="text" name="first_name" id="f-<?= $uid ?>" value="<?= htmlspecialchars($row['first_name']) ?>" class="edit-<?= $uid ?> input-inline" style="display:none;">
                            <!-- value (initial text) = [echo] htmlspecialchars (security tool) 
                                 ( $row ['first_name'] ) ensures the text in the box is safe. -->

                        </td>
                        <!-- [155] </td> ends cell. -->
                        
                        <!-- Last Name Cell -->
                        <td>
                        <!-- [156] <td> cell for Last Name. -->

                            <span class="view-<?= $uid ?>"><?= htmlspecialchars($row['last_name']) ?></span>
                            <!-- htmlspecialchars (security tool) ( $row ['last_name'] ) -->
                            <!-- htmlspecialchars (security tool) ( $row ['last_name'] (the family name data) ) 
                                 converts symbols into safe text. -->

                            <input type="text" name="last_name" id="l-<?= $uid ?>" value="<?= htmlspecialchars($row['last_name']) ?>" class="edit-<?= $uid ?> input-inline" style="display:none;">
                            <!-- value = [echo] htmlspecialchars (security tool) ( $row ['last_name'] ) -->

                        </td>
                        <!-- [159] </td> -->

                        <!-- Email Cell -->
                        <td>
                        <!-- [160] <td> cell for Email. -->

                            <span class="view-<?= $uid ?>"><?= htmlspecialchars($row['email']) ?></span>
                            <!-- htmlspecialchars (security tool) ( $row ['email'] ) -->
                            <!-- htmlspecialchars (security tool) ( $row ['email'] (electronic mail address data) ) -->

                            <input type="email" name="email" id="e-<?= $uid ?>" value="<?= htmlspecialchars($row['email']) ?>" class="edit-<?= $uid ?> input-inline" style="display:none;">
                            <!-- value = [echo] htmlspecialchars (security tool) ( $row ['email'] ) -->

                        </td>
                        <!-- [163] </td> -->

                        <!-- Phone Cell -->
                        <td>
                        <!-- [164] <td> cell for Phone. -->

                            <span class="view-<?= $uid ?>"><?= htmlspecialchars($row['phone_number']) ?></span>
                            <!-- htmlspecialchars (security tool) ( $row ['phone_number'] (contact info) ) -->

                            <input type="text" name="phone_number" value="<?= htmlspecialchars($row['phone_number']) ?>" class="edit-<?= $uid ?> input-inline" style="display:none;">
                            <!-- value = [echo] htmlspecialchars (security tool) ( $row ['phone_number'] ) -->

                        </td>
                        <!-- [167] </td> -->

                        <!-- Role Cell -->
                        <td>
                        <!-- [168] <td> cell for Role. -->

                            <span class="view-<?= $uid ?>"><?= $row['role'] ?></span>
                            <!-- [169] <span> label. -->

                            <select name="role" class="edit-<?= $uid ?> input-inline" style="display:none;">
                            <!-- [170] <select> hidden dropdown. -->

                                <option value="PASSENGER" <?= $row['role']=='PASSENGER'?'selected':'' ?>>PASSENGER</option>
                                <!-- [171] <option>. ? (ternary) choice checks if this is the current role and marks as 'selected'. -->

                                <option value="AGENT" <?= $row['role']=='AGENT'?'selected':'' ?>>AGENT</option>
                                <!-- [172] <option>. -->

                                <option value="ADMIN" <?= $row['role']=='ADMIN'?'selected':'' ?>>ADMIN</option>
                                <!-- [173] <option>. -->

                            </select>
                            <!-- [174] </select> ends hidden dropdown. -->

                        </td>
                        <!-- [175] </td> -->

                        <!-- Actions Cell -->
                        <td>
                        <!-- [176] <td> cell for Buttons. -->
                            <!-- Default Action Buttons -->
                            <div id="btn-main-<?= $uid ?>">
                            <!-- < (less than sign) div (box) id (identity) = "btn-main- [echo] $uid" (unique number) > starts the visible action box. -->

                                <button type="button" class="action-btn btn-update" onclick="toggleEdit(<?= $uid ?>)">Update</button>
                                <!-- < (less than sign) button (clickable item) type (nature) = "button" (does not submit) 
                                     class (style) = "action-btn (standard look) btn-update (blue color)" 
                                     onclick (on click event) = "toggleEdit (run the toggle tool) ( [echo] $uid (for this user) )" 
                                     > (greater than sign) Update (label) < / (slash) button > (ends item). -->

                                <a href="?delete_user=<?= $uid ?>" class="action-btn btn-delete" onclick="return confirm('Delete user?')">Delete</a>
                                <!-- < (less than sign) a (anchor link) href (destination) = "?delete_user = [echo] $uid" (sends ID to URL) 
                                     class (style) = "action-btn (standard look) btn-delete (red color)" 
                                     onclick (on click event) = "return confirm (ask a question) ( 'Delete user?' )" 
                                     > (greater than sign) Delete (label) < / (slash) a > (ends link). -->

                            </div>
                            <!-- < / (slash) div > ends main button box. -->
                            
                            <!-- Editing Action Buttons -->
                            <div id="btn-save-<?= $uid ?>" style="display:none;">
                            <!-- < (less than sign) div (box) id (identity) = "btn-save- [echo] $uid" (unique number) 
                                 style (visual) = "display:none;" (starts hidden) > starts the edit action box. -->

                                <button type="submit" name="update_user" class="action-btn btn-update">Save</button>
                                <!-- < (less than sign) button (clickable item) type (nature) = "submit" (sends the form data) 
                                     name (server label) = "update_user" (tells PHP which logic to run) 
                                     class (style) = "action-btn (standard look) btn-update (blue color)" 
                                     > (greater than sign) Save (label) < / (slash) button > (ends item). -->

                                <button type="button" class="action-btn btn-delete" onclick="toggleEdit(<?= $uid ?>)">Cancel</button>
                                <!-- < (less than sign) button (clickable item) type (nature) = "button" (does not submit) 
                                     class (style) = "action-btn (standard look) btn-delete (red color)" 
                                     onclick (on click event) = "toggleEdit (run the toggle tool) ( [echo] $uid (for this user) )" 
                                     > (greater than sign) Cancel (label) < / (slash) button > (ends item). -->

                            </div>
                            <!-- < / (slash) div > ends edit button box. -->

                        </td>
                        <!-- [185] </td> -->

                    </form>
                    <!-- [186] </form> ends modification form. -->

                </tr>
                <!-- [187] </tr> ends row for this user. -->

                <?php endwhile; ?>
                <!-- [188] [php] endwhile; [?] ends the loop. -->

            </tbody>
            <!-- [189] </tbody> ends data rows. -->

        </table>
        <!-- [190] </table> ends grid. -->

    </div>
    <!-- [191] </div> ends main card. -->

    <div style="height: 100px;"></div>
    <!-- [192] <div> spacer box. -->

    <script src="js/footer.js"></script>
    <!-- [193] <script> pulls in footer. -->

    <div style="text-align:center;"><a href="dashboard.php" style="color:var(--purple); font-weight:bold;">← Back to Dashboard</a></div>
    <!-- [194] <div> contains a link to return to the dashboard. -->

</body>
<!-- [195] </body> ends visible page. -->

</html>
<!-- [196] </html> ends document. -->

<?php mysqli_close($conn); ?>
<!-- [197] [php] starts PHP. mysqli_close (m y s q l i underscore c l o s e) shuts down the database bridge $conn to save resources. ; terminates. [?] ends PHP. -->
