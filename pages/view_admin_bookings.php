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
        // Validates the inline passenger details form before submission
        function validateDetails(bookingId) {
            var name = document.getElementById('pname-' + bookingId).value.trim();
            var age  = document.getElementById('page-'  + bookingId).value.trim();
            var pid  = document.getElementById('ppid-'  + bookingId).value.trim();
            if (name === '') { alert('Passenger Name is required'); return false; }
            if (age === '' || isNaN(age) || parseInt(age) <= 0) { alert('Valid Age is required'); return false; }
            if (pid === '') { alert('ID/Passport Number is required'); return false; }
            return true;
        }

        // Prints a full boarding pass including the 3 traveler details
        function printTicket(id, name, from, to, date, time, bus, seat, age, pid) {
            const printWin = window.open('', '', 'height=700,width=850');
            printWin.document.write('<html><head><title>Wema Travellers - Boarding Pass</title>');
            printWin.document.write('<style>');
            printWin.document.write('body{font-family:Arial,sans-serif;background:#f9f6ff;display:flex;justify-content:center;padding:30px;}');
            printWin.document.write('.wrapper{border:4px solid #8e44ad;border-radius:16px;max-width:620px;width:100%;background:white;overflow:hidden;}');
            printWin.document.write('.t-header{background:#8e44ad;color:white;padding:20px 30px;text-align:center;}');
            printWin.document.write('.t-header h1{margin:0;font-size:1.8rem;letter-spacing:2px;}');
            printWin.document.write('.t-header p{margin:4px 0 0;font-size:0.85rem;opacity:0.85;}');
            printWin.document.write('.t-body{padding:25px 30px;}');
            printWin.document.write('.t-body p{margin:8px 0;font-size:0.95rem;color:#2d3748;}');
            printWin.document.write('hr{border:0;border-top:2px dashed #e2d9f3;margin:15px 0;}');
            printWin.document.write('.t-footer{background:#f3f0fa;padding:12px 30px;font-size:0.78rem;color:#718096;text-align:center;border-top:1px solid #e2d9f3;}');
            printWin.document.write('</style>');
            printWin.document.write('</head><body>');
            printWin.document.write('<div class="wrapper">');
            printWin.document.write('<div class="t-header"><h1>&#x1F6A6; WEMA TRAVELLERS</h1><p>OFFICIAL BOARDING PASS</p></div>');
            printWin.document.write('<div class="t-body">');
            printWin.document.write('<p><strong>Booking Ref:</strong> #' + id + '</p>');
            printWin.document.write('<p><strong>Route:</strong> ' + from + ' &rarr; ' + to + '</p>');
            printWin.document.write('<p><strong>Departure:</strong> ' + date + ' at ' + time + '</p>');
            printWin.document.write('<p><strong>Vehicle:</strong> ' + bus + ' &nbsp;|&nbsp; <strong>Seat:</strong> ' + seat + '</p>');
            printWin.document.write('<hr>');
            printWin.document.write('<p><strong>&#x1F464; Traveler Name:</strong> ' + name + '</p>');
            printWin.document.write('<p><strong>&#x1F382; Traveler Age:</strong> ' + age + ' years</p>');
            printWin.document.write('<p><strong>&#x1F194; ID/Passport No:</strong> ' + pid + '</p>');
            printWin.document.write('</div>');
            printWin.document.write('<div class="t-footer">Please arrive 30 mins before departure &bull; Valid Government-Issued ID Required &bull; Non-Transferable</div>');
            printWin.document.write('</div>');
            printWin.document.write('<scr'+'ipt>window.onload=function(){window.print();window.close();};<\/scr'+'ipt>');
            printWin.document.write('</body></html>');
            printWin.document.close();
        }
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
                    <th>Ref ID</th><th>Date</th><th>Passenger (Account)</th><th>Route</th><th>Seat</th><th>Traveler Details</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_list = "SELECT b.booking_id, b.booking_time, b.seat_number, b.booking_status,
                                    b.passenger_name, b.passenger_age, b.passenger_id_number,
                                    u.first_name, u.last_name,
                                    r.from_location, r.to_location, r.departure_date, r.departure_time,
                                    bs.bus_name
                             FROM bookings b
                             JOIN users u  ON b.user_id  = u.user_id
                             JOIN routes r ON b.route_id = r.route_id
                             JOIN buses bs ON b.bus_id   = bs.bus_id
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
                        <strong><?= htmlspecialchars($row['passenger_name'] ?: '—') ?></strong>
                        <div style="font-size:0.8em; color:grey;">By: <?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($row['from_location'].' → '.$row['to_location']) ?></td>
                    <td><?= $row['seat_number'] ?></td>

                    <!-- TRAVELER DETAILS CELL: shows update form if incomplete, static text if complete -->
                    <td>
                        <?php
                        $has_all = !empty($row['passenger_name'])
                                && !empty($row['passenger_id_number'])
                                && !empty($row['passenger_age'])
                                && intval($row['passenger_age']) > 0;
                        ?>
                        <?php if ($has_all): ?>
                            <!-- All 3 details saved — show static text only, no button -->
                            <div style="font-size:0.85rem; line-height:1.7;">
                                <div>👤 <strong><?= htmlspecialchars($row['passenger_name']) ?></strong></div>
                                <div>🎂 Age: <?= htmlspecialchars($row['passenger_age']) ?></div>
                                <div>🆔 <?= htmlspecialchars($row['passenger_id_number']) ?></div>
                            </div>
                        <?php else: ?>
                            <!-- Missing details — show compact inline update form -->
                            <form action="op_update_passenger_details.php" method="POST"
                                  onsubmit="return validateDetails(<?= $row['booking_id'] ?>)"
                                  style="display:flex; flex-direction:column; gap:5px; min-width:170px;">
                                <input type="hidden" name="booking_id"   value="<?= $row['booking_id'] ?>">
                                <input type="hidden" name="redirect_to"  value="view_admin_bookings.php">
                                <input type="text"   name="passenger_name"       id="pname-<?= $row['booking_id'] ?>"
                                       value="<?= htmlspecialchars($row['passenger_name']) ?>"
                                       placeholder="Full Name"
                                       style="padding:4px 7px; border:1px solid #cbd5e0; border-radius:4px; font-size:0.82rem;">
                                <div style="display:flex; gap:5px;">
                                    <input type="number" name="passenger_age"    id="page-<?= $row['booking_id'] ?>"
                                           value="<?= $row['passenger_age'] > 0 ? $row['passenger_age'] : '' ?>"
                                           placeholder="Age" min="1"
                                           style="padding:4px 6px; border:1px solid #cbd5e0; border-radius:4px; font-size:0.82rem; width:60px;">
                                    <input type="text"   name="passenger_id_number" id="ppid-<?= $row['booking_id'] ?>"
                                           value="<?= htmlspecialchars($row['passenger_id_number']) ?>"
                                           placeholder="ID/Passport"
                                           style="padding:4px 7px; border:1px solid #cbd5e0; border-radius:4px; font-size:0.82rem; flex:1;">
                                </div>
                                <button type="submit"
                                        style="background:#48bb78; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer; font-weight:bold; font-size:0.82rem; align-self:flex-start;">
                                    💾 Save
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>

                    <td><span style="color:<?= ($row['booking_status']=='CANCELLED'?'red':'green') ?>; font-weight:bold;"><?= $row['booking_status'] ?></span></td>
                    <td>
                        <?php if($row['booking_status'] != 'CANCELLED'): ?>
                            <button onclick="printTicket(
                                '<?= $row['booking_id'] ?>',
                                '<?= addslashes(htmlspecialchars($row['passenger_name'])) ?>',
                                '<?= addslashes(htmlspecialchars($row['from_location'])) ?>',
                                '<?= addslashes(htmlspecialchars($row['to_location'])) ?>',
                                '<?= $row['departure_date'] ?>',
                                '<?= $row['departure_time'] ?>',
                                '<?= addslashes(htmlspecialchars($row['bus_name'])) ?>',
                                '<?= $row['seat_number'] ?>',
                                '<?= $row['passenger_age'] ?>',
                                '<?= addslashes(htmlspecialchars($row['passenger_id_number'])) ?>'
                            )" class="action-btn btn-print">PRINT</button>
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
