<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * OPERATION: ADD ROUTE                                              // [4] Title identifying this script as the route creation worker.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script handles the backend logic for initializing a // [6] Main objective: persist a new travel path into the routes registry.
 * new travel route in the database.                                 // [7] Task: SQL insertion of logistical route data.
 * Usage: Triggered via a POST request with path, timing, and cost details. // [8] Data Requirements: core segment attributes.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.

if ($_SERVER["REQUEST_METHOD"] == "POST") {                          // [12] Logic Barrier: Ensure script only processes POST submissions.
    $from = $_POST['from_location'];                                 // [13] Capture: Geographical starting point of the journey.
    $to = $_POST['to_location'];                                     // [14] Capture: Geographical destination endpoint.
    $date = $_POST['departure_date'];                                // [15] Capture: Scheduled calendar date for departure.
    $time = $_POST['departure_time'];                                // [16] Capture: Scheduled clock time for departure.
    $cost = $_POST['cost'];                                          // [17] Capture: Ticket pricing for the segment in KES.
    $bus_id = $_POST['bus_id'];                                      // [18] Capture: Unique vehicle ID assigned to this segment.

    $stmt = $conn->prepare("INSERT INTO routes (from_location, to_location, departure_date, departure_time, cost, bus_id) VALUES (?, ?, ?, ?, ?, ?)"); // [19] Prepare secure SQL command for record creation.
    $stmt->bind_param("ssssdi", $from, $to, $date, $time, $cost, $bus_id); // [20] Safely bind 6 parameters (4 strings, 1 decimal, 1 integer) to the template.

    if ($stmt->execute()) {                                          // [21] Attempt to commit the new route to the database ledger.
        echo "Route added successfully. ID: " . $stmt->insert_id;    // [22] Case: Success. Output the new unique record identifier.
    } else {                                                         // [23] Case: Failure.
        echo "Error: " . $conn->error;                                // [24] Output the raw technical error for administrative debugging.
    }                                                                // [25] Close execution check.

    $stmt->close();                                                  // [26] Release the prepared statement memory resources.
}                                                                    // [27] Close the POST operational block.
?>                                                                   <!-- [28] Close the PHP processing script tag. -->
