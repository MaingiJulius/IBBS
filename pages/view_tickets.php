<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'PASSENGER';
// Build WHERE clause based on role
if ($role === 'ADMIN' || $role === 'AGENT') {
    if (isset($_GET['user_id'])) {
        $filter_uid = intval($_GET['user_id']);
        $where_extra = "AND b.user_id = $filter_uid";
    } else {
        $where_extra = "";
    }
} else {
    $where_extra = "AND b.user_id = $user_id";
}
$sql = "SELECT b.*, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name
        FROM bookings b
        JOIN routes r ON b.route_id = r.route_id
        JOIN buses bs ON b.bus_id = bs.bus_id
        WHERE b.booking_status IN ('PAID', 'CHECKED_IN') $where_extra
        ORDER BY r.departure_date DESC";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Boarding Passes - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .ticket-container { max-width: 820px; margin: 50px 30px 50px 15%; padding: 20px;
        .ticket-card { background: #ffffff; border-radius: 16px; padding: 22px 26px; margin-bottom: 22px; display: flex; justify-content: space-between; align-items: flex-start; box-shadow: 0 6px 20px rgba(0,0,0,0.07); border-left: 10px solid var(--purple); gap: 20px;
        .ticket-info { flex: 1; min-width: 0;
        .ticket-info h3 { margin: 0 0 8px 0; color: var(--purple); font-size: 1.2rem;
        .ticket-info p { margin: 4px 0; font-size: 0.9rem;
        .ticket-details-col { width: 160px; flex-shrink: 0; border-left: 1px solid #e2e8f0; padding-left: 14px; font-size: 0.82rem; color: #4a5568; display: flex; flex-direction: column; gap: 5px;
        .td-label { font-size: 0.82rem; color: #4a5568; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        .booking-id-badge { background: var(--purple); color: white; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; margin-bottom: 6px; display: inline-block;
        .action-btn { padding: 9px 16px; border-radius: 50px; cursor: pointer; font-size: 0.82rem; font-weight: 700; border: none; text-decoration: none; display: inline-block; text-align: center;
        @media print {
            .no-print { display: none !important;
            .ticket-details-print { display: block !important;
        @media (max-width: 768px) {
            .ticket-container { margin: 30px 15px;
            .ticket-card { flex-direction: column;
            .ticket-details-col { width: 100%; border-left: none; border-top: 1px solid #e2e8f0; padding-left: 0; padding-top: 10px;
    </style>
    <script>
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
    </script>
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <div class="no-print">
        <script src="js/header2.js"></script>
    </div>
    <div style="height: 100px;"></div>
    <div class="ticket-container">
        <h2 style="text-align:center; color: var(--purple); margin-bottom: 40px; font-weight: 800;">🎫 My Digital Travel Wallet</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($ticket = mysqli_fetch_assoc($result)): ?>
                <div class="ticket-card" id="ticket-<?= $ticket['booking_id'] ?>">
                    <div class="ticket-info">
                        <div class="booking-id-badge">Booking ID: #<?= $ticket['booking_id'] ?></div>
                        <h3><?= htmlspecialchars($ticket['from_location']) ?> → <?= htmlspecialchars($ticket['to_location']) ?></h3>
                        <p><strong>📅 Date:</strong> <?= $ticket['departure_date'] ?> | <strong>⏰ Time:</strong> <?= $ticket['departure_time'] ?></p>
                        <p><strong>💺 Seat:</strong> <?= $ticket['seat_number'] ?> | <strong>🚌 Bus:</strong> <?= htmlspecialchars($ticket['bus_name']) ?></p>
                    </div>
                    <?php
                    $has_details = !empty($ticket['passenger_name'])
                                && !empty($ticket['passenger_id_number'])
                                && !empty($ticket['passenger_age'])
                                && intval($ticket['passenger_age']) > 0;
                    ?>
                    <div class="ticket-details-col no-print">
                        <?php if ($has_details): ?>
                            <div class="td-label">👤 <?= htmlspecialchars($ticket['passenger_name']) ?></div>
                            <div class="td-label">🎂 <?= htmlspecialchars($ticket['passenger_age']) ?> yrs</div>
                            <div class="td-label">🆔 <?= htmlspecialchars($ticket['passenger_id_number']) ?></div>
                        <?php else: ?>
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
                    <?php if ($has_details): ?>
                    <div class="ticket-details-print" style="display:none;">
                        <p><strong>👤 Traveler Name:</strong> <?= htmlspecialchars($ticket['passenger_name']) ?></p>
                        <p><strong>🎂 Age:</strong> <?= htmlspecialchars($ticket['passenger_age']) ?> years</p>
                        <p><strong>🆔 ID/Passport:</strong> <?= htmlspecialchars($ticket['passenger_id_number']) ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="no-print" style="display:flex; flex-direction:column; gap:10px; align-self:center;">
                        <form action="print_ticket.php" method="POST" target="_blank" style="margin:0; padding:0; display:inline;">
                            <input type="hidden" name="booking_id" value="<?= $ticket['booking_id'] ?>">
                            <button type="submit" class="action-btn" style="background:#9a4d9a; color:white; border:none; cursor:pointer; font-weight:700; width:100%;">🖨️ Print</button>
                        </form>
                        <a href="user_cancel_ticket.php?booking_id=<?= $ticket['booking_id'] ?>" class="action-btn" style="background:#f59e0b; color:white;" onclick="return confirm('Cancel this ticket?')">❌ Cancel</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; color:#666;">No active tickets found. <a href="book.php">Book now →</a></p>
        <?php endif; ?>
    </div>
    <div class="no-print" style="height: 100px;"></div>
    <script src="js/footer.js" class="no-print"></script>
    <div class="no-print" style="text-align:center;"><a href="dashboard.php" style="color:var(--purple); font-weight:bold;">← Back to Dashboard</a></div>
</body>
</html>
<?php
mysqli_close($conn);
?>