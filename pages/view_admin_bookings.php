<?php
// <?php (opening tag) tells the server to start interpreting the code as PHP.

/**
 * ADMINISTRATION: BOOKING COMMAND CENTER (view_admin_bookings.php)
 */

// require_once (require once) includes the database connection.
require_once 'db_connection.php';
// require_once (require once) includes the logger utility.
require_once 'logger.php';
// session_start (session start) starts the user session.
session_start();

// --- SECURITY CHECK ---
// if (if) checks if the user is authorized as an ADMIN or AGENT.
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'ADMIN' && $_SESSION['role'] != 'AGENT')) {
    die("Access Denied: Staff Authorization Required.");
}

// --- CANCEL LOGIC ---
if (isset($_GET['cancel_booking'])) {
    $bid = $_GET['cancel_booking'];
    $sql_can = "UPDATE bookings SET booking_status = 'CANCELLED' WHERE booking_id = ?";
    $stmt_can = mysqli_prepare($conn, $sql_can);
    /* $stmt_can (handle) = (assignment). 
       mysqli (MySQL Improved) _ (underscore) prepare (prepare) is the security 
       function that pre-compiles the command blueprint. 
       "Improved" (mysqli) is used because it supports secure "Prepared Statements" 
       using placeholders like ? to prevent SQL Injection. 
       ( starts. $conn (bridge) , (comma) $sql_can (blueprint) ) ends. ; (semicolon). */

    mysqli_stmt_bind_param($stmt_can, "i", $bid);
    /* mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
       bind (bind) _ (underscore) param (parameter) is the function that 
       securely pours the data into the ? placeholders. 
       The ? (Question Mark) is a safety hole that ensures user data 
       is never treated as a command. 
       ( starts. $stmt_can (tool handle) , (comma) "i" (integer number type) 
       , (comma) $bid (booking identity data) ) ends. ; (semicolon). */
    /* mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
       bind (bind) _ (underscore) param (parameter) is the security function that 
       attaches (binds) the target ID to the placeholder. ( starts the tool. 
       $stmt_can (tool handle) , (comma) "i" (integer number type) , (comma) 
       $bid (booking identity data) ) ends the tool. ; (semicolon). */
    mysqli_stmt_execute($stmt_can);
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Cancelled Ticket: $bid");
    mysqli_stmt_close($stmt_can);
    header("Location: view_admin_bookings.php?msg=Ticket cancelled.");
    exit();
}

// --- DELETE LOGIC ---
if (isset($_GET['delete_booking'])) {
    $bid = $_GET['delete_booking'];
    $sql_del = "DELETE FROM bookings WHERE booking_id = ?";
    $stmt_del = mysqli_prepare($conn, $sql_del);
    mysqli_stmt_bind_param($stmt_del, "i", $bid);
    mysqli_stmt_execute($stmt_del);
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Deleted Booking: $bid");
    mysqli_stmt_close($stmt_del);
    header("Location: view_admin_bookings.php?msg=Record deleted.");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Ledger - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">

    <script>
        // [Extreme Audit-Ready Documentation]
        function printTicket(id, name, from, to, date, time, bus, seat) {
// function (function keyword) starts the definition. printTicket (p r i n t T i c k e t) 
// is the logical label for the printing task. ( (opening bracket) starts the input 
// list. id (booking reference) , (comma) name (passenger) , (comma) from (origin) 
// , (comma) to (destination) , (comma) date (travel day) , (comma) time (hour) 
// , (comma) bus (vehicle) , (comma) seat (assignment) ) (closing bracket) 
// { (opening curly bracket) marks the start of the instructions.

            const printWin = window.open('', '', 'height=600,width=800');
// const (constant). printWin (variable name). = (assignment). window (browser) 
// . (dot) open (open tool). ( (bracket) '' (empty URL) , '' (no name) , 
// 'height=600,width=800' (window size) ) (bracket). ; (semicolon).

            printWin.document.write('<html><head><title>Ticket Printout</title>');
// printWin.document.write (write). ( '<html>...' ) ; This pours the header 
// code into the new window.

            printWin.document.write('<style>.no-print { display: none !important; } body { font-family: sans-serif; }</style>');
// printWin.document.write (write). ( '<style>...' ) ; This adds the visual rules.

            printWin.document.write('</head><body>');
// printWin.document.write (write). ( '</head><body>' ) ; Closes head, starts body.

            printWin.document.write('<div style="padding:40px; border:5px solid #9a4d9a; border-radius:15px; max-width:600px; margin:20px auto;">');
// printWin.document.write (write). ( '<div style="..."' ) ; Creates a purple 
// bordered box to act as the physical ticket frame.

            printWin.document.write('<h1 style="color:#9a4d9a; text-align:center;">WEMA TRAVELLERS</h1>');
// printWin.document.write (write). ( '<h1 style="..."' ) ; Adds the company branding.

            printWin.document.write('<h2 style="text-align:center; border-bottom:2px solid #eee; padding-bottom:10px;">OFFICIAL TRAVEL TICKET</h2>');
// printWin.document.write (write). ( '<h2 style="..."' ) ; Adds the document title.

            printWin.document.write('<p><strong>Booking Ref:</strong> #' + id + '</p>');
// printWin.document.write (write). ( '<p>... #' + id + '</p>' ) ; Displays the ID.

            printWin.document.write('<p><strong>Passenger:</strong> ' + name + '</p>');
// printWin.document.write (write). ( '<p>... ' + name + '</p>' ) ; Displays the name.

            printWin.document.write('<p><strong>Route:</strong> ' + from + ' &rarr; ' + to + '</p>');
// printWin.document.write (write). ( '<p>... ' + from + ' &rarr; ' + to + '</p>' ) ; 
// Displays the travel path using the HTML arrow symbol (&rarr;).

            printWin.document.write('<p><strong>Departure:</strong> ' + date + ' at ' + time + '</p>');
// printWin.document.write (write). ( '<p>... ' + date + ' at ' + time + '</p>' ) ; 
// Displays the schedule.

            printWin.document.write('<p><strong>Vehicle:</strong> ' + bus + ' | <strong>Seat:</strong> ' + seat + '</p>');
// printWin.document.write (write). ( '<p>... ' + bus + ' | ... ' + seat + '</p>' ) ; 
// Displays the logistics info.

            printWin.document.write('<div style="margin-top:30px; text-align:center; font-size:0.8em; color:#666;">Please arrive 30 mins before departure. Valid ID required.</div>');
// printWin.document.write (write). ( '<div style="..."' ) ; Adds the legal disclaimer.

            printWin.document.write('</div>');
// printWin.document.write (write). ( '</div>' ) ; Closes the ticket box.

            printWin.document.write('<script>window.onload = function() { window.print(); window.close(); };<\/script>');
// printWin.document.write (write). ( '<script>...' ) ; Adds the automated 
// print-and-close behavior.

            printWin.document.write('</body></html>');
// printWin.document.write (write). ( '</body></html>' ) ; Finalizes the HTML structure.

            printWin.document.close();
// printWin.document.close (close) ; This signals to the browser that the data 
// stream is complete and the page is ready to render.
        }
// } (closing curly bracket) ends the function block.
    </script>
</head>

<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <div class="view-container">
        <h2 style="color: var(--purple);">🎟️ Booking Transaction Ledger</h2>
        
        <?php if(isset($_GET['msg'])): ?><div style="color:green; font-weight:bold;"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>

        <table class="crud-table">
            <thead>
                <tr>
                    <th>Ref ID</th><th>Date</th><th>Passenger</th><th>Route</th><th>Seat</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_list = "SELECT b.*, u.first_name, u.last_name, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name 
                             FROM bookings b 
                             JOIN users u ON b.user_id = u.user_id 
                             JOIN routes r ON b.route_id = r.route_id 
                             JOIN buses bs ON b.bus_id = bs.bus_id
                             ORDER BY b.booking_id DESC";
                $res_list = mysqli_query($conn, $sql_list);
                /* $res_list (result list) = (assignment). 
                   mysqli_query (MySQL Improved query) is the command that sends 
                   the instruction to the database. "Improved" (mysqli) is used 
                   everywhere in the system to ensure modern security and 
                   faster performance. ( starts. $conn (bridge) , (comma) 
                   $sql_list (instruction) ) ends. ; (semicolon). */
                while($row = mysqli_fetch_assoc($res_list)):
                /* while (while) starts a loop. $row (row container) pulls data. 
                   mysqli_fetch_assoc (fetch associative) converts raw data into labeled pieces. 
                   ( starts. $res_list (result source). ) ends. : (colon) starts the loop block. */
                ?>
                <tr>
                    <td><?= $row['booking_id'] ?></td>
                    <td><?= $row['booking_time'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($row['passenger_name']) ?></strong>
                        <!-- html (HyperText) special (special) chars (characters) is a security tool 
                             that encodes text for safety. ( starts the tool. $row (row data) 
                             ['passenger_name'] (specific label) ) ends. -->
                        <div style="font-size:0.8em; color:grey;">By: <?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></div>
                        <!-- htmlspecialchars (security tool) ( $row ['first_name'] . ' ' . $row ['last_name'] ) -->
                    </td>
                    <td><?= htmlspecialchars($row['from_location'].' to '.$row['to_location']) ?></td>
                    <!-- htmlspecialchars (security tool) ( $row ['from_location'] . ' to ' . $row ['to_location'] ) -->
                    <td><?= $row['seat_number'] ?></td>
                    <td><span style="color:<?= ($row['booking_status']=='CANCELLED'?'red':'green') ?>; font-weight:bold;"><?= $row['booking_status'] ?></span></td>
                    <td>
                        <?php if($row['booking_status'] != 'CANCELLED'): ?>
                            <button onclick="printTicket('<?= $row['booking_id'] ?>', '<?= addslashes(htmlspecialchars($row['passenger_name'])) ?>', '<?= addslashes(htmlspecialchars($row['from_location'])) ?>', '<?= addslashes(htmlspecialchars($row['to_location'])) ?>', '<?= $row['departure_date'] ?>', '<?= $row['departure_time'] ?>', '<?= addslashes(htmlspecialchars($row['bus_name'])) ?>', '<?= $row['seat_number'] ?>')" class="action-btn btn-print">PRINT</button>
<!-- [new] < (less than) button (button element) onclick (on click event) = (equals) 
     "printTicket( ... )" (the function call) > (greater than) PRINT (label) 
     < (less than) / (slash) button (button) > (greater than). 
     This button triggers the virtual ticket generator. We pass the row data 
     (id, name, locations, times, bus, seat) as arguments so the printer 
     knows exactly what to display on the physical paper. -->

                            <a href="?cancel_booking=<?= $row['booking_id'] ?>" class="action-btn btn-cancel" onclick="return confirm('Cancel this?')">CANCEL</a>
                        <?php endif; ?>
                        <a href="?delete_booking=<?= $row['booking_id'] ?>" class="action-btn btn-delete" onclick="return confirm('Delete permanently?')">DELETE</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div style="height: 120px;"></div>
    <script src="js/footer.js"></script>
    <div style="text-align:center;"><a href="dashboard.php" style="color:var(--purple); font-weight:bold;">← Back to Dashboard</a></div>
</body>
</html>
<?php mysqli_close($conn); ?>
