<?php
/**
 * =================================================================
 * CORE INFRASTRUCTURE: DATABASE CONNECTIVITY (db_connection.php)
 * =================================================================
 * Purpose: This script establishes the critical data bridge between
 * the PHP application logic and the MySQL storage engine.
 * Features: Error reporting, Temporal syncing, and authentication.
 * =================================================================
 */

// [1] DEBUGGING LAYER: Initialize error reporting for transparent auditing.
error_reporting(E_ALL); // Capture and log every PHP event (Notices, Warnings, Fatal).

// [2] RUNTIME CONFIG: Direct error display to the browser interface.
ini_set('display_errors', '1'); // 1 = ON (Visible for developers); 0 = OFF (Secure for production).

// [3] TEMPORAL SYNC: Synchronize the server clock with Kenya's timezone.
date_default_timezone_set('Africa/Nairobi'); // Ensures all booking timestamps reflect East Africa Time.

/* --- [4] DATABASE AUTHENTICATION SECRET CREDENTIALS --- */

/**
 * SERVER IDENTIFIER:
 * The virtual network address where the MySQL engine is hosting the data.
 */
$server_name = "localhost"; // 'localhost' targets the same machine running the PHP server.

/**
 * DATABASE USER IDENTITY:
 * The identity profile utilized to gain entry into the database server.
 */
$username = "root"; // 'root' is the high-level superuser default for development.

/**
 * SECURITY PASSPHRASE:
 * The confidential password required for database user authentication.
 */
$password = ""; // Left empty for standard local environment connectivity.

/**
 * LOGICAL SCHEMA SELECTION:
 * The specific named relational database containing the IBBS tables.
 */
$database_name = "IBBS_PROTOTYPE"; // Targets the specific IBBS schema for all queries.

/**
 * COMMUNICATION TRAFFIC PORT:
 * The standard networking port allocated for MySQL data transmission.
 */
$port = 3306; // Global standard port for SQL-based database engines.

/* --- [5] CONNECTION INITIALIZATION --- */

/**
 * BRIDGE ACTIVATION:
 * Attempt to initialize a new 'mysqli' object to open the communication channel.
 */
$conn = new mysqli($server_name, $username, $password, $database_name, $port);

/* --- [6] FATAL FAILURE HANDLING --- */

/**
 * ERROR VALIDATION:
 * Verify if the connection handshake with the MySQL server was successful.
 */
if ($conn->connect_error) {
    /**
     * EMERGENCY TERMINATION:
     * If failed, halt the entire application ecosystem immediately.
     */
    die("CRITICAL SYSTEM FAILURE: DB Connection Terminated. " . $conn->connect_error);
}

/**
 * [7] GLOBAL OBJECT AVAILABILITY:
 * The '$conn' variable is now stabilized and live.
 * This script is 'required' at the top of every backend page to enable
 * SQL operations like SELECT, INSERT, UPDATE, and DELETE.
 */
?>


