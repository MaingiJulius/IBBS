<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side logic engine.

/**
 * TICKET VERIFICATION CENTER (admin_verify_ticket.php)
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// TICKET VERIFICATION CENTER is the module title. * (asterisk) / (forward slash) 
// closes the block.

require_once 'db_connection.php';
// require_once (require once) is a directive that imports the database bridge file and 
// ensures it is only loaded one time to prevent errors. 'db_connection.php' (quote db 
// underscore connection dot php quote) is the file path. ; (semicolon) terminates the line.

session_start();
// session_start (session start) is the command that activates the server's memory 
// to track the user across different pages. ( ) (empty brackets) execute the tool. 
// ; (semicolon) terminates the instruction.

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'ADMIN' && $_SESSION['role'] !== 'AGENT')) {
// if (if) starts a logic check to verify the visitor's identity and security clearace. 
// ( (opening bracket) starts the condition. ! (exclamation mark) is the logical 
// NOT operator used to detect a "Missing" session. isset (is set) is a 
// built-in tool that verifies if a variable exists. ( (bracket) $ (dollar sign) 
// _ (underscore) SESSION (Superglobal Array used for server-side memory) 
// [ 'role' ] (key for user type) ) (bracket). || (double pipe) is the 
// logical OR operator. ( (bracket) $ (dollar sign) _ (underscore) SESSION 
// [ 'role' ] !== (not identical) 'ADMIN' (text) && (double ampersand) is 
// the logical AND operator. $ (dollar sign) _ (underscore) SESSION 
// [ 'role' ] !== 'AGENT' ) (bracket) ) (closing bracket). { (opening curly 
// bracket) marks the start of the denial logic.

    header("Location: login.html");
    // header (header) redirection tool sends unauthorized users to the login 
    // page. ; (semicolon).

    exit();
    // exit (exit) stops all further script execution immediately. ; (semicolon).
}
// } (closing curly bracket) ends the security check logic block.

$search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';
// $ (dollar sign) variable marker. search_id (s e a r c h underscore i d) is 
// the logical label chosen to identify the search term provided in the URL. 
// = (equals sign) assignment. isset (is set). ( (bracket) $ (dollar sign) 
// _ (underscore) GET (Superglobal Array used to pull data from the URL) 
// [ 'search_id' ] ) (bracket). ? (question mark) ternary if (short check). 
// : (colon) ternary else (fallback). '' (empty quote). ; (semicolon).

$tickets = [];
// $ (dollar sign) variable. tickets label. = (equals sign) assignment. [] 
// initializes an empty array to hold passenger bookings. ; (semicolon).

$error = '';
// $ (dollar sign) variable. error label. = (equals sign) assignment. '' 
// (empty quote). ; (semicolon).

$success_msg = '';
// $ (dollar sign) variable. success_msg label. = (equals sign). '' (empty quote). 
// ; (semicolon).

if (isset($_POST['confirm_boarding'])) {
// if (if) starts a logic check to determine if the boarding confirmation 
// button has been clicked. ( (opening bracket) starts the condition. 
// isset (is set) checks if a variable exists and has been sent to the server. 
// ( (bracket) $ (dollar sign) _ (underscore) POST [ 'confirm_boarding' ] 
// (the specific name of the submit button) ) (bracket) ) (bracket). { 
// (opening curly bracket) marks the start of the boarding update logic.

    $bid = $_POST['booking_id'];
    // $ (dollar sign) variable marker. bid (b i d) is the logical label chosen 
    // to identify the booking identification number. = (equals sign) 
    // assignment operator. $ (dollar sign) _ (underscore) POST (Superglobal 
    // Array used by the server to collect and pull data from an HTML form 
    // sent via the secure HTTP POST method. It does NOT send data to the 
    // database directly; it only captures what the user typed) [ 'booking_id' ] 
    // retrieves the specific ID. ; (semicolon).

    $sql_upd = "UPDATE bookings SET booking_status = 'CHECKED_IN' WHERE booking_id = ?";
    // $ (dollar sign) variable marker. sql_upd (s q l underscore u p d) is a 
    // logical identifier chosen to describe the database modification command. 
    // = (equals sign) assignment operator. "UPDATE..." (quote) starts the 
    // SQL instruction to change a record status to 'CHECKED_IN'. ? (question 
    // mark) is a critical security placeholder that neutralizes SQL Injection 
    // by ensuring the ID added later is treated only as a literal value. 
    // ; (semicolon) terminates the line.

    $stmt_upd = mysqli_prepare($conn, $sql_upd);
    // $ (dollar sign) variable marker. stmt_upd (s t m t underscore u p d) is 
    // the handle for the update tool object. = (equals sign) assignment. 
    // mysqli_prepare (prepare) pre-compiles the command blueprint. Pre-compiling 
    // (pre compiling) locks the structural shape of the UPDATE instruction in 
    // the database before user data is introduced. ( (opening bracket) 
    // $ (dollar sign) conn (bridge handle) , (comma) $ (dollar sign) sql_upd 
    // (the command blueprint) ) (closing bracket). ; (semicolon).

    mysqli_stmt_bind_param($stmt_upd, "i", $bid);
    // mysqli_stmt_bind_param (bind parameter) is the function that securely 
    // attaches (binds) the target ID to the placeholder. Binding (binding) is 
    // the safe act of pouring the variable into the pre-compiled blueprint 
    // hole (?). ( (opening bracket) $ (dollar sign) stmt_upd (handle) , (comma) 
    // "i" (integer type) , (comma) $ (dollar sign) bid (data) ) 
    // (closing bracket). ; (semicolon).

    if (mysqli_stmt_execute($stmt_upd)) {
    // if (if) starts a check for execution success. ( (opening bracket) 
    // mysqli_stmt_execute (execute) runs the modification. ( (bracket) 
    // $ (dollar sign) stmt_upd (handle) ) (bracket) ) (bracket). { (opening 
    // curly bracket) starts the success path.

        $success_msg = "Boarding Authorized.";
        // $ (dollar sign) variable marker. success_msg (s u c c e s s underscore 
        // m s g) is the label for the notification text. = (equals sign) 
        // assignment. "Boarding Authorized." (quote). ; (semicolon).
    }
    // } (closing curly bracket) ends the success path.

    mysqli_stmt_close($stmt_upd);
    // mysqli_stmt_close (close) terminates the tool and releases resources. 
    // ( (bracket) $ (dollar sign) stmt_upd ) (bracket). ; (semicolon).
}
// } (closing curly bracket) ends the update logic.

if ($search_id) {
// if (if) starts a check to determine if a search term has been provided. 
// ( (opening bracket) $ (dollar sign) search_id (identification) ) 
// (closing bracket). { (opening curly bracket).

    $sql_s = "SELECT b.*, r.from_location, r.to_location, r.departure_date, bs.bus_name, u.first_name, u.last_name 
            FROM bookings b 
            JOIN routes r ON b.route_id = r.route_id 
            JOIN buses bs ON b.bus_id = bs.bus_id 
            JOIN users u ON b.user_id = u.user_id
            WHERE b.passenger_id_number = ?";
    // $ (dollar sign) variable marker. sql_s (s q l underscore s) is the 
    // identifier for the search command string. = (equals sign) assignment. 
    // "SELECT..." (quote) starts the complex JOIN instruction. ? (question 
    // mark) is the security placeholder that isolates user data from the 
    // command structure. ; (semicolon).

    $stmt_s = mysqli_prepare($conn, $sql_s);
    // $ (dollar sign) variable marker. stmt_s (s t m t underscore s) is the 
    // handle for the search tool object. = (equals sign) assignment. 
    // mysqli_prepare (prepare) pre-compiles the search blueprint. Pre-compiling 
    // (pre compiling) locks the structural shape of the SELECT instruction 
    // in the database before data is introduced. ( (opening bracket) 
    // $ (dollar sign) conn (bridge handle) , (comma) $ (dollar sign) sql_s 
    // (the command blueprint) ) (closing bracket). ; (semicolon).

    mysqli_stmt_bind_param($stmt_s, "s", $search_id);
    // mysqli_stmt_bind_param attaches the string search ID to the query. 
    // Binding (binding) is the process of safely pouring the user's data 
    // into the pre-compiled blueprint holes (?). This ensures the data is 
    // treated only as text, never as a command. ( (opening bracket) 
    // $ (dollar sign) stmt_s (handle) , (comma) "s" (string type) , (comma) 
    // $ (dollar sign) search_id (data) ) (closing bracket). ; (semicolon).

    mysqli_stmt_execute($stmt_s);
    // mysqli_stmt_execute runs the search command against the database. 
    // ( (opening bracket) $ (dollar sign) stmt_s (handle) ) (closing bracket). 
    // ; (semicolon).

    $res_s = mysqli_stmt_get_result($stmt_s);
    // $ (dollar sign) variable. res_s (result search) label. = (equals sign). 
    // mysqli_stmt_get_result (get result) extracts the found data from the tool. 
    // ( (opening bracket) $ (dollar sign) stmt_s (handle) ) (closing bracket). 
    // ; (semicolon).

    $tickets = [];
    // $ (dollar sign) variable. tickets (tickets) label. = (equals sign). 
    // [ ] (empty brackets) initializes an empty list container. ; (semicolon).

    while ($row = mysqli_fetch_assoc($res_s)) {
    // while (while) starts a loop that runs as long as rows are found. ( (bracket) 
    // $ (dollar sign) row (row) label. = (equals sign) assignment. 
    // mysqli_fetch_assoc (fetch associative) converts results into a labeled 
    // list. ( (bracket) $ (dollar sign) res_s (container) ) (bracket) ) (bracket). 
    // { starts the loop block.

        $tickets[] = $row;
        // $ (dollar sign) tickets (tickets) variable. [ ] (empty brackets) 
        // appends the current row to the list. = (equals sign). $ (dollar sign) 
        // row (row). ; (semicolon).
    }
    // } ends the loop block.

    mysqli_stmt_close($stmt_s);
    // mysqli_stmt_close releases server resources. ( (opening bracket) 
    // $ (dollar sign) stmt_s (handle) ) (closing bracket). ; (semicolon).

    if (empty($tickets)) { $error = "No booking found."; }
    // if (if) checks if the tickets list is empty. ( (bracket) empty (empty) tool. 
    // ( (bracket) $ (dollar sign) tickets ) (bracket) ) (bracket). { starts. 
    // $ (dollar sign) error = (equals) "No booking found.". } ends.
}
// } (closing curly bracket) ends the search logic.
?>

<!DOCTYPE html>
<!-- < (less than) ! (exclamation) DOCTYPE html > signals HTML5 standard. -->
<html lang="en">
<!-- < (less than) html (html) lang (language) = (equals) "en" (English) > starts the document. -->
<head>
<!-- < (less than) head (head) > opens the metadata section. -->
    <meta charset="UTF-8">
    <!-- < (less than) meta (meta) charset (character set) = (equals) "UTF-8" (universal encoding) >. -->
    <title>Verification - IBBS</title>
    <!-- < (less than) title (title) > sets the browser tab name. -->
    <link rel="stylesheet" href="css/main.css">
    <!-- < (less than) link (link) rel (relation) = (equals) "stylesheet" (CSS file) href (path) = (equals) "css/main.css" >. -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Links the secondary style file for layout consistentency. -->
    <style>
    /* < (less than) style (style) > opens internal CSS block. */
        .verify-container { max-width: 600px; margin: 40px auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
        /* Defines the box size, centering, and soft shadow for the verification card. */
        .search-box { display: flex; gap: 10px; margin-bottom: 30px; }
        /* Uses flexbox to align the input and button horizontally with a gap. */
        .search-box input { flex: 1; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; }
        /* Makes the search input take all available space and adds rounded corners. */
        .result-card { border: 2px solid #e2e8f0; border-radius: 16px; padding: 25px; margin-top: 20px; }
        /* Styles the box that appears when a passenger is found. */
    </style>
    <!-- < (less than) / (slash) style (style) > closes the CSS block. -->
</head>
<!-- < (less than) / (slash) head (head) > closes the metadata section. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">
<!-- < (less than) body (body) class (class) = (equals) [php] (inject role) > starts visible content. -->
    <script src="js/header2.js"></script>
    <!-- < (less than) script (script) src (source) = (equals) "js/header2.js" > (greater than) loads navigation. -->
    
    <div style="height: 100px;"></div>
    <!-- <div> (box) style (inline) = (equals) "height: 100px;" (gap) > creates space below navbar. -->

    <div class="verify-container">
    <!-- < (less than) div (div) class (class) = (equals) "verify-container" (box name) >. -->
        <h2 style="color: var(--purple); text-align: center;">Ticket Verification</h2>
        <!-- < (less than) h2 (heading level 2) style (color purple, centered) > (greater than) Title. -->
        
        <?php if ($error): ?>
        <!-- [php] if (if). ( (bracket) $ (dollar sign) error (message) ) (bracket) : (then). -->
            <div style="color:red; text-align:center; margin-bottom:15px;"><?= $error ?></div>
            <!-- <div> (box) showing the error message in red text. -->
        <?php endif; ?>
        <!-- [php] endif (end logic). -->

        <?php if ($success_msg): ?>
        <!-- [php] if (if). ( (bracket) $ (dollar sign) success_msg (message) ) (bracket) : (then). -->
            <div style="color:green; text-align:center; margin-bottom:15px; font-weight:bold;"><?= $success_msg ?></div>
            <!-- <div> (box) showing the success message in green text. -->
        <?php endif; ?>
        <!-- [php] endif (end logic). -->

        <form method="GET" class="search-box">
        <!-- < (less than) form (form) method (method) = (equals) "GET" (visible URL data) > starts search input. -->
            <input type="text" name="search_id" placeholder="Enter Passenger ID Number" value="<?= htmlspecialchars($search_id) ?>" style="font-size:1.1rem;">
            <!-- html (HyperText) special (special) chars (characters) is a security tool 
                 that encodes text for safety. ( starts the tool. $search_id (the data) 
                 ) ends. -->
            <!-- < (less than) input (input field) name (name) = (equals) "search_id" (key) >. -->
            <button type="submit" class="button regular-button pink-background" style="width:auto; margin:0;">Search</button>
            <!-- < (less than) button (button) type (type) = (equals) "submit" (send) > (greater than) Search Button. -->
        </form>
        <!-- < (less than) / (slash) form (form) > closes the search container. -->

        <?php if (!empty($tickets)): ?>
        <!-- [php] if (if). ( (bracket) ! (not) empty (empty) tool. ( (bracket) 
             $ (dollar sign) tickets ) (bracket) ) (bracket) : (then). -->
            <?php foreach ($tickets as $ticket): ?>
            <!-- [php] foreach (for each) loops through the list. ( (bracket) 
                 $ (dollar sign) tickets as $ (dollar sign) ticket ) (bracket) : (then). -->
            <div class="result-card">
            <!-- < (less than) div (div) class (class) = (equals) "result-card" (box) >. -->
                <h3 style="margin-top:0; color:var(--purple);"><?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?></h3>
                <!-- html (HyperText) special (special) chars (characters) is a security tool 
                     that encodes text. ( starts. $ticket (data list) ['first_name'] (label) 
                     . (connects) ' ' (space) . (connects) $ticket ['last_name'] (label) ) ends. -->
                <!-- <h3> (heading 3) displays the passenger's full name. -->
                
                <p><strong>📍 Route:</strong> <?= htmlspecialchars($ticket['from_location']) ?> → <?= htmlspecialchars($ticket['to_location']) ?></p>
                <!-- htmlspecialchars (security tool) ( $ticket ['from_location'] (origin) ) -->
                <!-- <p> (paragraph) shows travel origin and destination. -->
                
                <p><strong>📅 Date:</strong> <?= $ticket['departure_date'] ?></p>
                <!-- <p> (paragraph) shows travel date. -->
                
                <p><strong>💺 Bus:</strong> <?= htmlspecialchars($ticket['bus_name']) ?> | <strong>Seat:</strong> <?= $ticket['seat_number'] ?></p>
                <!-- htmlspecialchars (security tool) ( $ticket ['bus_name'] (vehicle label) ) -->
                <!-- <p> (paragraph) shows vehicle and seat identifier. -->
                
                <div style="margin-top:20px; padding:15px; background:#f8fafc; border-radius:8px; border-left:4px solid var(--purple);">
                    <p style="margin:0;"><strong>Current Status:</strong> <span style="color:<?= $ticket['booking_status'] === 'CHECKED_IN' ? '#22c55e' : '#f59e0b' ?>; font-weight:800;"><?= $ticket['booking_status'] ?></span></p>
                    <!-- Displays the status in green if checked in, orange if just paid. -->
                </div>

                <?php if ($ticket['booking_status'] === 'PAID'): ?>
                <!-- [php] if (if). ( (bracket) status is identical to 'PAID' ) (bracket) : (then). -->
                    <form method="POST" style="margin-top:25px;">
                    <!-- < (less than) form (form) method (method) = (equals) "POST" (secure data) >. -->
                        <input type="hidden" name="booking_id" value="<?= $ticket['booking_id'] ?>">
                        <!-- < (less than) input (input) type (type) = (equals) "hidden" (invisible) > stores ID. -->
                        <button type="submit" name="confirm_boarding" class="button regular-button" style="background:#22c55e; color:white; border:none; box-shadow:0 4px 10px rgba(34,197,94,0.3);">✅ Authorize Boarding</button>
                        <!-- < (less than) button (button) > triggers the database update. -->
                    </form>
                    <!-- < (less than) / (slash) form (form) > ends the confirmation tool. -->
                <?php endif; ?>
                <!-- [php] endif (end check). -->
            </div>
            <!-- < (less than) / (slash) div (div) > ends result card. -->
            <?php endforeach; ?>
            <!-- [php] endforeach ends the loop. -->
        <?php endif; ?>
        <!-- [php] endif (end check). -->
    </div>
    <!-- < (less than) / (slash) div (div) > ends main container. -->

    <div style="height: 100px;"></div>
    <!-- Spacer at bottom. -->
    
    <script src="js/footer.js"></script>
    <!-- Loads global footer logic. -->
</body>
<!-- < (less than) / (slash) body (body) > ends document content. -->
</html>
<!-- < (less than) / (slash) html (html) > ends the entire page. -->

<?php 
// [php] starts cleanup.
mysqli_close($conn); 
// mysqli_close (close) terminates the database connection bridge. ; (semicolon).
?>
<!-- [php] ends. -->
