<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * OPERATION: ADD BUS                                                // [4] Title identifying this script as the vehicle registration worker.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script handles the backend logic for adding a new   // [6] Main objective: persist a new vehicle into the fleet registry.
 * bus record to the database.                                       // [7] Task: SQL insertion of fleet inventory data.
 * Usage: POST with reg_no, bus_name, capacity, seat_layout, driver_id // [8] Data Requirements: key vehicle attributes.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.

if ($_SERVER["REQUEST_METHOD"] == "POST") {                          // [12] Logic Barrier: Ensure script only processes POST submissions.
    $reg_no = $_POST['reg_no'];                                      // [13] Capture: Vehicle registration plate number.
    $bus_name = $_POST['bus_name'];                                  // [14] Capture: Commercial name/model of the bus.
    $capacity = $_POST['capacity'];                                   // [15] Capture: Maximum authorized passenger capacity.
    $seat_layout = $_POST['seat_layout'];                            // [16] Capture: Visual seat configuration identifier.
    $driver_id = !empty($_POST['driver_id']) ? $_POST['driver_id'] : NULL; // [17] Capture: Optional foreign key link to an assigned driver.

    $stmt = $conn->prepare("INSERT INTO buses (reg_no, bus_name, max_passengers, seat_layout, driver_id) VALUES (?, ?, ?, ?, ?)"); // [18] Prepare secure SQL command for record creation.
    $stmt->bind_param("ssisi", $reg_no, $bus_name, $capacity, $seat_layout, $driver_id); // [19] Safely bind 5 parameters to the insertion template.

    if ($stmt->execute()) {                                          // [20] Attempt to commit the new vehicle to the database ledger.
        echo "Bus added successfully. ID: " . $stmt->insert_id;        // [21] Case: Success. Output the new unique record identifier.
    } else {                                                         // [22] Case: Failure.
        echo "Error: " . $conn->error;                                // [23] Output the raw technical error for administrative debugging.
    }                                                                // [24] Close execution check.

    $stmt->close();                                                  // [25] Release the prepared statement memory resources.
}                                                                    // [26] Close the POST operational block.
?>                                                                   <!-- [27] Close the PHP processing script tag. -->
