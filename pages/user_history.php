<?php
// require_once: Injects the standard database connection logic.
require_once 'db_connection.php';
// session_start: Activates the session handler to read user specific login tokens.
session_start();

// Retrieve the user_id from the active session.
$user_id = $_SESSION['user_id'] ?? null;

// Security Middleware: Checks if the variable exists. If not, the user is an unauthorized guest.
if (!$user_id) {
    echo "Please log in to view your booking history.";
    exit(); // Blocks further code execution.
}

// SQL Logic: Fetch all rows from the 'user_booking_history' view for this specific passenger.
$sql = "SELECT * FROM user_booking_history WHERE user_id = ?";
$stmt = $conn->prepare($sql); // Prepares the statement for security.
$stmt->bind_param("i", $user_id); // Binds the session user_id to the query.
$stmt->execute(); // Runs the query.
$result = $stmt->get_result(); // Captures the resulting rows.

// Condition: Populate the $booking_history array if rows exist.
if ($result->num_rows > 0) {
    $booking_history = $result->fetch_all(MYSQLI_ASSOC); // Pulls all data into a multi-dimensional array.
} else {
    $booking_history = []; // Initializes as empty to avoid 'undefined variable' errors in HTML.
}

// Memory Cleanup.
$stmt->close();
$conn->close();
?>

<!DOCTYPE html> <!-- Declares the HTML5 standard. -->
<html lang="en"> <!-- Sets language to English. -->

<head>
    <meta charset="UTF-8"> <!-- Text encoding. -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Legacy IE support. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Mobile viewport scaling. -->
    <title>Your Booking History</title> <!-- Tab title. -->

    <!-- Global CSS for branding and consistent layout -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/entry-page.css">
    <style>
        /* Specific page styles for the history table layout */
        .container {
            width: 80%; /* Takes up 80% of screen width. */
            margin: 50px auto; /* Centers horizontally with 50px top margin. */
            padding: 20px; /* Internal cushion. */
        }

        .header {
            text-align: center; /* Centers the <h1>. */
            margin-bottom: 20px; /* Gap below the title. */
        }

        .table-container {
            overflow-y: scroll; /* Enables vertical scrolling if table is long. */
            max-height: 500px; /* Caps the height at 500 pixels. */
        }

        table {
            width: 100%; /* Full width table. */
            border-collapse: collapse; /* Merges borders into single lines. */
            text-align: center; /* Centers text inside cells. */
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px; /* Large internal padding for cellular readability. */
            border: 1px solid #ddd; /* Subtle grey borders. */
        }

        th {
            background-color: #f2f2f2; /* Light grey header background. */
            font-weight: bold; /* Bold headers. */
        }

        td {
            background-color: #fff; /* White background for data rows. */
        }

        .button {
            padding: 10px 20px; /* Clicking surface. */
            background-color: #4CAF50; /* Green primary button. */
            color: white; /* Contrast text. */
            border: none; /* Removes outline. */
            cursor: pointer; /* Change to hand icon. */
        }

        .button:hover {
            background-color: #45a049; /* Darker green on hover. */
        }

        .back-button {
            padding: 10px 20px; /* Button sizing. */
            background-color: #f44336; /* Red 'Back' button for awareness. */
            color: white; /* Contrast text. */
            border: none; /* No border. */
            cursor: pointer; /* Hand icon. */
            margin-top: 20px; /* Spacing. */
        }

        .back-button:hover {
            background-color: #da190b; /* Darker red on hover. */
        }

        .no-bookings {
            text-align: center; /* Centered error text. */
            margin-top: 20px;
            font-size: 1.2rem;
            color: #555; /* Muted grey for 'empty' state. */
        }
    </style>
</head>

<body>
    <!-- Include Header -->
    <script src="js/header.js"></script>

    <div class="container"> <!-- Main content wrapper. -->
        <h1 class="header">Your Booking History</h1> <!-- Page Title. -->

        <?php if (!empty($booking_history)): ?> <!-- Logic: Check if the user has records. -->
            <div class="table-container"> <!-- Scrollable wrapper. -->
                <table>
                    <thead>
                        <tr>
                            <th>Route</th> <!-- ID of the trip. -->
                            <th>From Location</th> <!-- Origin city. -->
                            <th>To Location</th> <!-- Destination city. -->
                            <th>Departure Date</th> <!-- Travel date. -->
                            <th>Time</th> <!-- Travel time. -->
                            <th>Cost</th> <!-- Amount paid. -->
                            <th>Booking Time</th> <!-- Timestamp of the transaction. -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($booking_history as $row): ?> <!-- Loop: Display each row. -->
                            <tr>
                                <td><?= htmlspecialchars($row['route_id']); ?></td> <!-- Route ID (Protected from XSS). -->
                                <td><?= htmlspecialchars($row['from_location']); ?></td> <!-- Origin. -->
                                <td><?= htmlspecialchars($row['to_location']); ?></td> <!-- Destination. -->
                                <td><?= htmlspecialchars($row['departure_date']); ?></td> <!-- Date. -->
                                <td><?= htmlspecialchars($row['time']); ?></td> <!-- Time. -->
                                <td>$<?= number_format($row['cost'], 2); ?></td> <!-- Formatted cost with 2 decimal places. -->
                                <td><?= htmlspecialchars($row['booking_time']); ?></td> <!-- Registration time. -->
                            </tr>
                        <?php endforeach; ?> <!-- End Loop. -->
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Fallback: Display if user has never booked a trip. -->
            <p class="no-bookings">You have no booking history.</p>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 40px;">
    <!-- Navigation back to the interactive booking page. -->
    <a href="book.php" class="back-button">Back to Bookings</a>
</div>

    </div>

    <!-- Include Footer -->
    <script src="js/footer.js"></script>
</body>

</html>
