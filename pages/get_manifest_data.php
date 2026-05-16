<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side logic engine.

/**
 * MANIFEST DATA GENERATOR (get_manifest_data.php)
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// MANIFEST DATA GENERATOR is the module title. * (asterisk) / (forward slash) 
// closes the block.

require_once 'db_connection.php';
// require_once (require once) is a directive that imports the database bridge file and 
// ensures it is only loaded one time to prevent errors. 'db_connection.php' (quote db 
// underscore connection dot php quote) is the file path. ; (semicolon) terminates the line.

session_start();
// session_start (session start) is the command that activates the server's memory 
// to track the user across different pages. ( ) (empty brackets) execute the tool. 
// ; (semicolon) terminates the instruction.

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
// if (if) starts a logic check for security. ( (opening bracket) starts the condition. 
// ! (exclamation mark) is the NOT operator which reverses the logic. isset (is set) 
// is a built-in tool that verifies if a memory container exists and has data. 
// ( (bracket) $ (dollar sign) is the mandatory variable prefix. _ (underscore) 
// connects the prefix to the array name. SESSION (S E S S I O N) is a Superglobal 
// Array used by the server to maintain persistent memory. [ (opening square 
// bracket) starts the index pointer. 'role' (quote r o l e quote) is the specific 
// key being checked. ] (closing square bracket) ends the pointer. ) (bracket) 
// ends the tool. || (double pipe) is the logical OR operator. $ (dollar sign) 
// _ (underscore) SESSION [ 'role' ] (the data container) !== (is NOT identical 
// to) 'ADMIN' (the required permission text). ) (closing bracket) ends the 
// entire condition. { (opening curly bracket) marks the start of the access 
// denial logic.

    die("Access Denied.");
    // die (die) is a terminal function that prints an error message and stops all 
    // further code execution immediately. ( (opening bracket) "Access Denied." 
    // (the message string) ) (closing bracket). ; (semicolon) terminates the line.
}
// } (closing curly bracket) marks the end of the security check block.

$route_id = isset($_GET['route_id']) ? $_GET['route_id'] : die("Error");
// $ (dollar sign) variable marker. route_id (r o u t e underscore i d) is 
// the unique label chosen to identify the specific trip identification number. 
// = (equals sign) is the assignment operator. isset (is set) is a tool that 
// verifies if a variable exists. ( (opening bracket) $ (dollar sign) _ 
// (underscore) GET (G E T) is a Superglobal Array used by the server to collect 
// and pull data from the URL query string. [ 'route_id' ] retrieves the ID from 
// the address bar. ) (closing bracket) ends the tool. ? (question mark) is the 
// ternary "if" operator. $_GET['route_id'] is the value if true. : (colon) is 
// the ternary "else" operator. die (die) kills the script with an "Error" 
// message if the ID is missing. ; (semicolon) terminates the instruction.

$sql_t = "SELECT r.*, b.bus_name, b.reg_no, d.full_name as driver_name FROM routes r JOIN buses b ON r.bus_id = b.bus_id LEFT JOIN drivers d ON b.driver_id = d.driver_id WHERE r.route_id = ?";
// $ (dollar sign) variable marker. sql_t (s q l underscore t) is the identifier 
// for the database retrieval command string. = (equals sign) assignment. 
// "SELECT..." (quote) starts the complex SQL instruction. ? (question mark) 
// is a critical security placeholder that acts as a "hole" to prevent SQL 
// Injection by telling the database engine to treat the ID only as a literal 
// value. ; (semicolon) terminates the line.

$stmt_t = mysqli_prepare($conn, $sql_t);
/* $stmt_t (handle) = (assignment). 
   mysqli (MySQL Improved) _ (underscore) prepare (prepare) is the security 
   function that pre-compiles the command blueprint. 
   "Improved" (mysqli) is used because it supports secure "Prepared Statements" 
   using placeholders like ? to prevent SQL Injection. 
   ( starts. $conn (bridge) , (comma) $sql_t (blueprint) ) ends. ; (semicolon). */

mysqli_stmt_bind_param($stmt_t, "i", $route_id);
/* mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
   bind (bind) _ (underscore) param (parameter) is the function that 
   securely pours the data into the ? placeholders. 
   The ? (Question Mark) is a safety hole that ensures user data 
   is never treated as a command, blocking SQL Injection. 
   ( starts. $stmt_t (tool handle) , (comma) "i" (integer number type) 
   , (comma) $route_id (trip identity data) ) ends. ; (semicolon). */

mysqli_stmt_execute($stmt_t);
/* mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
   execute (execute) is the command that triggers the database engine to 
   run the query and find the trip. ( starts. $stmt_t (the tool handle). 
   ) ends. ; (semicolon) terminates the line. */

$res_t = mysqli_stmt_get_result($stmt_t);
/* $ (variable) res_t (result list) = (assignment). 
   mysqli_stmt_get_result (get result) is the function that pulls the 
   found trip data from the database server into your script. 
   ; (semicolon) terminates the line. */

$trip = mysqli_fetch_assoc($res_t);
/* $ (variable) trip (one row) = (assignment). 
   mysqli_fetch_assoc (fetch associative) converts the result set 
   into an easy-to-read list with labels (like 'bus_name'). 
   ; (semicolon) terminates the line. */

mysqli_stmt_close($stmt_t);
// mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
// close (close) is a critical cleanup tool that terminates the search handle 
// and releases the server memory immediately to prevent resource leakage. 
// ( (bracket) $stmt_t ) (bracket). ; (semicolon).

$sql_p = "SELECT * FROM bookings WHERE route_id = ? AND booking_status != 'CANCELLED' ORDER BY seat_number ASC";
// $ (dollar sign) variable marker. sql_p (s q l underscore p) is the identifier 
// for the passenger retrieval command. = (equals sign). "SELECT..." (quote) 
// instruction. ? (question mark) is the security placeholder for the trip ID. 
// ; (semicolon) terminates the line.

$stmt_p = mysqli_prepare($conn, $sql_p);
// $ (dollar sign) variable marker. stmt_p (s t m t underscore p) is the handle 
// for the passenger search tool. = (equals sign). mysqli_prepare (prepare) 
// pre-compiles the passenger search blueprint to ensure structural integrity 
// and security. ( (bracket) $conn , $sql_p ) (bracket). ; (semicolon).

mysqli_stmt_bind_param($stmt_p, "i", $route_id);
// mysqli_stmt_bind_param (bind parameter) is the security function that 
// attaches the route ID variable to the second search's placeholder. 
// "i" (integer) indicates the data type. ( (bracket) $stmt_p , "i" , $route_id 
// ) (bracket). ; (semicolon).

mysqli_stmt_execute($stmt_p);
// mysqli_stmt_execute (execute) runs the passenger list search in the database. 
// ( (bracket) $stmt_p ) (bracket). ; (semicolon).

$passengers = mysqli_stmt_get_result($stmt_p);
// $ (dollar sign) variable. passengers (p a s s e n g e r s) is the container 
// that holds the list of traveler results retrieved from the database. 
// = (equals sign) assignment operator. ; (semicolon).

mysqli_stmt_close($stmt_p);
// mysqli_stmt_close (close) terminates the second statement tool handle and 
// clears the memory to maintain server efficiency. ( (bracket) $stmt_p ) 
// (bracket). ; (semicolon).
?>
<!-- (less than sign) ? (question mark) > (greater than sign) is the closing tag that ends the 
PHP logic engine and returns the processor to HTML display mode. -->

<!DOCTYPE html>
<!-- (less than sign) ! (exclamation mark) DOCTYPE (document type) html (h t m l) 
(greater than sign) is the standard declaration that tells the web browser 
this is a modern HTML5 document. -->

<html lang="en">
<!-- (less than sign) html (h t m l) starts the web document structure. lang 
(language) = (equals sign) "en" (English text content). (greater than sign). -->

<head>
<!-- (less than sign) head (h e a d) (greater than sign) starts the hidden 
configuration section of the page where metadata and styles are defined. -->

    <meta charset="UTF-8">
    <!-- (less than sign) meta (m e t a) charset (character set) = (equals sign) 
    "UTF-8" (standard universal text encoding) (greater than sign). -->

    <title>Official Manifest - IBBS</title>
    <!-- (less than sign) title (t i t l e) (greater than sign) sets the specific 
    label that appears on the browser's tab. (less than sign) / (slash) title 
    (greater than sign). -->

    <style>
    /* / (forward slash) * (asterisk) starts a CSS (Cascading Style Sheets) 
    design section for page layout. */
        body { font-family: sans-serif; padding: 40px; }
        /* body (b o d y) targets the entire page content. { (bracket) font-family 
        (font) : (colon) sans-serif (clean style) ; (semicolon) padding (internal 
        space) : (colon) 40px (size) ; (semicolon) } (bracket). */

        .header { text-align: center; border-bottom: 2px solid black; padding-bottom: 20px; }
        /* . (dot) header (class) targets elements with the 'header' label. 
        text-align: center (centers text). border-bottom (line below). */

        .details { margin: 20px 0; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; border: 1px solid #ccc; padding: 15px; }
        /* . (dot) details (class) defines the layout of the trip information box 
        using a grid system. */

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        /* table (t a b l e) targets the data grid. width: 100% (full width). 
        border-collapse (joins lines). */

        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        /* th (header cell) , (and) td (data cell) define the borders and internal 
        spacing for the table. */

    </style>
    <!-- (less than sign) / (slash) style (s t y l e) (greater than sign) ends the 
    visual design configuration section. -->

</head>
<!-- (less than sign) / (slash) head (h e a d) (greater than sign) ends the 
configuration section. -->

<body>
<!-- (less than sign) body (b o d y) (greater than sign) starts the visible 
section of the webpage that the user interacts with. -->

    <div class="header"><h1>Wema Travellers Manifest</h1></div>
    <!-- (less than sign) div (d i v) class (class) = (equals sign) "header" 
    (greater than sign) is a container for the main title. (less than sign) h1 
    (heading level one) (greater than sign) is the primary page title. 
    (less than sign) / (slash) h1 (greater than sign). (less than sign) / 
    (slash) div (greater than sign). -->
    
    <div class="details">
    <!-- (less than sign) div (d i v) class (class) = (equals sign) "details" 
    (greater than sign) starts the container for trip-specific information. -->

        <div><strong>Path:</strong> <?php echo htmlspecialchars($trip['from_location']); ?> → <?php echo htmlspecialchars($trip['to_location']); ?></div>
        <!-- html (HyperText) special (special) chars (characters) is a security tool 
             that encodes text so symbols like < are safe. ( starts the tool. 
             $trip (trip data container) ['from_location'] (origin label) 
             is the specific data piece. ) ends the tool. -->

        <div><strong>Date:</strong> <?php echo $trip['departure_date']; ?></div>
        <!-- (less than sign) div (d i v) (greater than sign) displays the trip's 
        scheduled departure date. (less than sign) ? (question mark) php echo 
        (print) $trip['departure_date'] (data) ; (semicolon) ? (question mark) 
        > (greater than sign). -->

        <div><strong>Bus:</strong> <?php echo htmlspecialchars($trip['bus_name']); ?></div>
        <!-- htmlspecialchars (security tool) ( $trip ['bus_name'] (vehicle label) ) -->

        <div><strong>Driver:</strong> <?php echo htmlspecialchars($trip['driver_name']); ?></div>
        <!-- htmlspecialchars (security tool) ( $trip ['driver_name'] (operator label) ) -->

    </div>
    <!-- (less than sign) / (slash) div (greater than sign) ends the trip details 
    container. -->

    <table>
    <!-- (less than sign) table (t a b l e) (greater than sign) starts the grid 
    for displaying the list of passengers. -->

        <thead>
        <!-- (less than sign) thead (t h e a d) (greater than sign) starts the 
        heading section for the table columns. -->

            <tr><th>No.</th><th>Name</th><th>ID Number</th><th>Seat</th><th>Status</th></tr>
            <!-- (less than sign) tr (table row) (greater than sign). (less than 
            sign) th (table heading) (greater than sign) defines the labels for 
            each data column. -->

        </thead>
        <!-- (less than sign) / (slash) thead (greater than sign) ends the table 
        headings. -->

        <tbody>
        <!-- (less than sign) tbody (t b o d y) (greater than sign) starts the 
        main content area where individual traveler rows will be generated. -->

            <?php $i=1; while($p = mysqli_fetch_assoc($passengers)): ?>
            <!-- (less than sign) ? (question mark) php (PHP engine). $ (dollar 
            sign) i (i) = (assign) 1 (initial count value) ; (semicolon). while 
            (while) starts a loop that repeats for every passenger. ( (bracket) 
            $p (variable for one passenger) = (assign) mysqli_fetch_assoc (fetch 
            labeled data) ( $passengers ) list ) (bracket) : (colon) starts the 
            loop block. -->

                <tr>
                <!-- (less than sign) tr (t r) (greater than sign) starts a new 
                data row for the current traveler being processed by the loop. -->

                    <td><?php echo $i++; ?></td>
                    <!-- (less than sign) td (table data) (greater than sign). 
                    (less than sign) ? (question mark) php echo (print) $i 
                    (count) ++ (add one after printing) ; (semicolon) ? 
                    (question mark) > (greater than sign). -->

                    <td><?php echo htmlspecialchars($p['passenger_name']); ?></td>
                    <!-- html (HyperText) special (special) chars (characters) is a security tool 
                         that encodes text so symbols like < are safe. ( starts the tool. 
                         $p (passenger row variable) ['passenger_name'] (name label) 
                         is the specific data piece. ) ends the tool. -->

                    <td><?php echo htmlspecialchars($p['passenger_id_number']); ?></td>
                    <!-- htmlspecialchars (security tool) ( $p ['passenger_id_number'] (ID label) ) -->

                    <td><?php echo $p['seat_number']; ?></td>
                    <!-- (less than sign) td (greater than sign) displays the 
                    seat number chosen by the passenger. -->

                    <td><?php echo $p['booking_status']; ?></td>
                    <!-- (less than sign) td (greater than sign) displays the 
                    current state of the reservation (e.g., BOOKED). -->

                </tr>
                <!-- (less than sign) / (slash) tr (greater than sign) ends the 
                row for the current passenger. -->

            <?php endwhile; ?>
            <!-- (less than sign) ? (question mark) php (PHP engine) endwhile 
            (end while) is the command that closes the passenger loop block. 
            ; (semicolon) ? (question mark) > (greater than sign). -->

        </tbody>
        <!-- (less than sign) / (slash) tbody (greater than sign) ends the table 
        data body section. -->

    </table>
    <!-- (less than sign) / (slash) table (greater than sign) ends the data grid. -->

    <script>
    /* (less than sign) s (s) c (c) r (r) i (i) p (p) t (t) > (greater than sign) 
       starts the JavaScript logic block. */

        window.onload = function() {
        /* window (w i n d o w) is the master browser object representing the 
           entire browser window. . (dot) is the connector used to access its 
           tools. onload (o n l o a d) is the specific "event" property that 
           detects when the page has finished loading all text, images, and 
           styles. = (equals sign) is the assignment operator. function (f u n 
           c t i o n) defines the set of instructions to follow when the 
           event happens. ( ) (bracket) { (opening curly bracket) marks 
           the start of the instruction set. */

            window.print();
            /* window (browser) . (dot) print (p r i n t) is the specific 
               instruction that tells the browser to open the system's 
               printing dialog box. ( (bracket) ) (bracket) executes 
               this tool immediately. ; (semicolon) terminates the line. */
        };
        /* } (closing curly bracket) ends the instruction set. ; (semicolon) 
           marks the end of the assignment. */

    </script>

    <!-- (less than sign) / (slash) script (greater than sign) ends the 
    JavaScript block. -->

</body>
<!-- (less than sign) / (slash) body (greater than sign) ends the visible page 
content section. -->

</html>
<!-- (less than sign) / (slash) html (greater than sign) ends the overall web 
document structure. -->

<?php mysqli_close($conn); ?>
<!-- (less than sign) ? (question mark) php (PHP engine). mysqli (MySQL 
Improved) _ (underscore) close (close) is the tool that formally shuts the 
bridge to the database server. ( (bracket) $conn (the bridge handle) ) 
(bracket) ; (semicolon) ? (question mark) > (greater than sign). -->
