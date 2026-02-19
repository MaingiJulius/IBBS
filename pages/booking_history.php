<?php
/**
 * PASSENGER BOOKING HISTORY (booking_history.php)
 * Purpose: This script allows a logged-in Passenger to view their past and 
 * upcoming travel reservations in a simple list format.
 * 
 * NOTE: This is a legacy version (v1.0) of the history view. 
 * Newer versions use 'view_user_history.php' for a more modern experience.
 */

// --- 1. DATA BRIDGE ---
// Link to the database connection logic.
require_once 'db_connection.php';

// --- 2. IDENTITY VERIFICATION ---
// Open the session to access $_SESSION['user_id'].
session_start();

// Identify who is viewing the page.
$user_id = $_SESSION['user_id'] ?? null;

/**
 * --- SECURITY CHECK ---
 * If the user_id is missing, the person is not logged in.
 * We prompt them to log in before they can see private data.
 */
if (!$user_id) {
    echo "Access Error: Please log in to view your personal booking history.";
    exit();
}

/**
 * --- 3. DATA RETRIEVAL ---
 * We fetch records from 'user_booking_history'.
 * NOTE: 'user_booking_history' is a MySQL View that joins bookings 
 * with route and location data for user convenience.
 */
$sql_history = "SELECT * FROM user_booking_history WHERE user_id = ?";

// Use Prepared Statements to prevent SQL Injection attacks.
$stmt_hist = $conn->prepare($sql_history);
$stmt_hist->bind_param("i", $user_id); // "i" stands for integer.
$stmt_hist->execute();
$res_hist = $stmt_hist->get_result();

/**
 * --- 4. DATA RENDERING ---
 * Check if the passenger has actually traveled or booked with us before.
 */
if ($res_hist->num_rows > 0) {
    
    echo "<h2>Your Personal Booking History</h2>";
    
    // Start drawing the HTML table
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

    /**
     * LOOP: 
     * Print one table row for every booking found in the database.
     */
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
    // EMPTY STATE: If the user is new and has no bookings.
    echo "<p>Discovery Awaits: You haven't booked any trips with Wema Travellers yet.</p>";
}

// --- 5. CLEANUP ---
// Close the statement and database connection to free up system memory.
$stmt_hist->close();
$conn->close();
?>
