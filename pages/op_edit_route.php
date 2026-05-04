<?php
/**
 * =================================================================
 * BACKEND OPERATION: TRAVEL ARCHITECTURE UPDATE (op_edit_route.php)
 * =================================================================
 * Purpose: This script serves as the processing engine for updating
 * existing travel schedules in the system database.
 * Implementation: POST-based SQL UPDATE using Secure Prepared Statements.
 * =================================================================
 */

// [1] ARCHITECTURE: Establish the database communication handle ($conn).
session_start();
require_once 'db_connection.php';
require_once 'logger.php';

/* --- [2] TRANSACTIONAL LOGIC: UPDATE HANDLER --- */

/**
 * FORM SUBMISSION CHECK:
 * Verify that the incoming request is specifically a POST method.
 * This ensures data is not sent via the URL for security/integrity.
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // [3] DATA EXTRACTION: Map incoming form fields to local PHP variables.
    
    // The unique record key (Primary Key) for the route.
    $route_id = $_POST['route_id'];        
    
    // Geographic starting point location string.
    $from = $_POST['from_location'];      
    
    // Geographic destination point location string.
    $to = $_POST['to_location'];          
    
    // The calendar date of execution for the trip.
    $date = $_POST['departure_date'];     
    
    // The specific wall-clock time for fleet departure.
    $time = $_POST['departure_time'];     
    
    // The numerical fare/pricing adjustment for the route.
    $cost = $_POST['cost'];               

    /* --- [4] DATABASE PERSISTENCE ENGINE --- */

    /**
     * SQL PREPARATION:
     * Define the data synchronization command targeting 5 separate attributes.
     * Use '?' placeholders as a security shield against SQL command poisoning.
     */
    $stmt = $conn->prepare("UPDATE routes SET from_location=?, to_location=?, departure_date=?, departure_time=?, cost=? WHERE route_id=?");
    
    /**
     * PARAMETER BINDING:
     * 1. 'ssssdi' = Type Definition (4 Strings, 1 Double/Decimal, 1 Integer).
     * 2. Maps variables to the query placeholders in strict sequence.
     */
    $stmt->bind_param("ssssdi", $from, $to, $date, $time, $cost, $route_id);

    /**
     * EXECUTION:
     * Transmit the finalized and sanitized update command to the MySQL engine.
     */
    if ($stmt->execute()) {
        // [AUDIT LOG] Record the modification.
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Modified Travel Route (ID: $route_id)");
        /**
         * SUCCESS CALLBACK:
         * Informs the client-side UI of a successful database write operation.
         */
        echo "Success: The travel segment has been updated in the master registry.";
    } else {
        /**
         * SYSTEM FAULT CALLBACK:
         * Provide a descriptive error string if the database rejected the transaction.
         */
        echo "System Fault: " . $conn->error;
    }
    
    /* --- [5] TEARDOWN & CLEANUP --- */
    
    // RESOURCE RELEASE: Terminate the prepared statement object to free server memory.
    $stmt->close();
}

/**
 * [6] TERMINATION:
 * Stop script processing to prevent unintentional output stream interference.
 */
?>

