<?php
/**
 * LOGS TABLE SETUP SCRIPT (setup_logs_table.php)
 * Purpose: This is a one-time utility script to build the 'Logs' table in MySQL.
 * We run this once to ensure our database has a place to store activity history.
 */

// [1] Include the database connection so we can execute 'CREATE TABLE'.
require_once 'db_connection.php';

/**
 * SQL COMMAND: CREATE TABLE
 * We define the columns for our logging system:
 * - log_id: A unique number for every single log (automatic).
 * - user_id: The ID of the person who did the action.
 * - type: The category (e.g., DELETION, UPDATE).
 * - description: A text explanation of what happened.
 * - name: The human name of the user.
 * - time: The clock time when it happened.
 * - date: The calendar date.
 */
$sql = "CREATE TABLE IF NOT EXISTS Logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    name VARCHAR(255) NOT NULL,
    time TIME NOT NULL,
    date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";

// [2] Attempt to run the SQL command.
if ($conn->query($sql) === TRUE) {
    // If successful, show a confirmation message on the screen.
    echo "Success: The 'Logs' table is now ready in the database.";
} else {
    // If it failed (e.g., database is offline), show the error detail.
    echo "Database Error: Could not create the logs table. Detail: " . $conn->error;
}

// [3] Close the connection.
$conn->close();
?>

