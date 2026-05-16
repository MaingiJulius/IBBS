<?php
// < (less than sign) ? (question mark) php (PHP: Hypertext Preprocessor) is the opening 
// tag that starts the server-side logic engine.

/**
 * PASSENGER MANIFESTS (admin_manifests.php)
 */
// / (forward slash) * (asterisk) * (asterisk) opens a professional documentation block.
// PASSENGER MANIFESTS is the module title. * (asterisk) / (forward slash) 
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

$filter_country = isset($_GET['country']) ? $_GET['country'] : '';
// $ (dollar sign) creates a variable. filter_country (filter country) is the label. 
// = (equals sign) assigns the data. isset (is set) checks if the URL has a country 
// filter. ( $ (dollar sign) _ (underscore) GET [ 'country' ] ) (bracket). ? (question 
// mark) is the ternary "if" operator. : (colon) is the ternary "else" operator. 
// '' (quote quote) is an empty value. ; (semicolon) terminates the line.

$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
// $ (dollar sign) variable. filter_date (filter date) label. = (equals sign) 
// assignment. isset check for URL date parameter. ? (then). : (else) empty string. 
// ; (semicolon).

$sql_c = "SELECT DISTINCT SUBSTRING_INDEX(to_location, ', ', -1) as country FROM routes ORDER BY country ASC";
// $ (dollar sign) variable. sql_c (sql countries) label for the country list command. 
// = (equals sign) assignment. "SELECT..." (quote) is the SQL instruction to extract 
// unique country names from locations. ; (semicolon).

$res_c = mysqli_query($conn, $sql_c);
// $ (dollar sign) variable. res_c (result countries) container. = (equals sign) 
// assignment. mysqli (MySQL Improved) _ (underscore) query (query) is the tool 
// that sends the command through the bridge. ( (opening bracket) $ (dollar sign) 
// conn (connection bridge handle) , (comma) $ (dollar sign) sql_c (database 
// command) ) (closing bracket). ; (semicolon).

$sql_t = "SELECT r.*, b.bus_name, b.reg_no, d.full_name as driver_name, (SELECT COUNT(*) FROM bookings WHERE route_id = r.route_id AND booking_status != 'CANCELLED') as passenger_count FROM routes r JOIN buses b ON r.bus_id = b.bus_id LEFT JOIN drivers d ON b.driver_id = d.driver_id WHERE 1=1";
// $ (dollar sign) variable. sql_t (sql trips) label. = (equals sign) assignment. 
// "SELECT..." (quote) is the complex command to find all trips and their 
// details. ; (semicolon).

if ($filter_country) { $sql_t .= " AND r.to_location LIKE '%" . mysqli_real_escape_string($conn, $filter_country) . "%'"; }
// if (if) check for country filter. ( $ (dollar sign) filter_country ) (bracket). 
// { (curly bracket) starts logic. $sql_t (label) .= (dot equals) appends text. 
// " AND..." (quote) SQL filter. . (dot) connects strings. mysqli_real_escape_string 
// (MySQL Improved real escape string) is the security tool that cleans input. 
// ( (bracket) $ (dollar sign) conn (bridge) , (comma) $ (dollar sign) 
// filter_country (data) ) (bracket). } (closing curly bracket).

if ($filter_date) { $sql_t .= " AND r.departure_date = '" . mysqli_real_escape_string($conn, $filter_date) . "'"; }
// if (if) check for date filter. ( $ (dollar sign) filter_date ) (bracket). 
// { (curly bracket). $sql_t (label) .= (dot equals) appends date filter. 
// mysqli_real_escape_string (security tool) ( (bracket) $ (dollar sign) 
// conn (bridge) , (comma) $ (dollar sign) filter_date (data) ) (bracket). 
// } (closing curly bracket).

$sql_t .= " ORDER BY r.departure_date ASC";
// $ (dollar sign) sql_t (label) .= (dot equals) appends the sorting instruction. 
// " ORDER BY..." (quote) tells the machine to sort by date. ; (semicolon).

$res_t = mysqli_query($conn, $sql_t);
// $ (dollar sign) variable. res_t (result trips) container. = (equals sign) 
// assignment. mysqli_query runs the final command. ( (opening bracket) 
// $ (dollar sign) conn (bridge) , (comma) $ (dollar sign) sql_t (command) 
// ) (closing bracket). ; (semicolon).
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

    <title>Passenger Manifests - IBBS</title>
    <!-- < (less than sign) title (t i t l e) > (greater than sign) sets the label 
    on the browser's tab. < / (slash) title > (greater than sign). -->

    <link rel="stylesheet" href="css/main.css">
    <!-- < (less than sign) link (l i n k) rel (relationship) = (equals sign) 
    "stylesheet" href (reference) = (equals sign) "css/main.css" > (greater than sign). -->

    <link rel="stylesheet" href="css/style.css">
    <!-- < (less than sign) link (l i n k) rel (relationship) = (equals sign) 
    "stylesheet" href (reference) = (equals sign) "css/style.css" > (greater than sign). -->

    <style>
    /* / (forward slash) * (asterisk) starts a CSS design section. */

        .manifest-container { max-width: 1200px; margin: 30px auto; }
        /* . (dot) manifest-container (class) defines the look of the main wrapper. */

        .filter-section { background: white; padding: 20px; border-radius: 10px; display: flex; gap: 10px; align-items: flex-end; }
        /* . (dot) filter-section (class) defines the look of the search tools area. */

        .trip-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px; }
        /* . (dot) trip-grid (class) defines the flexible 2D grid layout. */

        .trip-card { background: white; border-radius: 10px; padding: 20px; border-left: 5px solid var(--purple); }
        /* . (dot) trip-card (class) defines the look of individual trip boxes. */

    </style>
    <!-- < / (slash) style (s t y l e) > (greater than sign) ends the design section. -->

</head>
<!-- < / (slash) head (h e a d) > (greater than sign) ends configuration. -->

<body class="admin-role">
<!-- < (less than sign) body (b o d y) class (class) = (equals sign) "admin-role" 
> (greater than sign) starts the visible page content. -->

    <script src="js/header2.js"></script>
    <!-- < (less than sign) script (s c r i p t) src (source) = (equals sign) 
    "js/header2.js" > (greater than sign) imports the navigation bar logic. < / 
    (slash) script > (greater than sign). -->

    <div style="height: 100px;"></div>
    <!-- < (less than sign) div (d i v) style (style) = (equals sign) "height: 100px;" 
    > (greater than sign) is a spacer element. < / (slash) div > (greater than sign). -->

    <div class="manifest-container">
    <!-- < (less than sign) div (d i v) class (class) = (equals sign) 
    "manifest-container" > (greater than sign) starts the main data container. -->

        <h2 style="color: var(--purple);">📋 Passenger Manifests</h2>
        <!-- < (less than sign) h2 (heading level two) > (greater than sign) starts the 
        section title with purple design. -->
        
        <form class="filter-section" method="GET">
        <!-- < (less than sign) form (f o r m) > (greater than sign) starts the search 
        input area. method (method) = (equals sign) "GET" (visibility in URL). -->

            <select name="country" class="input">
            <!-- < (less than sign) select (s e l e c t) > (greater than sign) creates 
             a dropdown menu for country selection. -->

                <option value="">All Countries</option>
                <!-- < (less than sign) option (o p t i o n) > (greater than sign) is a 
                default choice that clears the filter. -->

                <?php while($c = mysqli_fetch_assoc($res_c)): ?>
                <!-- [php] (start PHP). while (while) starts a loop for countries. 
                ( $c (row) = mysqli_fetch_assoc (fetch data) from ( $res_c ) list ). 
                : (colon) starts the loop block. [?] (stop PHP). -->

                    <option value="<?= $c['country'] ?>" <?= $filter_country == $c['country'] ? 'selected' : '' ?>><?= $c['country'] ?></option>
                    <!-- option tag that prints the country name. [echo] (echo). 
                    $filter_country == $c['country'] (check) ? (then) 'selected' 
                    (mark as active) : (else) '' (nothing). -->

                <?php endwhile; ?>
                <!-- endwhile (end while) closes the country loop. -->

            </select>
            <!-- < / (slash) select > (greater than sign) ends the dropdown. -->

            <input type="date" name="date" class="input" value="<?= $filter_date ?>">
            <!-- input tag for a calendar picker. type (type) = (equals sign) "date". 
            value (value) prints the current filter choice. -->

            <button type="submit" class="button pink-background">Filter Trips</button>
            <!-- button tag that sends the form data to the server for processing. -->

        </form>
        <!-- < / (slash) form > (greater than sign) ends the search area. -->

        <div class="trip-grid">
        <!-- < (less than sign) div (d i v) class (class) = (equals sign) "trip-grid" 
        > (greater than sign) starts the results layout. -->

            <?php if (mysqli_num_rows($res_t) > 0): ?>
            <!-- [php] (start PHP). if (if) check for results. ( mysqli_num_rows 
            (count results) from ( $res_t ) > 0 ) (bracket). : (colon) starts the 
            display block. [?] (stop PHP). -->

                <?php while($trip = mysqli_fetch_assoc($res_t)): ?>
                <!-- while (while) starts a loop for every trip found. $trip (row) 
                = mysqli_fetch_assoc (fetch data) from ( $res_t ) list. -->

                    <div class="trip-card">
                    <!-- < (less than sign) div (d i v) class (class) = (equals sign) 
                    "trip-card" > (greater than sign) starts the box for one trip. -->

                        <h3><?= htmlspecialchars($trip['from_location']) ?> → <?= htmlspecialchars($trip['to_location']) ?></h3>
                        <!-- html (HyperText) special (special) chars (characters) is a security tool 
                             that encodes text for safety. ( starts the tool. $trip (data row) 
                             ['from_location'] (origin label) ) ends. -->
                        <!-- <h3> (heading level three) displaying the route start and end. -->

                        <p>Date: <?= $trip['departure_date'] ?> | Bus: <?= htmlspecialchars($trip['bus_name']) ?></p>
                        <!-- htmlspecialchars (security tool) ( $trip ['bus_name'] (vehicle label) ) -->
                        <!-- <p> (paragraph) displaying the calendar date and vehicle name. -->

                        <p>Passengers: <?= $trip['passenger_count'] ?></p>
                        <!-- <p> (paragraph) displaying the number of travelers booked. -->

                        <button class="button" style="background:var(--purple); color:white;" onclick="window.open('get_manifest_data.php?route_id=<?= $trip['route_id'] ?>', '_blank')">View & Print Manifest</button>
                        <!-- button tag with onclick (on click) trigger. window.open 
                        (browser tool) opens the manifest generator in a new tab. -->

                    </div>
                    <!-- < / (slash) div > (greater than sign) ends the trip box. -->

                <?php endwhile; ?>
                <!-- endwhile (end while) closes the trip loop. -->

            <?php else: ?>
            <!-- else (else) branch if zero results were found in the database. -->

                <p>No trips found matching filters.</p>
                <!-- <p> (paragraph) message for no results. -->

            <?php endif; ?>
            <!-- endif (end if) closes the results check. -->

        </div>
        <!-- < / (slash) div > (greater than sign) ends the grid. -->

    </div>
    <!-- < / (slash) div > (greater than sign) ends the container. -->

    <script src="js/footer.js"></script>
    <!-- script tag that imports the footer logic. -->

</body>
<!-- < / (slash) body > (greater than sign) ends page content. -->

</html>
<!-- < / (slash) html > (greater than sign) ends the document. -->

<?php mysqli_close($conn); ?>
<!-- mysqli (MySQL Improved) _ (underscore) close (close) is the tool that 
formally shuts the bridge to the database. ( $conn (bridge) ) (bracket). 
; (semicolon) terminates the instruction. -->
