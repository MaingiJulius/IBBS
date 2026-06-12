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
<!DOCTYPE html>                                                         <!-- [31] Define document as standard HTML5. -->
<html lang="en">                                                     <!-- [32] Root element identifying English as content language. -->
<head>                                                               <!-- [33] Document metadata head section. -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [36] Responsive scaling for mobile devices. -->
    <title>Your Booking History</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/entry-page.css">
    </style>
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'passenger') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="container">                                              <!-- [58] UI wrap. -->
        <h1 class="header">Your Booking History</h1>                      <!-- [59] Title. -->
        <?php if (!empty($booking_history)): ?>                        <!-- [60] check if records exist. -->
            <div class="table-container">                                <!-- [61] grid wrap. -->
                <table>
                    <thead><tr><th>Route</th><th>From Location</th><th>To Location</th><th>Departure Date</th><th>Time</th><th>Cost</th><th>Booking Time</th></tr></thead>
                    <tbody>
                        <?php foreach ($booking_history as $row): ?>
                            <tr>                                         <!-- [66] record row. -->
                                <td><?= htmlspecialchars($row['route_id']); ?></td> <!-- [67] ID cell. -->
                                <td><?= htmlspecialchars($row['from_location']); ?></td> <!-- [68] origin. -->
                                <td><?= htmlspecialchars($row['to_location']); ?></td> <!-- [69] destination. -->
                                <td><?= htmlspecialchars($row['departure_date']); ?></td> <!-- [70] date. -->
                                <td><?= htmlspecialchars($row['time']); ?></td> <!-- [71] departure time. -->
                                <td>KES <?= number_format($row['cost'], 2); ?></td> <!-- [72] monetarized cost. -->
                                <td><?= htmlspecialchars($row['booking_time']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>                                                   <!-- [79] empty handle. -->
            <p class="no-bookings">You have no booking history.</p>       <!-- [80] message. -->
        <?php endif; ?>
        <div style="text-align: center; margin-top: 40px;">              <!-- [82] btn row. -->
            <a href="book.php" class="back-button">Back to Bookings</a>     <!-- [83] navigator. -->
        </div>
    </div>
    <script src="js/footer.js"></script>
</body>
</html>