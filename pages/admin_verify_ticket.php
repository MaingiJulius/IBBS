<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'ADMIN' && $_SESSION['role'] !== 'AGENT')) {
    header("Location: login.html");
    exit();
}
$search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';
$tickets = [];
$error = '';
$success_msg = '';
if (isset($_POST['confirm_boarding'])) {
    $bid = $_POST['booking_id'];
    $sql_upd = "UPDATE bookings SET booking_status = 'CHECKED_IN' WHERE booking_id = $bid";
    if (mysqli_query($conn,$sql_upd)) {
        $success_msg = "Boarding Authorized.";
    }
}
if ($search_id) {
    $sql_s = "SELECT b.*, r.from_location, r.to_location, r.departure_date, bs.bus_name, u.first_name, u.last_name
            FROM bookings b
            JOIN routes r ON b.route_id = r.route_id
            JOIN buses bs ON b.bus_id = bs.bus_id
            JOIN users u ON b.user_id = u.user_id
            WHERE b.passenger_id_number = '$search_id'";
    $res_s = mysqli_query($conn, $sql_s);
    $tickets = [];
    while ($row = mysqli_fetch_assoc($res_s)) {
        $tickets[] = $row;
    }
    if (empty($tickets)) { $error = "No booking found."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verification - IBBS</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .verify-container { max-width: 600px; margin: 40px auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
        .search-box { display: flex; gap: 10px; margin-bottom: 30px; }
        .search-box input { flex: 1; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; }
        .result-card { border: 2px solid #e2e8f0; border-radius: 16px; padding: 25px; margin-top: 20px; }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="verify-container">
        <h2 style="color: var(--purple); text-align: center;">Ticket Verification</h2>
        <?php if ($error): ?>
            <div style="color:red; text-align:center; margin-bottom:15px;"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success_msg): ?>
            <div style="color:green; text-align:center; margin-bottom:15px; font-weight:bold;"><?= $success_msg ?></div>
        <?php endif; ?>
        <form method="GET" class="search-box">
            <input type="text" name="search_id" placeholder="Enter Passenger ID Number" value="<?= htmlspecialchars($search_id) ?>" style="font-size:1.1rem;">
            <button type="submit" class="button regular-button pink-background" style="width:auto; margin:0;">Search</button>
        </form>
        <?php if (!empty($tickets)): ?>
            <?php foreach ($tickets as $ticket): ?>
            <div class="result-card">
                <h3 style="margin-top:0; color:var(--purple);"><?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?></h3>
                <p><strong>📍 Route:</strong> <?= htmlspecialchars($ticket['from_location']) ?> → <?= htmlspecialchars($ticket['to_location']) ?></p>
                <p><strong>📅 Date:</strong> <?= $ticket['departure_date'] ?></p>
                <p><strong>💺 Bus:</strong> <?= htmlspecialchars($ticket['bus_name']) ?> | <strong>Seat:</strong> <?= $ticket['seat_number'] ?></p>
                <div style="margin-top:20px; padding:15px; background:#f8fafc; border-radius:8px; border-left:4px solid var(--purple);">
                    <p style="margin:0;"><strong>Current Status:</strong> <span style="color:<?= $ticket['booking_status'] === 'CHECKED_IN' ? '#22c55e' : '#f59e0b' ?>; font-weight:800;"><?= $ticket['booking_status'] ?></span></p>
                </div>
                <?php if ($ticket['booking_status'] === 'PAID'): ?>
                    <form method="POST" style="margin-top:25px;">
                        <input type="hidden" name="booking_id" value="<?= $ticket['booking_id'] ?>">
                        <button type="submit" name="confirm_boarding" class="button regular-button" style="background:#22c55e; color:white; border:none; box-shadow:0 4px 10px rgba(34,197,94,0.3);">✅ Authorize Boarding</button>
                    </form>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
<?php
mysqli_close($conn);
?>