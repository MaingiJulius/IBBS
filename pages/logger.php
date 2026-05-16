<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side processing engine.

/**
 * SYSTEM AUDIT LOGGER (logger.php)
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// SYSTEM AUDIT LOGGER is the title. * (asterisk) / (forward slash) closes the block.

if (!function_exists('logActivity')) {
// if (if) starts a logic check. ( (opening bracket) starts condition. ! (exclamation mark) 
// is the logical NOT operator. function_exists (function exists) is a tool that checks if 
// a command already exists. ( 'logActivity' ) is the specific name. ) (closing bracket) 
// ends check. { (opening curly bracket) starts the definition block.
    
    function logActivity($userId, $userName, $type, $description) {
    // function (function) is the keyword to create a reusable tool. logActivity (log 
    // activity) is the tool's name. ( (opening bracket) starts the parameter list. 
    // $ (dollar sign) marks the variable. userId (user id) is the first input. , (comma) 
    // separates inputs. $ (dollar sign) userName (user name) is the second. , (comma) 
    // $ (dollar sign) type (type) is the third. , (comma) $ (dollar sign) description 
    // (description) is the fourth. ) (closing bracket) ends the list. { (opening curly 
    // bracket) starts the tool's logic.
        
        global $conn; 
        // global (global) is a keyword that allows the function to reach outside of itself 
        // to find a variable. $ (dollar sign) marks the conn (connection) bridge handle. 
        // ; (semicolon) terminates the instruction.
        
        if (!$conn) {
        // if (if) check for the connection bridge. ( (opening bracket) starts check. 
        // ! (exclamation mark) is the NOT operator. $ (dollar sign) marks the conn 
        // (connection) variable. ) (closing bracket) ends check. { (opening curly 
        // bracket) starts the "Import" block.

            require_once 'db_connection.php';
            // require_once (require once) is a directive that forces the server to load 
            // another file. 'db_connection.php' (quote db underscore connection dot php 
            // quote) is the path to the bridge file. ; (semicolon) terminates the line.
        }
        // } (closing curly bracket) ends the "Import" block.

        $time = date('H:i:s'); 
        // $ (dollar sign) creates a variable. time (time) is the label for the clock 
        // container. = (equals sign) assigns the current time. date (date) is the clock 
        // tool. ( (opening bracket) starts format. 'H:i:s' (quote Hour colon Minute 
        // colon Second quote) defines the pattern. ) (closing bracket) ends tool. 
        // ; (semicolon) terminates the instruction.

        $date = date('Y-m-d'); 
        // $ (dollar sign) marks a variable. date (date) is the label for the calendar 
        // container. = (equals sign) assigns the value. date (date) tool. ( (opening 
        // bracket) 'Y-m-d' (quote Year dash Month dash Day quote) format. ) (closing 
        // bracket). ; (semicolon) terminates the instruction.

        $sql = "INSERT INTO Logs (user_id, type, description, name, time, date) VALUES (?, ?, ?, ?, ?, ?)";
        // $ (dollar sign) creates a variable. sql (structured query language) is the label 
        // for the database command string. = (equals sign) assigns the text. "INSERT 
        // INTO Logs..." (quote) is the instruction to the database. ; (semicolon) 
        // terminates the line.
        
        $stmt = mysqli_prepare($conn, $sql);
        // $ (dollar sign) creates a variable. stmt (statement) is the handle for a 
        // pre-compiled database command. = (equals sign) assigns the result. mysqli 
        // (MySQL Improved) _ (underscore) prepare (prepare) is a security tool that 
        // pre-compiles the command. Pre-compiling (pre compiling) means the machine 
        // creates a structural blueprint of the command before any data is added. 
        // This stops hackers from injecting malicious code because the machine 
        // already knows the exact shape of the instruction. ( (opening bracket) 
        // starts input. $conn (connection bridge) , (comma) $sql (command). ) 
        // (closing bracket) ends input. ; (semicolon) terminates the instruction.
        
        mysqli_stmt_bind_param($stmt, "isssss", $userId, $type, $description, $userName, $time, $date);
        // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) bind 
        // (bind) _ (underscore) param (parameter) is a critical security tool that 
        // attaches (binds) real data to the pre-compiled command. Binding (binding) 
        // is the process of safely pouring the user's data into the pre-compiled 
        // blueprint holes (?). This ensures the data is treated only as text or 
        // numbers, never as a command. ( (opening bracket) starts input. $stmt 
        // (statement handle) , (comma) "isssss" (quote integer string string string 
        // string string quote) defines the data types. , (comma) $userId, $type, 
        // $description, $userName, $time, $date are the data sources. ) (closing 
        // bracket) ends input. ; (semicolon) terminates the instruction.
        
        mysqli_stmt_execute($stmt);
        // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
        // execute (execute) is the command that actually tells the database to run 
        // the finished instruction. ( (opening bracket) $stmt (the prepared command). 
        // ) (closing bracket). ; (semicolon) terminates the instruction.
        
        mysqli_stmt_close($stmt);
        // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
        // close (close) is a cleanup tool that releases the server memory used 
        // by the command. ( (opening bracket) $stmt (handle). ) (closing bracket). 
        // ; (semicolon) terminates the instruction.
    }
    // } (closing curly bracket) ends the logActivity tool's logic body.
}
// } (closing curly bracket) ends the function existence check block.
?>

