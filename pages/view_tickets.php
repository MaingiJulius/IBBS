<?php                                                                // [1] <?php (opening tag) starts PHP mode.
/**                                                                  // [2] /** (star-star) opens documentation block.
 * PASSENGER PORTAL: MY DIGITAL TICKETS (view_tickets.php)           // [3] Title identifying the file's purpose.
 */                                                                  // [4] */ (star-slash) closes documentation block.

require_once 'db_connection.php';                                    // [5] require_once (require once) imports database bridge. ; (semicolon).
session_start();                                                    // [6] session_start (session start) activates user memory. ( ) (bracket). ; (semicolon).

if (!isset($_SESSION['user_id'])) {                                  // [7] if (if) starts check. ( (bracket). ! (not). isset (is set). ( (bracket). $_SESSION (session). [ 'user_id' ] (key). ) (bracket). ) (bracket). { (curly bracket) starts block.
    header("Location: login.html");                                  // [8] header (header) redirects browser. ( "Location..." ). ; (semicolon).
    exit();                                                          // [9] exit (exit). ( ). ; (semicolon).
}                                                                    // [10] } (curly bracket) ends block.

$user_id = $_SESSION['user_id'];                                     // [11] $user_id (variable). = (assign). $_SESSION['user_id']. ; (semicolon).

// --- DATABASE FETCHING (Procedural Style) ---                      // [12] // (double slash) comment describing the next step.
$sql = "SELECT b.*, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name 
        FROM bookings b 
        JOIN routes r ON b.route_id = r.route_id 
        JOIN buses bs ON b.bus_id = bs.bus_id 
        WHERE b.user_id = ? AND b.booking_status IN ('PAID', 'CHECKED_IN') 
        ORDER BY r.departure_date DESC";                             // [13] $sql (variable). = (assign). "SELECT..." (SQL command). ; (semicolon).

$stmt = mysqli_prepare($conn, $sql);                                 // [14] $stmt (variable). = (assign). mysqli_prepare (prepare). ( $conn (bridge) , $sql (query) ). ; (semicolon).
mysqli_stmt_bind_param($stmt, "i", $user_id);                        
/* mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
   bind (bind) _ (underscore) param (parameter) is the security function that 
   attaches (binds) the data to the query. ( starts the tool. $stmt (tool handle) 
   , (comma) "i" (integer number type) , (comma) $user_id (current user identity data) 
   ) ends the tool. ; (semicolon). */
mysqli_stmt_execute($stmt);                                          // [16] mysqli_stmt_execute (execute). ( $stmt ). ; (semicolon).
$result = mysqli_stmt_get_result($stmt);                             // [17] $result (variable). = (assign). mysqli_stmt_get_result (get result). ( $stmt ). ; (semicolon).
?>                                                                   <!-- [18] [?] (closing tag) ends PHP and starts HTML. -->

<!DOCTYPE html>                                                      <!-- [19] <!DOCTYPE html> defines a standard modern web document. -->
<html lang="en">                                                     <!-- [20] <html> starts the web page structure. -->
<head>                                                               <!-- [21] <head> contains hidden metadata. -->
    <meta charset="UTF-8">                                           <!-- [22] <meta> sets text encoding. -->
    <title>My Boarding Passes - Wema Travellers</title>              <!-- [23] <title> sets browser tab text. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [24] <link> imports design rules. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [25] <link> imports branding colors. -->
    <style>                                                          /* [26] <style> starts CSS design. */
        .ticket-container { max-width: 1200px; margin: 50px auto; padding: 20px; } /* [27] Wider centered box. */
        .ticket-card { background: #ffffff; border-radius: 16px; padding: 30px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-left: 12px solid var(--purple); } /* [28] Ticket card design. */
        .ticket-info h3 { margin: 0 0 10px 0; color: var(--purple); font-size: 1.4rem; } /* [29] Destination names design. */
        .booking-id-badge { background: var(--purple); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; margin-bottom: 5px; display: inline-block; } /* [30] ID badge design. */
        .action-btn { padding: 10px 20px; border-radius: 50px; cursor: pointer; font-size: 0.85rem; font-weight: 700; border: none; text-decoration: none; display: inline-block; text-align: center; } /* [31] Button design. */
        @media print { .no-print { display: none; } }               /* [32] @media print (print mode) hides .no-print elements. */
    </style>                                                         <!-- [33] </style> ends CSS. -->
    <script>
// < (less than) s (s) c (c) r (r) i (i) p (p) t (t) > (greater than) starts 
// the JavaScript logic block for browser-side interactions.

        function printTicket(bookingId) {
// function (function keyword) defines a reusable task. printTicket 
// (p r i n t T i c k e t) is the unique name of this logic block. ( (bracket) 
// bookingId (b o o k i n g I d) is a variable input representing the 
// specific ticket number. ) (bracket). { (opening curly bracket) marks the 
// start of the print processing instructions.

            const cardInfo = document.querySelector('#ticket-' + bookingId + ' .ticket-info');
// const (constant) defines a variable that cannot be changed. cardInfo 
// (c a r d I n f o) is the label for the ticket data we want to print. 
// = (equals sign) is the assignment operator. document (the webpage object) 
// . (dot) querySelector (query selector) is a powerful function used to 
// find a specific element. ( (bracket) '#' (hash) + (plus sign) bookingId 
// + (plus sign) ' .ticket-info' (the specific class inside the card) ) 
// (bracket). ; (semicolon) terminates the line. This variable stores the 
// "ticket-info" container to exclude buttons.

            const content = cardInfo.innerHTML;
// const (constant). content (c o n t e n t) is the label for the HTML text. 
// = (equals sign). cardInfo (our found data box) . (dot) innerHTML 
// (i n n e r H T M L) is the built-in property that retrieves all the tags 
// and text inside that box. ; (semicolon).

            const printWin = window.open('', '', 'height=600,width=800');
// const (constant). printWin (p r i n t W i n) is the variable label for the 
// new popup window. = (equals sign). window (the browser) . (dot) open 
// (open) is the function that creates a new display area. ( (bracket) 
// '' (empty URL) , (comma) '' (no name) , (comma) 'height=600,width=800' 
// (size settings) ) (bracket). ; (semicolon).

            printWin.document.write('<html><head><title>Ticket Printout</title>');
// printWin (our new window) . (dot) document (its content object) . (dot) 
// write (write) is the function that pours data into the window. ( (bracket) 
// '<html>...' (the HTML header code) ) (bracket). ; (semicolon).

            printWin.document.write('<style>.no-print { display: none !important; }</style>');
// printWin.document.write adds a specific style rule to the new window. 
// .no-print { display: none !important; } ensures that any element with 
// the "no-print" class is hidden in the popup.

            printWin.document.write('</head><body>');
// printWin.document.write closes the head and starts the body.

            printWin.document.write('<div style="font-family:sans-serif; padding:40px; border:5px solid #8e44ad; border-radius:15px; position:relative;">');
// printWin.document.write pours a styled <div> (box) with a purple border 
// and padding into the window to make the ticket look professional.

            printWin.document.write('<h1 style="color:#8e44ad; text-align:center; font-size: 2rem;">WEMA TRAVELLERS</h1>');
// printWin.document.write adds the branding title in purple to the top of 
// the printed page.

            printWin.document.write(content);
// printWin.document.write (write). ( (bracket) content (our captured ticket 
// data) ) (bracket). ; (semicolon). This is the CORE logic that places the 
// passenger information into the print window.

            printWin.document.write('</div>');
// printWin.document.write closes the styled box tags.

            printWin.document.write('<script>window.onload = function() { window.print(); window.close(); };<\/script>');
// printWin.document.write adds an automated script to the new window. 
// window.onload (on load) waits for the text to appear. window.print() 
// (print) triggers the printer. window.close() (close) shuts the popup 
// automatically after printing.

            printWin.document.write('</body></html>');
// printWin.document.write closes the body and html tags.

            printWin.document.close();
// printWin (window) . (dot) document (content) . (dot) close (close) 
// is the function that finalizes the data stream so the browser knows 
// everything is ready. ; (semicolon).

        }

// } (closing curly bracket) ends the printTicket function block.

        function validateInlineForm(bookingId) {
// function (function) validateInlineForm (v a l i d a t e I n l i n e F o r m) 
// is the name of the check logic. ( bookingId ) { starts the instructions.

            var pid = document.getElementById("pid-" + bookingId).value.trim();
// var (variable). pid (p i d). = (assign). document . getElementById 
// (find by ID). ( "pid-" + bookingId ). value (text). trim() (remove space). ;

            var age = document.getElementById("age-" + bookingId).value.trim();
// var (variable). age (a g e). = (assign). value. trim(). ;

            if (pid == "") {
// if (if) check for emptiness. ( (bracket) pid == "" ) (bracket). { 
// (bracket) starts error.

                alert("ID Number is required");
// alert (pop-up). ; (semicolon).

                return false;
// return (return) false (fail signal). ; (semicolon).

            }
// } ends if.

            if (age == "" || isNaN(age)) {
// if (if) check for valid age. ( age is empty OR isNaN (is NOT a number) ).

                alert("Valid Age is required");
// alert (pop-up). ; (semicolon).

                return false;
// return (return) false (fail signal). ; (semicolon).

            }
// } ends if.

            return true;
// return (return) true (success signal). ; (semicolon).

        }
// } (closing curly bracket) ends the validation function.
    </script>
                                                        <!-- [55] </script> ends browser logic. -->
</head>                                                              <!-- [56] </head> ends metadata. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">             <!-- [57] <body> starts content. [echo] [echo] prints role class. -->
    <div class="no-print">                                           <!-- [58] <div> (box). class="no-print" hides it. -->
        <script src="js/header2.js"></script>                        <!-- [59] Imports navigation bar. -->
    </div>                                                           <!-- [60] </div> ends box. -->
    <div style="height: 100px;"></div>                               <!-- [61] Spacer box. -->

    <div class="ticket-container">                                   <!-- [62] Opens main list box. -->
        <h2 style="text-align:center; color: var(--purple); margin-bottom: 40px; font-weight: 800;">🎫 My Digital Travel Wallet</h2> <!-- [63] Heading. -->
        
        <?php if (mysqli_num_rows($result) > 0): ?>                  <!-- [64] [php] (start). if (if). ( mysqli_num_rows (count rows) > 0 ). : (then). [?] (end). -->
            <?php while($ticket = mysqli_fetch_assoc($result)): ?>   
                <!-- while (while) starts a loop. $ticket (row container) pulls data. 
                     mysqli_fetch_assoc (fetch associative) converts raw data into labeled pieces. 
                     ( starts. $result (result source). ) ends. : (colon) starts the loop block. -->
                <div class="ticket-card" id="ticket-<?= $ticket['booking_id'] ?>"> <!-- [66] Ticket box with unique ID. -->
                    <div class="ticket-info">                        <!-- [67] Info section. -->
                        <div class="booking-id-badge">Booking ID: #<?= $ticket['booking_id'] ?></div> <!-- [68] ID badge. -->
                        <h3><?= htmlspecialchars($ticket['from_location']) ?> → <?= htmlspecialchars($ticket['to_location']) ?></h3> 
                        <!-- html (HyperText) special (special) chars (characters) is a security tool 
                             that encodes text for safety. ( starts the tool. $ticket (data row) 
                             ['from_location'] (origin label) ) ends. -->
                        <p><strong>📅 Date:</strong> <?= $ticket['departure_date'] ?> | <strong>⏰ Time:</strong> <?= $ticket['departure_time'] ?></p> <!-- [70] Date/Time. -->
                        <p><strong>💺 Seat:</strong> <?= $ticket['seat_number'] ?> | <strong>🚌 Bus:</strong> <?= htmlspecialchars($ticket['bus_name']) ?></p> 
                        <!-- htmlspecialchars (security tool) ( $ticket ['bus_name'] (vehicle label) ) -->
                        <p><strong>👤 Traveler:</strong> <?= htmlspecialchars($ticket['passenger_name'] ?: $_SESSION['name']) ?></p> <!-- [72] Traveler Name. -->

                        <?php if (empty($ticket['passenger_id_number'])): ?> <!-- [73] if (empty). : (then). -->
                            <form action="op_update_passenger_details.php" method="POST" onsubmit="return validateInlineForm(<?= $ticket['booking_id'] ?>)" class="no-print"> <!-- [74] <form> (form). class="no-print" hides the inputs from the printer. -->
                                <input type="hidden" name="booking_id" value="<?= $ticket['booking_id'] ?>"> <!-- [75] Hidden input. -->
                                <input type="text" name="passenger_id_number" id="pid-<?= $ticket['booking_id'] ?>" placeholder="ID No." style="width:100px;"> <!-- [76] Text input. -->
                                <input type="text" name="passenger_age" id="age-<?= $ticket['booking_id'] ?>" placeholder="Age" style="width:50px;"> <!-- [77] Age input. -->
                                <button type="submit" style="background:#48bb78; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;">Save</button> <!-- [78] Submit button. -->
                            </form>                                  <!-- [79] </form> ends form. -->
                        <?php else: ?>                               <!-- [80] else (otherwise). -->
                            <p><strong>🆔 ID:</strong> <?= htmlspecialchars($ticket['passenger_id_number']) ?> | <strong>🎂 Age:</strong> <?= htmlspecialchars($ticket['passenger_age']) ?></p> <!-- [81] Static info. -->
                        <?php endif; ?>                              <!-- [82] endif (end check). -->
                    </div>                                           <!-- [83] </div> ends info. -->
                    
                    <div class="no-print" style="display:flex; flex-direction:column; gap:10px;"> <!-- [84] Button section. -->
                        <button onclick="printTicket(<?= $ticket['booking_id'] ?>)" class="action-btn" style="background:#9a4d9a; color:white;">🖨️ Print</button> <!-- [85] Print button. -->
                        <a href="user_cancel_ticket.php?booking_id=<?= $ticket['booking_id'] ?>" class="action-btn" style="background:#f59e0b; color:white;" onclick="return confirm('Cancel this ticket?')">❌ Cancel</a> <!-- [86] Cancel link. -->
                    </div>                                           <!-- [87] </div> ends buttons. -->
                </div>                                               <!-- [88] </div> ends card. -->
            <?php endwhile; ?>                                       <!-- [89] endwhile (end loop). -->
        <?php else: ?>                                               <!-- [90] else (no data). -->
            <p style="text-align:center; color:#666;">No active tickets found. <a href="book.php">Book now →</a></p> <!-- [91] Empty message. -->
        <?php endif; ?>                                              <!-- [92] endif (end check). -->
    </div>                                                           <!-- [93] </div> ends list box. -->

    <div class="no-print" style="height: 100px;"></div>              <!-- [94] Spacer. -->
    <script src="js/footer.js" class="no-print"></script>           <!-- [95] Site footer. -->
    <div class="no-print" style="text-align:center;"><a href="dashboard.php" style="color:var(--purple); font-weight:bold;">← Back to Dashboard</a></div> <!-- [96] Dashboard link. -->
</body>                                                              <!-- [97] </body> ends visible page. -->
</html>                                                              <!-- [98] </html> ends document. -->
<?php                                                                // [99] PHP cleanup.
mysqli_stmt_close($stmt);                                            // [100] mysqli_stmt_close (close stmt). ; (semicolon).
mysqli_close($conn);                                                 // [101] mysqli_close (close conn). ; (semicolon).
?>                                                                   <!-- [102] [?] ends PHP. -->
