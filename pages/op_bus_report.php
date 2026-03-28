<?php
/**
 * =================================================================
 * BACKEND OPERATION: FLEET DATA DISTRIBUTION (op_bus_report.php)
 * =================================================================
 * Purpose: This script acts as a lightweight API endpoint that
 * returns the current status of the bus fleet and assigned drivers.
 * Format: application/json (For AJAX consumption).
 * =================================================================
 */

// [1] ARCHITECTURE: establish the database communication handle.
require_once 'db_connection.php';

// [2] PROTOCOL HEADER: Instruct the browser to treat this output as a JSON data object.
header('Content-Type: application/json');

/* --- [3] DATA AGGREGATION LOGIC --- */

/**
 * SQL SELECTION:
 * - b.*: Pull all data from the 'buses' table (ID, Name, Capacity).
 * - d.full_name: Join with the 'drivers' table to get the human-readable driver name.
 * - LEFT JOIN: Ensure buses appear even if they don't have a driver assigned yet.
 */
$sql = "SELECT b.*, d.full_name as driver_name 
        FROM buses b 
        LEFT JOIN drivers d ON b.driver_id = d.driver_id";

// EXECUTION: Transmit the query to the MySQL server.
$result = $conn->query($sql);

// [4] STORAGE: Initialize an empty container array for the bus objects.
$buses = [];

// Validation: Check if the fleet table contains any records.
if ($result->num_rows > 0) {
    // ITERATOR: Loop through each row of the result set.
    while($row = $result->fetch_assoc()) {
        // PUSH: Inject the current row (associative array) into the main list.
        $buses[] = $row;
    }
}

/* --- [5] DATA TRANSMISSION --- */

/**
 * RESPONSE: Convert the PHP array into a standardized JSON string.
 * This allows JavaScript 'fetch' calls to easily parse the fleet data.
 */
echo json_encode($buses);

// TERMINATE: Stop script execution.
?>

