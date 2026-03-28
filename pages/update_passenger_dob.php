<?php
/**
 * =================================================================
 * ADMINISTRATION DATA REPAIR: DOB & AGE CALCULATOR (update_passenger_dob.php)
 * =================================================================
 * Purpose: This script allows staff to retroactively add missing
 * passenger birthdates and automatically calculate their legal age.
 * Backend Logic: Input Capture → DateTime Delta → Database Update.
 * =================================================================
 */

// [1] INFRASTRUCTURE: Include the shared $conn handle for database operations.
require_once 'db_connection.php';

// [2] SESSION STATE: Initialize engine to access user identity and roles.
session_start();

/* --- [3] ACCESS CONTROL: STAFF LOCK --- */

/**
 * SECURITY BARRIER:
 * We restrict this operation strictly to 'ADMIN' and 'AGENT' accounts.
 * This prevents passengers from manually overriding their age data.
 */
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    // REDIRECT: Block unauthorized traffic with a terminal security error message.
    die("Access Denied: High-Level Personnel Authentication Required for Data Modification.");
}

/* --- [4] TRANSACTIONAL LOGIC: UPDATE HANDLER --- */

// CONDITION: Ensure both the target Booking identifier and either DOB or National ID are present in POST.
if (isset($_POST['booking_id'])) {
    
    // CACHE: Map raw POST inputs to local variables for processing.
    $bid = $_POST['booking_id'];        // The unique database record index.
    $dob = $_POST['dob'] ?? '';         // The date string provided.
    $national_id = $_POST['national_id'] ?? ''; // The ID number provided.

    // Initialize segments for the dynamic query.
    $update_parts = [];
    $params = [];
    $types = "";

    // If DOB is provided, calculate age and add to update.
    if (!empty($dob)) {
        // OBJECT A: Instantiate a DateTime object for the provided birth date string.
        $birthDate = new DateTime($dob);
        // OBJECT B: Instantiate a DateTime object representing the current server time ('today').
        $today = new DateTime('today');
        // CALCULATION: Use the diff() utility to determine the chronological difference between dates.
        $age = $birthDate->diff($today)->y;
        
        $update_parts[] = "passenger_dob = ?, passenger_age = ?";
        $params[] = $dob;
        $params[] = $age;
        $types .= "si";
    }

    // If National ID is provided, add to update.
    if (!empty($national_id)) {
        $update_parts[] = "passenger_id_number = ?";
        $params[] = $national_id;
        $types .= "s";
    }

    // Shield against empty updates.
    if (empty($update_parts)) {
        header("Location: view_admin_bookings.php?msg=" . urlencode("No data provided for update."));
        exit();
    }

    /* --- [6] DATABASE PERSISTENCE LAYER --- */
    
    // COMMAND: Formulate the SQL template to update PII (Personally Identifiable Information).
    $sql = "UPDATE bookings SET " . implode(", ", $update_parts) . " WHERE booking_id = ?";
    $params[] = $bid;
    $types .= "i";
    
    // PREPARATION: Compile the query on the server and use '?' placeholders for security.
    $stmt = $conn->prepare($sql);
    
    // BINDING: Safely map inputs to the SQL statement.
    $stmt->bind_param($types, ...$params);
    
    // EXECUTION: Transmit the record update to the MySQL instance.
    if ($stmt->execute()) {
        // SUCCESS CALLBACK: Inform the administrator of the successful recalculation.
        $msg = "Success: Passenger dossier updated.";
        if (!empty($dob)) $msg .= " Calculated Age: " . $age . " years.";
    } else {
        // FAULT CALLBACK: Provide a descriptive mechanical error string.
        $msg = "System Fault: The database record could not be updated. " . $conn->error;
    }
    
    // TEARDOWN: Close the statement object to save server overhead.
    $stmt->close();
    
    /* --- [7] NAVIGATION REDIRECT HUB --- */
    
    // FINALIZE: Return back to the master booking ledger with an encoded feedback banner.
    header("Location: view_admin_bookings.php?msg=" . urlencode($msg));
    // HALT: Stop script processing to fire the redirect cleanly.
    exit();
    
} else {
    /* --- [8] PROTOCOL BLOCKER --- */
    
    // PROTECTOR: If accessed without valid POST parameters, treat as error and bounce back.
    header("Location: view_admin_bookings.php");
    // HALT.
    exit();
}
?>

