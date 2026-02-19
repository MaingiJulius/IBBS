<?php
require_once 'db_connection.php';
session_start();

// Get the user_id from the session
$user_id = $_SESSION['user_id'] ?? null;

// Check if the user is logged in
if (!$user_id) {
    echo "Please log in to view your booking history.";
    exit();
}

// Fetch the user's booking history from the database using the view
$sql = "SELECT * FROM user_booking_history WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any bookings for the user
if ($result->num_rows > 0) {
    $booking_history = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $booking_history = [];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Booking History</title>

    <!-- Include external stylesheets -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/entry-page.css">
    <style>
        .container {
            width: 80%;
            margin: 50px auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .table-container {
            overflow-y: scroll;
            max-height: 500px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        td {
            background-color: #fff;
        }

        .button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .button:hover {
            background-color: #45a049;
        }

        .back-button {
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        .back-button:hover {
            background-color: #da190b;
        }

        .no-bookings {
            text-align: center;
            margin-top: 20px;
            font-size: 1.2rem;
            color: #555;
        }
    </style>
</head>

<body>
    <!-- Include Header -->
    <script src="js/header.js"></script>

    <div class="container">
        <h1 class="header">Your Booking History</h1>

        <?php if (!empty($booking_history)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Route</th>
                            <th>From Location</th>
                            <th>To Location</th>
                            <th>Departure Date</th>
                            <th>Time</th>
                            <th>Cost</th>
                            <th>Booking Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($booking_history as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['route_id']); ?></td>
                                <td><?= htmlspecialchars($row['from_location']); ?></td>
                                <td><?= htmlspecialchars($row['to_location']); ?></td>
                                <td><?= htmlspecialchars($row['departure_date']); ?></td>
                                <td><?= htmlspecialchars($row['time']); ?></td>
                                <td>$<?= number_format($row['cost'], 2); ?></td>
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

    <!-- Include Footer -->
    <script src="js/footer.js"></script>
</body>

</html>
