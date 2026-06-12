<?php
require_once 'db_connection.php';
// ACTION: This runs when an Admin clicks 'Remove Driver' in the bus manager.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_id = $_POST['bus_id'];
    if (mysqli_query($conn, "UPDATE buses SET driver_id = NULL WHERE bus_id = $bus_id")) {
// Success!
        echo "Driver de-assigned successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
// Clean up.
}
?>