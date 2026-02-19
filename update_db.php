<?php
require_once 'pages/db_connection.php';

$sql = "ALTER TABLE bookings ADD COLUMN passenger_dob DATE DEFAULT NULL";
if ($conn->query($sql) === TRUE) {
    echo "Column passenger_dob added successfully";
} else {
    echo "Error adding column: " . $conn->error;
}
$conn->close();
?>
