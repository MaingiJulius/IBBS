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

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'PASSENGER';

// --- DATABASE FETCHING (Procedural Style) ---
if ($role === 'ADMIN' || $role === 'AGENT') {
    if (isset($_GET['user_id'])) {
        $target_user_id = intval($_GET['user_id']);
        $sql = "SELECT b.*, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name 
                FROM bookings b 
                JOIN routes r ON b.route_id = r.route_id 
                JOIN buses bs ON b.bus_id = bs.bus_id 
                WHERE b.user_id = ? AND b.booking_status IN ('PAID', 'CHECKED_IN') 
                ORDER BY r.departure_date DESC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $target_user_id);
    } else {
        $sql = "SELECT b.*, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name 
                FROM bookings b 
                JOIN routes r ON b.route_id = r.route_id 
                JOIN buses bs ON b.bus_id = bs.bus_id 
                WHERE b.booking_status IN ('PAID', 'CHECKED_IN') 
                ORDER BY r.departure_date DESC";
        $stmt = mysqli_prepare($conn, $sql);
    }
} else {
    $sql = "SELECT b.*, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name 
            FROM bookings b 
            JOIN routes r ON b.route_id = r.route_id 
            JOIN buses bs ON b.bus_id = bs.bus_id 
            WHERE b.user_id = ? AND b.booking_status IN ('PAID', 'CHECKED_IN') 
            ORDER BY r.departure_date DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>                                                                   <!-- [18] [?] (closing tag) ends PHP and starts HTML. -->

<!DOCTYPE html>                                                      <!-- [19] <!DOCTYPE html> defines a standard modern web document. -->
<html lang="en">                                                     <!-- [20] <html> starts the web page structure. -->
<head>                                                               <!-- [21] <head> contains hidden metadata. -->
    <meta charset="UTF-8">                                           <!-- [22] <meta> sets text encoding. -->
    <title>My Boarding Passes - Wema Travellers</title>              <!-- [23] <title> sets browser tab text. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [24] <link> imports design rules. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [25] <link> imports branding colors. -->
    <style>                                                          /* [26] <style> starts CSS design. */
        .ticket-container { max-width: 820px; margin: 50px 30px 50px 15%; padding: 20px; } /* Narrowed box shifted right. */
        .ticket-card { background: #ffffff; border-radius: 16px; padding: 22px 26px; margin-bottom: 22px; display: flex; justify-content: space-between; align-items: flex-start; box-shadow: 0 6px 20px rgba(0,0,0,0.07); border-left: 10px solid var(--purple); gap: 20px; } /* Ticket card design. */
        .ticket-info { flex: 1; min-width: 0; } /* left column */
        .ticket-info h3 { margin: 0 0 8px 0; color: var(--purple); font-size: 1.2rem; } /* Destination names. */
        .ticket-info p { margin: 4px 0; font-size: 0.9rem; } /* tighter paragraph spacing */
        .ticket-details-col { width: 160px; flex-shrink: 0; border-left: 1px solid #e2e8f0; padding-left: 14px; font-size: 0.82rem; color: #4a5568; display: flex; flex-direction: column; gap: 5px; } /* middle column */
        .td-label { font-size: 0.82rem; color: #4a5568; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; } /* compact detail row */
        .booking-id-badge { background: var(--purple); color: white; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; margin-bottom: 6px; display: inline-block; } /* ID badge. */
        .action-btn { padding: 9px 16px; border-radius: 50px; cursor: pointer; font-size: 0.82rem; font-weight: 700; border: none; text-decoration: none; display: inline-block; text-align: center; } /* Button design. */
        @media print { .no-print { display: none !important; } .ticket-details-print { display: block !important; } }
        @media (max-width: 768px) { .ticket-container { margin: 30px 15px; } .ticket-card { flex-direction: column; } .ticket-details-col { width: 100%; border-left: none; border-top: 1px solid #e2e8f0; padding-left: 0; padding-top: 10px; } } /* responsive */
    </style>                                                         <!-- [33] </style> ends CSS. -->
    <script>
// < (less than) s (s) c (c) r (r) i (i) p (p) t (t) > (greater than) starts 
// the JavaScript logic block for browser-side interactions.

        function printTicket(bookingId) {
            // Temporarily show the hidden print-only details block so it gets captured in innerHTML
            const printBlock = document.querySelector('#ticket-' + bookingId + ' .ticket-details-print');
            if (printBlock) printBlock.style.display = 'block';

            const cardInfo = document.querySelector('#ticket-' + bookingId + ' .ticket-info');
            const content = cardInfo.innerHTML
                          + (printBlock ? printBlock.outerHTML : '');

            // Hide it again after capturing
            if (printBlock) printBlock.style.display = 'none';

            const printWin = window.open('', '', 'height=700,width=850');
            printWin.document.write('<html><head><title>Wema Travellers - Boarding Pass</title>');
            printWin.document.write('<style>');
            printWin.document.write('body{font-family:Arial,sans-serif;background:#f9f6ff;display:flex;justify-content:center;padding:30px;}');
            printWin.document.write('.wrapper{border:4px solid #8e44ad;border-radius:16px;max-width:620px;width:100%;background:white;overflow:hidden;}');
            printWin.document.write('.t-header{background:#8e44ad;color:white;padding:20px 30px;text-align:center;}');
            printWin.document.write('.t-header h1{margin:0;font-size:1.8rem;letter-spacing:2px;}');
            printWin.document.write('.t-header p{margin:4px 0 0;font-size:0.85rem;opacity:0.85;letter-spacing:1px;}');
            printWin.document.write('.t-body{padding:25px 30px;}');
            printWin.document.write('.t-body p,.t-body h3{margin:8px 0;font-size:0.95rem;color:#2d3748;}');
            printWin.document.write('.t-body h3{font-size:1.2rem;color:#8e44ad;margin-bottom:14px;}');
            printWin.document.write('.t-body .booking-id-badge{background:#8e44ad;color:white;padding:3px 10px;border-radius:20px;font-size:0.78rem;font-weight:bold;display:inline-block;margin-bottom:10px;}');
            printWin.document.write('hr{border:0;border-top:2px dashed #e2d9f3;margin:15px 0;}');
            printWin.document.write('.t-footer{background:#f3f0fa;padding:12px 30px;font-size:0.78rem;color:#718096;text-align:center;border-top:1px solid #e2d9f3;}');
            printWin.document.write('.no-print{display:none !important;} form{display:none !important;} .ticket-details-col{display:none !important;}');
            printWin.document.write('.ticket-details-print{display:block !important;}');
            printWin.document.write('</style>');
            printWin.document.write('</head><body>');
            printWin.document.write('<div class="wrapper">');
            printWin.document.write('<div class="t-header"><h1>&#x1F6A6; WEMA TRAVELLERS</h1><p>OFFICIAL BOARDING PASS</p></div>');
            printWin.document.write('<div class="t-body">');
            printWin.document.write(content);
            printWin.document.write('</div>');
            printWin.document.write('<div class="t-footer">Please arrive 30 mins before departure &bull; Valid Government-Issued ID Required &bull; Non-Transferable</div>');
            printWin.document.write('</div>');
            printWin.document.write('<scr'+'ipt>window.onload=function(){window.print();window.close();};<\/scr'+'ipt>');
            printWin.document.write('</body></html>');
            printWin.document.close();
        }

// } (closing curly bracket) ends the printTicket function block.

        function validateInlineForm(bookingId) {
            var name = document.getElementById("name-" + bookingId).value.trim();
            var pid = document.getElementById("pid-" + bookingId).value.trim();
            var age = document.getElementById("age-" + bookingId).value.trim();

            if (name == "") {
                alert("Passenger Name is required");
                return false;
            }
            if (pid == "") {
                alert("ID Number is required");
                return false;
            }
            if (age == "" || isNaN(age) || parseInt(age) <= 0) {
                alert("Valid Age is required");
                return false;
            }
            return true;
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
                <div class="ticket-card" id="ticket-<?= $ticket['booking_id'] ?>"> <!-- Ticket box -->

                    <!-- COLUMN 1: Route & logistics info -->
                    <div class="ticket-info">
                        <div class="booking-id-badge">Booking ID: #<?= $ticket['booking_id'] ?></div>
                        <h3><?= htmlspecialchars($ticket['from_location']) ?> → <?= htmlspecialchars($ticket['to_location']) ?></h3>
                        <p><strong>📅 Date:</strong> <?= $ticket['departure_date'] ?> | <strong>⏰ Time:</strong> <?= $ticket['departure_time'] ?></p>
                        <p><strong>💺 Seat:</strong> <?= $ticket['seat_number'] ?> | <strong>🚌 Bus:</strong> <?= htmlspecialchars($ticket['bus_name']) ?></p>
                    </div>

                    <!-- COLUMN 2: Traveler details (static) or compact edit form -->
                    <?php
                    $has_details = !empty($ticket['passenger_name'])
                                && !empty($ticket['passenger_id_number'])
                                && !empty($ticket['passenger_age'])
                                && intval($ticket['passenger_age']) > 0;
                    ?>
                    <div class="ticket-details-col no-print">
                        <?php if ($has_details): ?>
                            <!-- All saved: compact static display -->
                            <div class="td-label">👤 <?= htmlspecialchars($ticket['passenger_name']) ?></div>
                            <div class="td-label">🎂 <?= htmlspecialchars($ticket['passenger_age']) ?> yrs</div>
                            <div class="td-label">🆔 <?= htmlspecialchars($ticket['passenger_id_number']) ?></div>
                        <?php else: ?>
                            <!-- Missing details: compact inline form -->
                            <form action="op_update_passenger_details.php" method="POST"
                                  onsubmit="return validateInlineForm(<?= $ticket['booking_id'] ?>)"
                                  style="display:flex; flex-direction:column; gap:6px;">
                                <input type="hidden" name="booking_id"  value="<?= $ticket['booking_id'] ?>">
                                <input type="hidden" name="redirect_to" value="view_tickets.php">
                                <div class="td-label" style="color:var(--purple); font-weight:bold; margin-bottom:2px;">📋 Fill Details</div>
                                <input type="text"   name="passenger_name"       id="name-<?= $ticket['booking_id'] ?>"
                                       value="<?= htmlspecialchars($ticket['passenger_name'] ?: $_SESSION['name']) ?>"
                                       placeholder="Full Name" required
                                       style="padding:4px 7px; border:1px solid #cbd5e0; border-radius:4px; font-size:0.8rem; width:100%;">
                                <div style="display:flex; gap:5px;">
                                    <input type="number" name="passenger_age"    id="age-<?= $ticket['booking_id'] ?>"
                                           value="<?= $ticket['passenger_age'] > 0 ? $ticket['passenger_age'] : '' ?>"
                                           placeholder="Age" min="1" required
                                           style="padding:4px 5px; border:1px solid #cbd5e0; border-radius:4px; font-size:0.8rem; width:55px;">
                                    <input type="text"   name="passenger_id_number" id="pid-<?= $ticket['booking_id'] ?>"
                                           value="<?= htmlspecialchars($ticket['passenger_id_number']) ?>"
                                           placeholder="ID/Passport" required
                                           style="padding:4px 7px; border:1px solid #cbd5e0; border-radius:4px; font-size:0.8rem; flex:1;">
                                </div>
                                <button type="submit"
                                        style="background:#48bb78; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer; font-weight:bold; font-size:0.8rem; align-self:flex-start;">
                                    💾 Save
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Also show static details in the print version (inside ticket-info for print capture) -->
                    <?php if ($has_details): ?>
                    <div class="ticket-details-print" style="display:none;">
                        <p><strong>👤 Traveler Name:</strong> <?= htmlspecialchars($ticket['passenger_name']) ?></p>
                        <p><strong>🎂 Age:</strong> <?= htmlspecialchars($ticket['passenger_age']) ?> years</p>
                        <p><strong>🆔 ID/Passport:</strong> <?= htmlspecialchars($ticket['passenger_id_number']) ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- COLUMN 3: Action buttons -->
                    <div class="no-print" style="display:flex; flex-direction:column; gap:10px; align-self:center;">
                        <button onclick="printTicket(<?= $ticket['booking_id'] ?>)" class="action-btn" style="background:#9a4d9a; color:white;">🖨️ Print</button>
                        <a href="user_cancel_ticket.php?booking_id=<?= $ticket['booking_id'] ?>" class="action-btn" style="background:#f59e0b; color:white;" onclick="return confirm('Cancel this ticket?')">❌ Cancel</a>
                    </div>

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
