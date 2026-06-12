<?php
require_once 'db_connection.php';
session_start();
// Identify who is viewing the page.
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "Access Error: Please log in to view your personal booking history.";
    exit();
}
$sql_history = "SELECT * FROM user_booking_history WHERE user_id = $user_id";
$res_hist = mysqli_query($conn, $sql_history);
if ($res_hist->num_rows > 0) {
    echo "<h2>Your Personal Booking History</h2>";
    echo "<table border='1' style='width:100%; border-collapse: collapse; text-align: left;'>
            <tr style='background-color: #f2f2f2;'>
                <th style='padding: 10px;'>Trip ID</th>
                <th style='padding: 10px;'>From</th>
                <th style='padding: 10px;'>To</th>
                <th style='padding: 10px;'>Travel Date</th>
                <th style='padding: 10px;'>Departure Time</th>
                <th style='padding: 10px;'>Fare Paid</th>
                <th style='padding: 10px;'>Reserved On</th>
            </tr>";
    while ($row = $res_hist->fetch_assoc()) {
        echo "<tr>
                <td style='padding: 10px;'>#" . htmlspecialchars($row['route_id']) . "</td>
                <td style='padding: 10px;'>" . htmlspecialchars($row['from_location']) . "</td>
                <td style='padding: 10px;'>" . htmlspecialchars($row['to_location']) . "</td>
                <td style='padding: 10px;'>" . htmlspecialchars($row['departure_date']) . "</td>
                <td style='padding: 10px;'>" . htmlspecialchars($row['time']) . "</td>
                <td style='padding: 10px;'>KES " . number_format($row['cost'], 2) . "</td>
                <td style='padding: 10px; color: #666;'>" . htmlspecialchars($row['booking_time']) . "</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Discovery Awaits: You haven't booked any trips with Wema Travellers yet.</p>";
}
?>