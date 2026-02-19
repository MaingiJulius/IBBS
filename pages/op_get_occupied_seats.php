<?php
// =================================================================
// HELPER SCRIPT: GET OCCUPIED SEATS (op_get_occupied_seats.php)
// =================================================================
// This script is called by JavaScript (AJAX) from 'book.php'.
// It returns a JSON list of seat numbers that are already booked for a specific route.
// This prevents users from selecting seats that are taken.
// =================================================================

// 1. Include DB Connection.
require_once 'db_connection.php';

// 2. Set Header to JSON.
header('Content-Type: application/json');

// 3. Get Route ID from URL Parameter.
$route_id = $_GET['route_id'] ?? null;

// Validate Route ID.
if (!$route_id) {
    echo json_encode(['occupied' => []]); // Return empty list if no ID.
    exit();
}

// 4. Query Database.
// specific query: Find seat_number from bookings table for this route 
// AND ensure status is NOT 'CANCELLED'.
$sql = "SELECT seat_number FROM bookings WHERE route_id = ? AND booking_status != 'CANCELLED'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();

// 5. Build Array of Seats.
$occupiedCount = [];
while ($row = $result->fetch_assoc()) {
    $occupiedCount[] = $row['seat_number']; // Add to array (e.g., ['S1', 'S4'])
}

// 6. Return JSON response.
echo json_encode(['occupied' => $occupiedCount]);

// Clean up.
$stmt->close();
$conn->close();
?>
