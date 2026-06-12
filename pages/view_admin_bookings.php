<?php
require_once 'db_connection.php';
require_once 'logger.php';
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'ADMIN' && $_SESSION['role'] != 'AGENT')) {
    die("Access Denied: Staff Authorization Required.");
}
if (isset($_GET['cancel_booking'])) {
    $bid = $_GET['cancel_booking'];
    $sql_can="UPDATE bookings SET booking_status='CANCELLED' WHERE booking_id=$bid";
    mysqli_query($conn,$sql_can);
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Cancelled Ticket: $bid");
    header("Location: view_admin_bookings.php?msg=Ticket cancelled.");
    exit();
}
if (isset($_GET['delete_booking'])) {
    $bid = $_GET['delete_booking'];
$sql_del = "DELETE FROM bookings WHERE booking_id = $bid";
    mysqli_query($conn, $sql_del);
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Deleted Booking: $bid");
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
        function validateDetails(bookingId) {
            var name = document.getElementById('pname-' + bookingId).value.trim();
            var age  = document.getElementById('page-'  + bookingId).value.trim();
            var pid  = document.getElementById('ppid-'  + bookingId).value.trim();
            if (name === '') { alert('Passenger Name is required'); return false; }
            if (age === '' || isNaN(age) || parseInt(age) <= 0) { alert('Valid Age is required'); return false; }
            if (pid === '') { alert('ID/Passport Number is required'); return false; }
            return true;
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
                while($row = mysqli_fetch_assoc($res_list)):
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
                    <td>
                        <?php
                        $has_all = !empty($row['passenger_name'])
                                && !empty($row['passenger_id_number'])
                                && !empty($row['passenger_age'])
                                && intval($row['passenger_age']) > 0;
                        ?>
                        <?php if ($has_all): ?>
                            <div style="font-size:0.85rem; line-height:1.7;">
                                <div>👤 <strong><?= htmlspecialchars($row['passenger_name']) ?></strong></div>
                                <div>🎂 Age: <?= htmlspecialchars($row['passenger_age']) ?></div>
                                <div>🆔 <?= htmlspecialchars($row['passenger_id_number']) ?></div>
                            </div>
                        <?php else: ?>
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
                            <form action="print_ticket.php" method="POST" target="_blank" style="margin:0; padding:0; display:inline;">
                                <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                                <button type="submit" class="action-btn btn-print" style="border:none; cursor:pointer; font-weight:bold;">PRINT</button>
                            </form>
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