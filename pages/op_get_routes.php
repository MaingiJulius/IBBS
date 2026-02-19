<?php
// =================================================================
// OPERATION: GET ALL ROUTES
// =================================================================
// Returns routes in JSON format with KES and USD prices.
// =================================================================
require_once 'db_connection.php';

header('Content-Type: application/json');

$exchange_rate = 130;
$sql = "SELECT * FROM routes ORDER BY departure_date";
$result = $conn->query($sql);

$routes = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['cost_kes'] = $row['cost'];
        $row['cost_usd'] = number_format($row['cost'] / $exchange_rate, 2);
        $routes[] = $row;
    }
}

echo json_encode($routes);
?>
