<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side logic engine.

/**
 * ADD_ROUTE.PHP
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// ADD_ROUTE.PHP is the module title. * (asterisk) / (forward slash) closes the block.

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_route'])) {
// if (if) starts a check for form submission. ( (opening bracket) $ (dollar sign) _ 
// (underscore) SERVER [ 'REQUEST_METHOD' ] === (identical to) 'POST' (transmission type). 
// && (double ampersand) is the AND operator. isset (is set) ( $_POST [ 'add_route' ] 
// (button ID) ) (bracket). ) (closing bracket). { (opening curly bracket) starts logic.

    $from   = $_POST['from_location'];
    // $ (dollar sign) creates a variable. from (from) is the label for the departure city. 
    // = (equals sign) assigns form data. $_POST['from_location'] is the input. ; (semicolon).

    $to     = $_POST['to_location'];
    // $ (dollar sign) variable. to (to) label for destination. = (equals sign) assignment. 
    // $_POST['to_location'] is the input. ; (semicolon).

    $date   = $_POST['departure_date'];
    // $ (dollar sign) variable. date (date) label for travel day. = (equals sign) assignment. 
    // $_POST['departure_date'] is the input. ; (semicolon).

    $time   = $_POST['departure_time'];
    // $ (dollar sign) variable. time (time) label for departure hour. = (equals sign) 
    // assignment. $_POST['departure_time'] is the input. ; (semicolon).

    $cost   = $_POST['cost'];
    // $ (dollar sign) variable. cost (cost) label for ticket price. = (equals sign) 
    // assignment. $_POST['cost'] is the input. ; (semicolon).

    $bus_id = $_POST['bus_id'];
    // $ (dollar sign) variable. bus_id (bus id) label for vehicle identification. 
    // = (equals sign) assignment. $_POST['bus_id'] is the input. ; (semicolon).

    $stmt = $conn->prepare("CALL AddRoute(?, ?, ?, ?, ?, ?)");
    // $ (dollar sign) creates a variable. stmt (statement) is the handle for the 
    // secure database interaction. = (equals sign) assignment. $ (dollar sign) 
    // conn (connection bridge handle) -> (arrow) points to the prepare (prepare) 
    // tool which is a security function that pre-compiles the command. 
    // Pre-compiling (pre compiling) means the machine creates a structural 
    // blueprint of the command before any data is added. This stops hackers 
    // from injecting malicious code because the machine already knows the 
    // exact shape of the instruction. ( (opening bracket) "CALL AddRoute..." 
    // (quote) is the SQL procedure command string ) (closing bracket). 
    // ; (semicolon).

    $stmt->bind_param("ssssdi", $from, $to, $date, $time, $cost, $bus_id);
    // $ (dollar sign) stmt (statement handle) -> (arrow) points to the 
    // bind_param (bind parameter) tool which links real variables to the 
    // query placeholders. Binding (binding) is the process of safely pouring 
    // the user's data into the pre-compiled blueprint holes (?). This ensures 
    // the data is treated only as text or numbers, never as a command. 
    // ( (opening bracket) "ssssdi" (types: 4 strings, 1 decimal, 1 integer) 
    // , (comma) $ (dollar sign) from (departure) , (comma) $ (dollar sign) 
    // to (destination) , (comma) $ (dollar sign) date (day) , (comma) 
    // $ (dollar sign) time (hour) , (comma) $ (dollar sign) cost (price) 
    // , (comma) $ (dollar sign) bus_id (vehicle) ) (closing bracket). ; (semicolon).

    $stmt->execute();
    // $ (dollar sign) stmt (handle) -> (arrow) execute (execute) tells the database 
    // to run the pre-written procedure with the bound data. ( (opening bracket) 
    // ) (closing bracket). ; (semicolon).

    $stmt->close();
    // $ (dollar sign) stmt (handle) -> (arrow) close (close) is a cleanup tool 
    // that releases the server's memory resources after the command is 
    // finished. ( (opening bracket) ) (closing bracket). ; (semicolon).

    header("Location: admin_routes.php");
    // header (header) is the redirection tool. ( "Location: admin_routes.php" ) 
    // tells the browser to go to the routes list. ; (semicolon).

    exit();
    // exit (exit) stops the script processing. ; (semicolon).
}
// } (closing curly bracket) ends the form submission block.
?>
<!-- ? (question mark) > (greater than sign) ends the PHP logic section. -->

<!DOCTYPE html>
<!-- < (less than sign) ! (exclamation mark) DOCTYPE (document type) html (h t m l) 
> (greater than sign) standard modern web document definition. -->

<html lang="en">
<!-- < (less than sign) html (h t m l) starts the web document structure. 
lang (language) = (equals sign) "en" (English). > (greater than sign). -->

<head>
<!-- < (less than sign) head (h e a d) > (greater than sign) starts metadata. -->

    <meta charset="UTF-8">
    <!-- < (less than sign) meta (m e t a) charset (character set) = (equals sign) 
    "UTF-8" (standard encoding) > (greater than sign). -->

    <title>Add New Route - Wema Travellers</title>
    <!-- < (less than sign) title (t i t l e) > (greater than sign) sets tab text. -->

    <link rel="stylesheet" href="css/style.css">
    <!-- < (less than sign) link (l i n k) rel (relationship) = (equals sign) 
    "stylesheet" href (reference) = (equals sign) "css/style.css" > (greater than sign). -->

    <style>
    /* / (forward slash) * (asterisk) starts a CSS design section. */
        /* CSS for the Route Entry page */
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 0; background-color: #f9f9f9; }
        /* body (body) selector defines the global font and soft gray background. 
        margin: 0 (zero) removes edge gaps. */

        .container { width: 90%; margin: 50px auto; max-width: 800px; }
        /* . (dot) container (class) defines the centered wrapper width. 
        margin: 50px auto (automatic centering). */

        h2 { text-align: center; margin-bottom: 30px; color: #333; }
        /* h2 (heading level two) defines the title alignment and color. */

        form { background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        /* form (form) selector creates the white card with a soft shadow effect. */

        .form-group { margin-bottom: 20px; }
        /* . (dot) form-group (class) adds vertical spacing between inputs. */

        label { font-weight: 600; display: block; margin-bottom: 8px; color: #555; }
        /* label (label) defines the bold description text above boxes. */

        input { width: 100%; padding: 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box; }
        /* input (input) defines the typing boxes with rounded edges and gray borders. */

        .btn-submit { background-color: #4CAF50; border: none; color: white; padding: 14px 20px; font-size: 1.1rem; font-weight: bold; border-radius: 6px; cursor: pointer; display: block; margin: 0 auto; width: 100%; transition: background 0.2s; }
        /* . (dot) btn-submit (class) creates the large green save button. */

        .btn-submit:hover { background-color: #388e3c; }
        /* hover rule changes the button to dark green when the mouse touches it. */

        .back-btn { margin: 30px auto; display: block; padding: 10px 20px; text-align: center; background-color: #999; color: white; border-radius: 6px; text-decoration: none; width: fit-content; }
        /* . (dot) back-btn (class) creates the gray navigation link button. */

        .back-btn:hover { background-color: #777; }
        /* hover rule for the back button. */

    </style>
    <!-- < / (slash) style (s t y l e) > (greater than sign) ends the design section. -->

</head>
<!-- < / (slash) head (h e a d) > (greater than sign) ends configuration. -->

<body class="<?= strtolower($_SESSION['role'] ?? 'admin') ?>-role">
<!-- < (less than sign) body (b o d y) class (class) = (equals sign) starts 
the visible part of the page. [echo] [echo] prints the lower-case role name. -->

    <script src="js/header2.js"></script>
    <!-- < (less than sign) script (s c r i p t) src (source) = (equals sign) 
    "js/header2.js" > (greater than sign) imports the navigation header logic. -->

    <div style="height: 100px;"></div>
    <!-- < (less than sign) div (d i v) style (style) = (equals sign) "height: 100px;" 
    > (greater than sign) creates a vertical spacer. < / (slash) div >. -->

    <div class="container">
    <!-- < (less than sign) div (d i v) class (class) = (equals sign) "container" 
    > (greater than sign) is the main page wrapper. -->

        <h2>Register New Trip Route</h2>
        <!-- <h2> (heading level two) is the main page title. -->
        
        <form method="POST" id="addRouteForm" onsubmit="return validateForm()">
        <!-- form (f o r m) tag starts the data entry section. method (method) 
        = (equals sign) "POST" (secure transmission). onsubmit (on submit) 
        = (equals sign) "return validateForm()" (runs browser check). -->

            <div class="form-group">
            <!-- div (d i v) class (class) = (equals sign) "form-group" (layout). -->

                <label>From (Source City)</label>
                <!-- label (label) provides the text instruction for the start location. -->

                <input type="text" name="from_location" id="from_location" placeholder="e.g. Nairobi, Kenya" onmouseout="validateFrom()">
                <!-- input (i n p u t) of type (type) "text" (typing box). placeholder 
                (place holder) shows example text. onmouseout (on mouse out) 
                runs a check when the user moves away. -->
            </div>

            <div class="form-group">
                <label>To (Destination City)</label>
                <input type="text" name="to_location" id="to_location" placeholder="e.g. Kampala, Uganda" onmouseout="validateTo()">
                <!-- input field for the destination city with automatic validation 
                trigger. -->
            </div>

            <div class="form-group">
                <label>Departure Date</label>
                <input type="text" name="departure_date" id="departure_date" placeholder="YYYY-MM-DD" onmouseout="validateDate()">
                <!-- input field for the date. YYYY-MM-DD is the standard machine 
                date format. -->
            </div>

            <div class="form-group">
                <label>Departure Time</label>
                <input type="text" name="departure_time" id="departure_time" placeholder="HH:MM" onmouseout="validateTime()">
                <!-- input field for the time. HH:MM is the 24-hour clock format. -->
            </div>

            <div class="form-group">
                <label>Ticket Cost (KSH)</label>
                <input type="text" name="cost" id="cost" placeholder="99.99" onmouseout="validateCost()">
                <!-- input field for the money amount. type="text" allows custom 
                decimal validation. -->
            </div>

            <div class="form-group">
                <label>Assign Bus (System ID)</label>
                <input type="text" name="bus_id" id="bus_id" placeholder="Look up IDs in Bus Fleet Management" onmouseout="validateBusId()">
                <!-- input field for the numeric database ID of the bus. -->
            </div>

            <button type="submit" name="add_route" class="btn-submit">Save Route to Database</button>
            <!-- button tag that sends the form data to the server. -->

        </form>
        <!-- < / (slash) form > (greater than sign) ends the entry section. -->

        <script>
        // < (less than sign) script (s c r i p t) > (greater than sign) starts 
        // the browser-side interactive logic.

            function validateFrom() {
            // function (function) defines a tool named validateFrom. { (curly 
            // bracket) starts the logic.

                var val = document.getElementById("from_location").value.trim();
                // var (variable) val (label). = (equals sign) assignment. trim() 
                // cleans empty spaces from the start city name.

                if (val.length < 3) { alert("Please enter a valid source city (min 3 characters)."); document.getElementById("from_location").focus(); return false; }
                // if (if) check for short input. alert (alert) shows a pop-up. 
                // return false (return false) stops the submission.

                return true;
                // return (return) true (true) means the data is okay.
            }

            function validateTo() {
            // tool for checking the destination name.

                var val = document.getElementById("to_location").value.trim();
                if (val.length < 3) { alert("Please enter a valid destination city (min 3 characters)."); document.getElementById("to_location").focus(); return false; }
                return true;
            }

            function validateDate() {
            // tool for checking the date format using a pattern.

                var val = document.getElementById("departure_date").value.trim();
                var regex = /^\d{4}-\d{2}-\d{2}$/;
                // regex (regular expression) is a pattern checker for YYYY-MM-DD.

                if (!regex.test(val)) { alert("Please enter departure date in YYYY-MM-DD format."); document.getElementById("departure_date").focus(); return false; }
                return true;
            }

            function validateTime() {
            // tool for checking the time format using a pattern.

                var val = document.getElementById("departure_time").value.trim();
                var regex = /^([01]\d|2[0-3]):([0-5]\d)$/;
                // regex (regular expression) is a pattern checker for HH:MM.

                if (!regex.test(val)) { alert("Please enter departure time in HH:MM (24h) format."); document.getElementById("departure_time").focus(); return false; }
                return true;
            }

            function validateCost() {
            // tool for checking the price amount.

                var val = document.getElementById("cost").value;
                if (val == "" || isNaN(val) || parseFloat(val) <= 0) { alert("Please enter a valid positive cost amount."); document.getElementById("cost").focus(); return false; }
                return true;
            }

            function validateBusId() {
            // tool for checking the numeric bus identification.

                var val = document.getElementById("bus_id").value;
                if (val == "" || isNaN(val) || parseInt(val) <= 0) { alert("Please enter a valid numeric Bus System ID."); document.getElementById("bus_id").focus(); return false; }
                return true;
            }

            function validateForm() {
            // master tool that runs all individual checks before the form is sent.

                if (!validateFrom()) return false;
                if (!validateTo()) return false;
                if (!validateDate()) return false;
                if (!validateTime()) return false;
                if (!validateCost()) return false;
                if (!validateBusId()) return false;
                return true;
            }

        </script>
        <!-- < / (slash) script > (greater than sign) ends the logic. -->

        <a href="dashboard.php" class="back-btn">← Cancel & Back to Dashboard</a>
        <!-- a (anchor) tag creates a link back to the dashboard. -->

    </div>
    <!-- < / (slash) div > (greater than sign) ends the container wrapper. -->

    <script src="js/footer.js"></script>
    <!-- script tag that imports the footer logic. -->
</body>
</html>
