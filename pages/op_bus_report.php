<?php
// =================================================================
// OPERATION: GET BUS REPORT
// =================================================================
// Returns list of buses and their drivers in JSON.
// =================================================================
require_once 'db_connection.php';

header('Content-Type: application/json');

$sql = "SELECT b.*, d.full_name as driver_name 
        FROM buses b 
        LEFT JOIN drivers d ON b.driver_id = d.driver_id";
$result = $conn->query($sql);

$buses = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $buses[] = $row;
    }
}

echo json_encode($buses);
?>
