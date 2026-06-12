<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'passenger') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="view-container">
        <h2 style="color:var(--purple); text-align:center;">My Travel History</h2>
        <p style="text-align:center;">Hello, <?= htmlspecialchars($_SESSION['name']) ?>! Here is a list of your trip records.</p>
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Date Booked</th>
                    <th>Route & Departure</th>
                    <th>Passenger</th>
                    <th>Bus</th>
                    <th>Seat</th>
                    <th>Cost (KES)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $user_id_val = intval($user_id);
                $sql = "SELECT b.*, r.from_location, r.to_location, r.cost, r.departure_date, bs.bus_name, u.first_name, u.last_name
                        FROM bookings b
                        JOIN routes r ON b.route_id = r.route_id
                        JOIN buses bs ON b.bus_id = bs.bus_id
                        JOIN users u ON b.user_id = u.user_id
                        WHERE b.user_id = $user_id_val
                        ORDER BY b.booking_time DESC";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)):
                ?>
                <tr>
                    <td><?= $row['booking_time'] ?></td>
                    <td><?= htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) ?><br><small>Dep: <?= $row['departure_date'] ?></small></td>
                    <td><?= htmlspecialchars($row['passenger_name'] ?: ($row['first_name'] . ' ' . $row['last_name'])) ?></td>
                    <td><?= htmlspecialchars($row['bus_name']) ?></td>
                    <td><?= $row['seat_number'] ?></td>
                    <td><?= number_format($row['cost'], 2) ?></td>
                    <td><span style="color:<?= ($row['booking_status']=='CANCELLED'?'red':'green') ?>; font-weight:bold;"><?= $row['booking_status'] ?></span></td>
                    <td>
                        <?php if ($row['booking_status'] == 'PAID' || $row['booking_status'] == 'CONFIRMED'): ?>
                            <a href="user_cancel_ticket.php?booking_id=<?= $row['booking_id'] ?>"
                               style="color:red;" onclick="return confirm('Cancel this trip?')">Cancel</a>
                        <?php else: ?>
                            --
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile;
                } else {
                      echo "<tr><td colspan='8' style='text-align:center;'>No bookings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>