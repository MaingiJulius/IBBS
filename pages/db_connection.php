<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
// and issues immediately and directly on the web browser screen so the
date_default_timezone_set('Africa/Nairobi');
$server_name = "localhost";
$username = "root";
$password = "";
$database_name = "IBBS_PROTOTYPE";
$port = 3306;
$conn = mysqli_connect($server_name, $username, $password, $database_name, $port);
// label chosen to represent the active bridge handle between the PHP
if (!$conn) {
    die("CRITICAL FAILURE: " . mysqli_connect_error());
}
?>