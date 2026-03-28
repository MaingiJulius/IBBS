<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * OPERATION: ADD DRIVER (op_add_driver.php)                         // [3] Title identifying this script as the driver registration worker.
 * Purpose: This script is used by Administrators to register a NEW driver into the database. // [4] Main objective: persist a new driver into the personnel registry.
 * It is a background "helper" script (API-like) that processes the data sent from a form. // [5] Task: SQL insertion of driver inventory data.
 * Usage: Triggered via a POST request containing national_id, full_name, phone, and email. // [6] Data Requirements: key driver attributes.
 */                                                                  // [7] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [8] Import database bridge object ($conn) for MySQL communication.

if ($_SERVER["REQUEST_METHOD"] == "POST") {                          // [9] Logic Barrier: Ensure script only processes POST submissions.
    $national_id = $_POST['national_id'];                            // [10] Capture: Unique national identifier (ID/Passport).
    $full_name   = $_POST['full_name'];                              // [11] Capture: Legal name of the employee.
    $phone       = $_POST['phone'];                                  // [12] Capture: Primary mobile contact number.
    $email       = $_POST['email'];                                  // [13] Capture: Official digital contact address.

    $sql = "INSERT INTO drivers (national_id, full_name, phone, email) VALUES (?, ?, ?, ?)"; // [14] Prepare secure SQL command for record creation.
    $stmt = $conn->prepare($sql);                                    // [15] Initialize the preparation engine for injection safety.
    $stmt->bind_param("ssss", $national_id, $full_name, $phone, $email); // [16] Safely bind 4 string parameters to the insertion template.

    if ($stmt->execute()) {                                          // [17] Attempt to commit the new driver to the database ledger.
        echo "Driver registered successfully! System ID: " . $stmt->insert_id; // [18] Case: Success. Output the new unique record identifier.
    } else {                                                         // [19] Case: Failure.
        echo "Error: Unable to register driver. " . $conn->error;    // [20] Output the raw technical error for administrative debugging.
    }                                                                // [21] Close execution check.

    $stmt->close();                                                  // [22] Release the prepared statement memory resources.
}                                                                    // [23] Close the POST operational block.
?>                                                                   <!-- [24] Close the PHP processing script tag. -->
