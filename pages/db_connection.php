<?php
/**
 * DB_CONNECTION.PHP
 * Purpose: This script acts as the "bridge" between our website (PHP code)
 * and the database server (MySQL) where all data is stored.
 * We include this file at the top of every page that needs to fetch or save data.
 */

// --- STEP 1: ENABLE ERROR REPORTING ---
// When developing, we want to see every possible error to fix bugs quickly.
// 'E_ALL' tells PHP to report all types of errors (warnings, notices, fatal errors).
error_reporting(E_ALL);

// 'ini_set' changes the configuration settings for this script execution only.
// We set 'display_errors' to '1' (True) so errors appear on the screen instead of being hidden in logs.
// WARNING: In a real 'Live' production website, this should be set to '0' for security.
ini_set('display_errors', '1');

// --- STEP 1.5: SET THE GLOBAL TIMEZONE ---
// We set the timezone to 'Africa/Nairobi' (East Africa Time).
// This ensures that all PHP functions like date() return the correct local time for Kenya.
// This is critical for generating accurate booking timestamps and feedback dates.
date_default_timezone_set('Africa/Nairobi');

// --- STEP 2: DEFINE DATABASE ACCESS CREDENTIALS ---
// We define variables to hold the specific details needed to login to the MySQL database.

// $server_name: The network address where the database lives.
// "localhost" means the database is on the same machine as the web server (Standard for XAMPP).
$server_name = "localhost";

// $username: The database user account name.
// "root" is the default administrative account created by local servers like XAMPP/WAMP.
$username = "root";

// $password: The secret key for the database user.
// By default in local development environments, the 'root' user has an empty password ("").
$password = "";

// $database_name: The specific database "folder" (schema) inside MySQL we want to open.
// This must match the name of the database you imported in phpMyAdmin.
$database_name = "IBBS_PROTOTYPE";

// $port: The virtual "door" number the database listens on.
// 3306 is the worldwide standard default port for MySQL (Relational) databases.
$port = 3306;

// --- STEP 3: INITIALIZE THE CONNECTION ---
// We create a new instance of the 'mysqli' class.
// This line actually reaches out to the MySQL server and attempts to open a communication channel.
// We pass our 5 credentials directly into the constructor.
$conn = new mysqli($server_name, $username, $password, $database_name, $port);

// --- STEP 4: VERIFY CONNECTION SUCCESS ---
// We check the 'connect_error' property of our new connection object ($conn).
// If this property contains anything (isn't NULL), it means the connection failed.
if ($conn->connect_error) {
    // If the connection failed, we stop the entire website from loading using 'die()'.
    // We print a clear message explaining why it failed (e.g., "Access Denied" or "Unknown Database").
    die("CRITICAL ERROR: Database Connection Failed. Details: " . $conn->connect_error);
}

// SUCCESS: If the code reaches this line, it means the connection was successful!
// The $conn variable is now a globally available object that we use in other files 
// to run queries like SELECT, INSERT, UPDATE, and DELETE.
?>
