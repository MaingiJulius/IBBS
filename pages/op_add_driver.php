<?php
require_once 'db_connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $national_id = $_POST['national_id'];
    $full_name   = $_POST['full_name'];
    $phone       = $_POST['phone'];
    $email       = $_POST['email'];
    $sql = "INSERT INTO drivers (national_id, full_name, phone, email) VALUES ('$national_id', '$full_name', '$phone', '$email')";
    if (mysqli_query($conn, $sql)) {
        echo "Driver registered successfully! System ID: " . mysqli_insert_id($conn);
    } else {
        echo "Error: Unable to register driver. " . mysqli_error($conn);
    }
}
?>