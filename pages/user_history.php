<?php
require_once 'db_connection.php';
session_start();
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "Please log in to view your booking history.";
    exit();
}
$sql = "SELECT * FROM user_booking_history WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
if ($result->num_rows > 0) {
    $booking_history = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $booking_history = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Booking History</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/entry-page.css">
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'passenger') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="container">
        <h1 class="header">Your Booking History</h1>
        <?php if (!empty($booking_history)): ?>
            <div class="table-container">
                <table>
                    <thead><tr><th>Route</th><th>From Location</th><th>To Location</th><th>Departure Date</th><th>Time</th><th>Cost</th><th>Booking Time</th></tr></thead>
                    <tbody>
                        <?php foreach ($booking_history as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['route_id']); ?></td>
                                <td><?= htmlspecialchars($row['from_location']); ?></td>
                                <td><?= htmlspecialchars($row['to_location']); ?></td>
                                <td><?= htmlspecialchars($row['departure_date']); ?></td>
                                <td><?= htmlspecialchars($row['time']); ?></td>
                                <td>KES <?= number_format($row['cost'], 2); ?></td>
                                <td><?= htmlspecialchars($row['booking_time']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-bookings">You have no booking history.</p>
        <?php endif; ?>
        <div style="text-align: center; margin-top: 40px;">
            <a href="book.php" class="back-button">Back to Bookings</a>
        </div>
    </div>
    <script src="js/footer.js"></script>
</body>
</html>