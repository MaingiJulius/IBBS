<?php
// 1. Include DB Connection.
require_once 'db_connection.php';
header('Content-Type: application/json');
$route_id = $_GET['route_id'] ?? null;
if (!$route_id) {
    echo json_encode(['occupied' => []]); // Return empty list if no ID.
    exit();
}
// 4. Query Database.
try {
    $sql = "SELECT seat_number FROM bookings WHERE route_id = $route_id AND booking_status != 'CANCELLED'";
    $result = mysqli_query($conn, $sql);
// 5. Build Array of Seats.
    $occupiedCount = [];
    while ($row = $result->fetch_assoc()) {
        $occupiedCount[] = $row['seat_number'];
    }
    echo json_encode(['occupied' => $occupiedCount]);
} catch (Throwable $e) {
    echo json_encode(['occupied' => [], 'error' => $e->getMessage()]);
}
// Clean up.
?>