<?php
require_once 'db_connection.php';
header('Content-Type: application/json');
$sql = "SELECT b.*, d.full_name as driver_name
        FROM buses b
        LEFT JOIN drivers d ON b.driver_id = d.driver_id";
$result = $conn->query($sql);
$buses = [];
// Validation: Check if the fleet table contains any records.
if ($result->num_rows > 0) {
// ITERATOR: Loop through each row of the result set.
    while($row = $result->fetch_assoc()) {
        $buses[] = $row;
    }
}
// --- [5] DATA TRANSMISSION ---
echo json_encode($buses);
?>