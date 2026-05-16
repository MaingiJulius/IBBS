<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side logic engine.

/**
 * ACCOUNT ENGINE (account2.php)
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// ACCOUNT ENGINE is the module title. * (asterisk) / (forward slash) closes the block.

error_reporting(E_ALL);
// error_reporting (error reporting) is a tool that sets which system issues to 
// track. ( (opening bracket) starts the input. E_ALL (Error All) is a machine constant 
// that tells the server to watch for every single error, warning, and notice. ) 
// (closing bracket) ends the input. ; (semicolon) is the instruction terminator.

ini_set('display_errors', '1');
// ini_set (i n i underscore s e t) is an initialization tool that modifies 
// the server's internal configuration settings at runtime (while the 
// script is running). It is used to force the system to display all 
// hidden errors and issues immediately and directly on the web browser 
// screen so the developer can see exactly where a problem occurs. 
// ( (opening bracket) starts the instruction. 'display_errors' (quote) 
// is the specific directive to enable the visual error reports. , 
// (comma) separates the setting from the command. '1' (quote) is the 
// numeric trigger for "ON" or "True". ) (closing bracket) ends the 
// command. ; (semicolon) terminates the instruction.

session_start();
// session_start (session start) is the command that activates the server's memory 
// to track the user across different pages. ( ) (empty brackets) execute the tool. 
// ; (semicolon) terminates the instruction.

require_once 'db_connection.php';
// require_once (require once) is a directive that imports the database bridge file and 
// ensures it is only loaded one time to prevent errors. 'db_connection.php' (quote db 
// underscore connection dot php quote) is the file path. ; (semicolon) terminates the line.

if (isset($_POST['save'])) {
// if (if) starts a logic check to determine if the registration form has been 
// submitted. ( (opening bracket) starts the condition. isset (is set) is a 
// built-in tool that verifies if a variable exists and has data. ( (bracket) 
// $ (dollar sign) is the mandatory variable prefix. _ (underscore) connects 
// the prefix to the array name. POST (P O S T) is a Superglobal Array used 
// by the server to collect and pull data from an HTML form sent via the secure 
// HTTP POST method. It does NOT send data to the database directly; it only 
// captures (pulls) what the user typed in the browser so the script can 
// process it. [ (opening square bracket) starts the index pointer. 
// 'save' (quote s a v e quote) is the specific name of the submit button. 
// ] (closing square bracket) ends the pointer. ) (bracket) ends the tool. 
// ) (closing bracket) ends the condition. { (opening curly bracket) marks 
// the start of the registration processing logic.

    $first_name = trim($_POST['first_name']);
    // $ (dollar sign) variable marker. first_name (f i r s t underscore n a m e) 
    // is the unique label chosen to identify this data container. = (equals sign) 
    // is the assignment operator. trim (t r i m) cleans the text. ( (opening 
    // bracket) $ (dollar sign) _ (underscore) POST (P O S T) is the Superglobal 
    // Array that pulls (collects) data from the form in the browser. [ 
    // 'first_name' ] (key) ) (closing bracket). ; (semicolon) terminates 
    // the instruction.

    $last_name = trim($_POST['last_name']);
    // $ (dollar sign) variable marker. last_name (l a s t underscore n a m e) is 
    // the logical identifier for the user's surname. = (equals sign) assignment. 
    // trim (t r i m) cleans the text. ( (opening bracket) $ (dollar sign) 
    // _ (underscore) POST [ 'last_name' ] ) (closing bracket). ; (semicolon).

    $email = trim($_POST['email']);
    // $ (dollar sign) variable marker. email (e m a i l) is the identifier for 
    // the user's electronic address. = (equals sign) assignment. trim (t r i m) 
    // tool removes accidental spaces. ( (opening bracket) $ (dollar sign) 
    // _ (underscore) POST [ 'email' ] ) (closing bracket). ; (semicolon).

    $phone_number = trim($_POST['phone_number']);
    // $ (dollar sign) variable marker. phone_number (p h o n e underscore n u m b e r) 
    // identifies the contact digits. = (equals sign) assignment. trim (t r i m) 
    // cleans the input. ( (opening bracket) $ (dollar sign) _ (underscore) 
    // POST [ 'phone_number' ] ) (closing bracket). ; (semicolon).

    $password_raw = trim($_POST['password']);
    // $ (dollar sign) variable marker. password_raw (p a s s w o r d underscore 
    // r a w) is a label chosen specifically to indicate that this container 
    // holds the plain, unencrypted secret before it is hashed. = (equals sign) 
    // assignment. trim (t r i m) tool. ( (opening bracket) $ (dollar sign) 
    // _ (underscore) POST [ 'password' ] ) (closing bracket). ; (semicolon).

    $sql_check = "SELECT email FROM users WHERE email = ? OR phone_number = ?";
    //here we are creating a variable called sql_check to store the SQL command
    //  that will check if the email or phone number already exists in the database.
    //  The question marks are placeholders for the actual values that will be securely 
    //  inserted later using prepared statements.
    //? (question mark) is a critical security placeholder that prevents SQL Injection by 
    // isolating user data from the command. ( (opening bracket) starts the command.

    // $ (dollar sign) variable marker. sql_check (s q l underscore c h e c k) 
    // is a logical label chosen to describe the action of searching the 
    // database to check for existing accounts. = (equals sign) assignment. 
    // "SELECT..." (quote) starts the SQL instruction. ? (question marks) 
    // are highly critical security placeholders. They act as "holes" that 
    // prevent SQL Injection by telling the database engine to treat the 
    // user data only as literal values, never as executable commands. 
    // ; (semicolon) terminates the line.

    $stmt_check = mysqli_prepare($conn, $sql_check);
    //here we are preparing the SQL command for execution. mysqli_prepare is a 
    // function that takes the database connection and the SQL command with placeholders, 
    // and returns a statement object that can be executed securely.
    //$conn (the database connection handle) is passed to link the command to the active
    // database session. $sql_check is the command string that contains the SQL query with 
    // placeholders. The result, $stmt_check, is a statement object that will be used to
    //  bind parameters and execute the command securely.
    
    // $ (dollar sign) variable marker. stmt_check (s t m t underscore c h e c k) 
    // is the handle for the prepared statement object. = (equals sign) 
    // assignment. mysqli (MySQL Improved) _ (underscore) prepare (prepare) is 
    // the security function that pre-compiles the command. Pre-compiling (pre 
    // compiling) is the process where the database builds a rigid structural 
    // blueprint of the command before any data is poured in, ensuring no 
    // malicious code can change the command's meaning. ( (opening bracket) 
    // $ (dollar sign) conn (the connection bridge handle) , (comma) separates 
    // the bridge from the command $ (dollar sign) sql_check (the search 
    // blueprint) ) (closing bracket). ; (semicolon).

    mysqli_stmt_bind_param($stmt_check, "ss", $email, $phone_number);
    // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
    // bind (bind) _ (underscore) param (parameter) is the security tool that 
    // attaches (binds) the real user data to the placeholders. Binding 
    // (binding) is the safe act of pouring the variables into the 
    // pre-compiled blueprint holes (?). ( (opening bracket) $ (dollar sign) 
    // stmt_check (handle) , (comma) "ss" (two strings) , (comma) $ (dollar 
    // sign) email (data) , (comma) $ (dollar sign) phone_number (data) ) 
    // (closing bracket). ; (semicolon).

    mysqli_stmt_execute($stmt_check);
    // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
    // execute (execute) is the command that triggers the search in the 
    // database server. ( (bracket) $ (dollar sign) stmt_check (handle) ) 
    // (bracket). ; (semicolon).

    mysqli_stmt_store_result($stmt_check);
    // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
    // store (store) _ (underscore) result (result) is the function that 
    // pulls the found matches into the server's local memory for counting. 
    // ( (bracket) $ (dollar sign) stmt_check (handle) ) (bracket). ; (semicolon).
    
    if (mysqli_stmt_num_rows($stmt_check) > 0) {
    // if (if) starts a logic check to see if an account already exists. 
    // ( (opening bracket) starts the condition. mysqli_stmt_num_rows 
    // (number of rows) counts the results. ( (bracket) $ (dollar sign) 
    // stmt_check ) (bracket) > (greater than sign) 0 (zero) matches ) 
    // (closing bracket). { (opening curly bracket).

        mysqli_stmt_close($stmt_check);
        // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
        // close (close) is a critical cleanup tool that terminates the search 
        // tool and releases the server memory immediately. It is used to 
        // prevent resource leakage. ( (bracket) $ (dollar sign) stmt_check 
        // ) (bracket). ; (semicolon).

        die("Error: User already exists!");
        // die (die) is a terminal function that kills the script and shows 
        // the error message to the user. ( (bracket) "Error..." (quote) ) 
        // (bracket). ; (semicolon).
    }
    // } (closing curly bracket) ends the error path.

    mysqli_stmt_close($stmt_check);
    // mysqli_stmt_close (close) terminates the tool handle and clears the 
    // memory for the successful path. ; (semicolon).

    $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);
    // $ (dollar sign) variable marker. hashed_password (h a s h e d 
    // underscore p a s s w o r d) is the logical identifier for the 
    // encrypted secret. = (equals sign) assignment operator. password_hash 
    // (password hash) is the high-security tool that scrambles the plain 
    // text into an unreadable string. ( (opening bracket) $ (dollar sign) 
    // password_raw (the input) , (comma) PASSWORD_DEFAULT (standard 
    // algorithm) ) (closing bracket). ; (semicolon).

    $role = "PASSENGER";
    // $ (dollar sign) variable. role (r o l e) label. = (equals sign) 
    // assignment. "PASSENGER" (quote) is the default user level. ; (semicolon).

    $sql_insert = "INSERT INTO users (first_name, last_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?, ?)";
    // $ (dollar sign) variable marker. sql_insert (s q l underscore i n s e r t) 
    // describes the database creation command string. = (equals sign). 
    // "INSERT..." (quote) instruction. ? (question marks) are the 6 
    // security placeholders. ; (semicolon).

    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    // $ (dollar sign) variable marker. stmt_insert (s t m t underscore 
    // i n s e r t) is the handle for the creation tool object. = (equals 
    // sign). mysqli_prepare (prepare) pre-compiles the command blueprint. 
    // Pre-compiling (pre compiling) locks the structural shape of the 
    // command in the database before any data is added. ( (bracket) 
    // $ (dollar sign) conn (bridge) , (comma) $ (dollar sign) sql_insert 
    // (the command) ) (bracket). ; (semicolon).

    mysqli_stmt_bind_param($stmt_insert, "ssssss", $first_name, $last_name, $email, $phone_number, $hashed_password, $role);
    // mysqli_stmt_bind_param (bind parameter) is the function that securely 
    // attaches (binds) the 6 user data points to the placeholders. Binding 
    // (binding) is the process of safely pouring user values into the 
    // pre-compiled blueprint holes (?). ( (opening bracket) $ (dollar sign) 
    // stmt_insert (handle) , (comma) "ssssss" (six strings) , (comma) 
    // $ (dollar sign) first_name ... ) (bracket). ; (semicolon).

    if (mysqli_stmt_execute($stmt_insert)) {
    // if (if) starts a logic check for creation success. ( (bracket) 
    // mysqli_stmt_execute (execute) runs the insertion. ( $stmt_insert ) 
    // ) (bracket). { (opening curly bracket) starts the success path.

        $new_user_id = mysqli_insert_id($conn);
        // $ (dollar sign) variable marker. new_user_id (n e w underscore u s e r 
        // underscore i d) is the label for the newly generated unique number. 
        // = (equals sign). mysqli_insert_id (insert id) pulls the last 
        // generated ID from the bridge. ( $conn ). ; (semicolon).

        $_SESSION['user_id'] = $new_user_id;
        // $ (dollar sign) variable marker. _ (underscore) SESSION (Superglobal 
        // Array used by the server to maintain persistent memory) [ 'user_id' ] 
        // (key) = (equals sign) $new_user_id (the data). ; (semicolon).

        $_SESSION['role'] = $role;
        // $ (dollar sign) variable. _ (underscore) SESSION [ 'role' ] (key 
        // for user type) = (equals sign) $role (permission level). ; (semicolon).

        $_SESSION['name'] = $first_name . " " . $last_name;
        // $ (dollar sign) variable. _ (underscore) SESSION [ 'name' ] (key 
        // for identity) = (equals sign) $first_name (data) . (dot) 
        // joins the text. " " (space). . (dot) $last_name (surname). 
        // ; (semicolon).

        mysqli_stmt_close($stmt_insert);
        // mysqli_stmt_close (close) terminates the tool and releases server 
        // memory used for the insertion to maintain system speed. ( (bracket) 
        // $ (dollar sign) stmt_insert ) (bracket). ; (semicolon).

        echo "<script>alert('Welcome!'); window.location.href='dashboard.php';</script>";
        // echo (echo) prints a command to the browser interface. <script> 
        // (script) starts a browser instruction. alert('...') shows a 
        // popup. window.location.href redirects to the dashboard. 
        // ; (semicolon).

        exit();
        // exit (exit) stops all further script execution immediately. 
        // ; (semicolon).
    } else {
    // } (closing curly bracket) ends the success path. else (else) 
    // starts the failure path. { (opening curly bracket).

        mysqli_stmt_close($stmt_insert);
        // mysqli_stmt_close (close) cleans up the tool handle to release 
        // resources upon failure. ( $stmt_insert ). ; (semicolon).

        die("Signup failed!");
        // die (die) kills the script with a failure message. ; (semicolon).
    }
    // } (closing curly bracket) ends the attempt block.
}
// } (closing curly bracket) ends the registration block.

if (isset($_POST['login'])) {
// if (if) starts a logic check to determine if the login form has been 
// submitted. ( (opening bracket) starts the condition. isset (is set) 
// verifies if a variable exists. ( $ (dollar sign) _ (underscore) 
// POST [ 'login' ] (submit button) ) (bracket) ) (bracket). { (opening 
// curly bracket) marks the start of the authentication logic.
// if condition. { (opening curly bracket) marks the start of the login 
// verification block.

    $email = trim($_POST['email']);
    // $ (dollar sign) variable marker. email (e m a i l) is the identifier. 
    // = (equals sign) assignment. trim (t r i m) cleans the text. ( (bracket) 
    // $ (dollar sign) _ (underscore) POST (Superglobal Array that pulls/collects 
    // form data) [ 'email' ] ) (bracket). ; (semicolon).

    $password_input = trim($_POST['password']);
    // $ (dollar sign) variable marker. password_input (p a s s w o r d underscore 
    // i n p u t) is a label chosen specifically to indicate this holds the 
    // plain text typed by the user. = (equals sign) assignment. trim (t r i m) 
    // tool. ( (opening bracket) $ (dollar sign) _ (underscore) POST [ 'password' ] 
    // ) (closing bracket). ; (semicolon).

    $sql_login = "SELECT user_id, password, role, first_name, last_name FROM users WHERE email = ?";
    // $ (dollar sign) variable marker. sql_login (s q l underscore l o g i n) 
    // is a logical label chosen for the login search. = (equals sign) 
    // assignment. "SELECT..." (quote) starts the SQL instruction. ? (question 
    // mark) is a critical security placeholder that prevents SQL Injection 
    // by isolating user data from the command. ; (semicolon).

    $stmt_login = mysqli_prepare($conn, $sql_login);
    // $ (dollar sign) variable marker. stmt_login (s t m t underscore l o g i n) 
    // is the handle for the search tool object. = (equals sign) assignment. 
    // mysqli (MySQL Improved) _ (underscore) prepare (prepare) is the security 
    // tool that pre-compiles the search blueprint. Pre-compiling (pre compiling) 
    // locks the structural shape of the command in the database before user data 
    // is added, preventing malicious code from altering the logic. 
    // ( (opening bracket) $ (dollar sign) conn (bridge) , (comma) $ (dollar sign) 
    // sql_login (command) ) (closing bracket). ; (semicolon).

    mysqli_stmt_bind_param($stmt_login, "s", $email);
    // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
    // bind (bind) _ (underscore) param (parameter) is the function that 
    // attaches the user data to the placeholder. Binding (binding) is the 
    // safe act of pouring the $email variable into the pre-compiled blueprint 
    // hole (?). ( (opening bracket) $ (dollar sign) stmt_login (handle) , 
    // (comma) "s" (quote s quote) means one string , (comma) $ (dollar sign) 
    // email (typed data) ) (closing bracket). ; (semicolon).

    mysqli_stmt_execute($stmt_login);
    // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
    // execute (execute) is the command that tells the database to perform 
    // the search. ( (opening bracket) $ (dollar sign) stmt_login (handle) 
    // ) (closing bracket). ; (semicolon).

    mysqli_stmt_store_result($stmt_login);
    // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
    // store (store) _ (underscore) result (result) is the tool that saves 
    // the found account details into the server's local memory for 
    // verification. ( (opening bracket) $ (dollar sign) stmt_login (handle) 
    // ) (closing bracket). ; (semicolon).

    mysqli_stmt_bind_result($stmt_login, $user_id, $stored_password, $role, $f_name, $l_name);
    // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
    // bind (bind) _ (underscore) result (result) is the function that links 
    // database columns to local variables. ( (opening bracket) $ (dollar sign) 
    // stmt_login (handle) , (comma) $ (dollar sign) user_id (id container) 
    // , (comma) $ (dollar sign) stored_password (hash container) , (comma) 
    // $ (dollar sign) role (role container) , (comma) $ (dollar sign) f_name 
    // (name container) , (comma) $ (dollar sign) l_name (surname container) 
    // ) (closing bracket). ; (semicolon).
    // (destination) , (comma) $ (dollar sign) l_name (destination) 
    // ) (closing bracket). ; (semicolon).

    if (mysqli_stmt_num_rows($stmt_login) > 0) {
    // if (if) check for account existence. ( (opening bracket) mysqli (MySQL Improved) 
    // _ (underscore) stmt (statement) _ (underscore) num (number) _ (underscore) 
    // rows (rows) counts the results. ( (bracket) $ (dollar sign) stmt_login 
    // ) (bracket) > (greater than sign) 0 (zero) matches ) (closing bracket). 
    // { (opening curly bracket) starts the verification block.

        mysqli_stmt_fetch($stmt_login);
        // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
        // fetch (fetch) is the tool that fills the local variables with the saved 
        // result data. ( (opening bracket) $ (dollar sign) stmt_login (handle) 
        // ) (closing bracket). ; (semicolon).

        if (password_verify($password_input, $stored_password)) {
        // if (if) check for a secret match. ( (opening bracket) password (password) 
        // _ (underscore) verify (verify) is the tool that checks the typed secret 
        // against the encrypted one. ( $password_input (typed) , $stored_password 
        // (scrambled) ) (bracket) ) (bracket). { (opening curly bracket) starts 
        // the success logic.

            $_SESSION['user_id'] = $user_id;
            // $ (dollar sign) variable marker. _ (underscore) connects to the 
            // array name. SESSION (S E S S I O N) is a Superglobal Array used 
            // by the server to maintain persistent memory across different 
            // web pages. [ (opening square bracket) starts the index pointer. 
            // 'user_id' (quote) is the unique key for the identification number. 
            // ] (closing square bracket) ends the pointer. = (equals sign) 
            // assignment operator. $ (dollar sign) user_id (the retrieved data) 
            // identifies the account. ; (semicolon) terminates the line.

            $_SESSION['role'] = $role;
            // $ (dollar sign) variable. _ (underscore) SESSION (memory array) 
            // [ 'role' ] (key for user type) = (equals sign) $role (the retrieved 
            // permission level). ; (semicolon).

            $_SESSION['name'] = $f_name . " " . $l_name;
            // $ (dollar sign) variable. _ (underscore) SESSION (memory array) 
            // [ 'name' ] (key for the identity) = (equals sign) $f_name 
            // (retrieved first name) . (dot) is the concatenation tool that 
            // joins text. " " (quote space quote) adds a gap. . (dot) connects. 
            // $l_name (retrieved surname). ; (semicolon).

            mysqli_stmt_close($stmt_login);
            // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
            // close (close) is a critical cleanup tool that formally terminates 
            // the statement handle and releases the server memory used for the 
            // login search. It is used to prevent "resource leakage" which 
            // would otherwise slow down the machine over time. ( (opening 
            // bracket) $ (dollar sign) stmt_login (the tool handle being 
            // destroyed) ) (closing bracket). ; (semicolon) terminates the 
            // instruction.

            if($role == 'ADMIN') { header("Location: admin_dashboard.php"); }
            // if (if) check for administrative role. ( (bracket) $ (dollar sign) 
            // role == (is equal to) 'ADMIN' (text) ) (bracket). { (curly 
            // bracket) header (header) redirection tool. ( "Location..." ) 
            // sends the admin to the management center. } (closing curly 
            // bracket). ; (semicolon).

            elseif ($role == 'AGENT') { header("Location: agent_dashboard.php"); }
            // elseif (else if) check for agent role. ( (bracket) $ (dollar sign) 
            // role == 'AGENT' ) (bracket). { (curly bracket) header (header) 
            // redirection to the agent portal. } (curly bracket). ; (semicolon).

            else { header("Location: dashboard.php"); }
            // else (else) default catch for standard users. { (curly bracket) 
            // header (header) redirection to the passenger home page. } 
            // (curly bracket). ; (semicolon).

            exit();
            // exit (exit) is a function that immediately kills the script 
            // processing after redirection. ( ) (empty brackets). 
            // ; (semicolon).
        } else {
        // } (closing curly bracket) ends the match path. else (else) 
        // marks the start of the incorrect password logic. { (opening 
        // curly bracket).

            mysqli_stmt_close($stmt_login);
            // mysqli_stmt_close (close) terminates the tool handle to release 
            // server memory immediately upon failure. ( (bracket) $ (dollar 
            // sign) stmt_login ) (bracket). ; (semicolon).

            echo "<script>alert('Wrong password!'); window.location.href='login.html';</script>";
            // echo (echo) prints a command to the browser. <script> starts a 
            // JavaScript block. alert('...') (alert) shows a pop-up warning. 
            // window.location.href (redirect) sends the user back to the 
            // login page. </script> ends the block. ; (semicolon).

            exit();
            // exit (exit) stops the script processing. ; (semicolon).
        }
        // } (closing curly bracket) ends password verification.
    } else {
    // } (closing curly bracket) ends existence check. else (else) 
    // marks the start of the missing account logic. { (opening curly 
    // bracket).

        mysqli_stmt_close($stmt_login);
        // mysqli_stmt_close (close) kills the statement tool handle and 
        // clears the memory to maintain server efficiency. ( (bracket) 
        // $ (dollar sign) stmt_login ) (bracket). ; (semicolon).

        echo "<script>alert('Account not found!'); window.location.href='login.html';</script>";
        // echo (echo) prints the pop-up and redirect script to the browser 
        // interface. ; (semicolon).

        exit();
        // exit (exit) terminates the processing. ; (semicolon).
    }
    // } (closing curly bracket) ends the account check block.
}
// } (closing curly bracket) ends the primary login logic.

mysqli_close($conn);
// mysqli (MySQL Improved) _ (underscore) close (close) is the final 
// cleanup tool that formally shuts the bridge between the PHP engine 
// and the database server. It is used to ensure no unused connections 
// remain hanging open. ( (opening bracket) $ (dollar sign) conn (the 
// bridge handle) ) (closing bracket). ; (semicolon) terminates the 
// instruction.

?>
<!-- ? (question mark) > (greater than sign) is the closing tag that ends the 
PHP logic and returns to HTML mode. -->
