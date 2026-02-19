<?php
/**
 * OPERATION: ADD DRIVER (op_add_driver.php)
 * Purpose: This script is used by Administrators to register a NEW driver into the database.
 * It is a background "helper" script (API-like) that processes the data sent from a form.
 * Usage: Triggered via a POST request containing national_id, full_name, phone, and email.
 */

// --- BOOTSTRAP: DATABASE ACCESS ---
// Include the database connection so we can execute INSERT queries.
// Once required, we have access to the $conn object.
require_once 'db_connection.php';

// --- ACTION HANDLER ---
// This block runs only if the script receives data via the HTTP POST method.
// This prevents users from accidentally triggering the script by just visiting the URL.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- STEP 1: COLLECT DATA FROM THE FORM ---
    // Extract the driver details from the global $_POST array (populated by the admin form).
    
    // national_id: A unique identification number (e.g., ID Card or Passport).
    $national_id = $_POST['national_id']; 
    
    // full_name: The legal name of the driver for record-keeping and insurance.
    $full_name   = $_POST['full_name'];   
    
    // phone: Contact number for the driver (important for trip coordination).
    $phone       = $_POST['phone'];       
    
    // email: Contact email address for official communication or payslips.
    $email       = $_POST['email'];       

    // --- STEP 2: PREPARE THE SQL COMMAND ---
    // We use a "Prepared Statement" for security. 
    // Instead of putting variables directly into the SQL string (which is dangerous), 
    // we use '?' placeholders. This completely prevents SQL Injection attacks.
    $sql = "INSERT INTO drivers (national_id, full_name, phone, email) VALUES (?, ?, ?, ?)";
    
    // Ask the database connection to prepare our command for execution.
    $stmt = $conn->prepare($sql);

    // --- STEP 3: BIND PARAMETERS ---
    // Here we tell the statement which variables to swap into the '?' placeholders.
    // "ssss" is a format string: it tells PHP that we are binding 4 variables, 
    // and every single one of them (s, s, s, s) should be treated as a String.
    $stmt->bind_param("ssss", $national_id, $full_name, $phone, $email);

    // --- STEP 4: EXECUTE AND RESPOND ---
    // Attempt to run the query on the database.
    if ($stmt->execute()) {
        // SUCCESS CASE:
        // If the database successfully saves the driver, the execute() function returns TRUE.
        // We echo a success message. Note: $stmt->insert_id gets the auto-incremented primary key.
        echo "Driver registered successfully! System ID: " . $stmt->insert_id;
    } else {
        // FAILURE CASE:
        // If it fails (e.g., the National ID already exists because it's a UNIQUE field):
        // We catch the error and display it to the Administrator.
        echo "Error: Unable to register driver. " . $conn->error;
    }

    // --- STEP 5: CLEAN UP RESOURCES ---
    // Close the statement object to free up system memory on the web server.
    $stmt->close();
}

// --- SCRIPT TERMINATION ---
// End of script. Note that no HTML or <html> tags are needed 
// because this is a 'silent' background operation that likely returns text to an AJAX call or a simple redirect.
?>
