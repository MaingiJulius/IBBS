<?php
// This script handles the cancellation of a ticket by a passenger.
// It changes the booking status to 'CANCELLED' in the database.

// Include the database connection configuration
require_once 'db_connection.php';

// Start the session to track the logged-in user
session_start();

// SECURITY CHECK: Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.html");
    exit();
}

// Get the current user's ID
$user_id = $_SESSION['user_id'];

// Check if a booking_id was passed in the URL
if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']); // Convert to integer for safety

    // SECURITY CHECK: Ensure the booking belongs to THIS user before catching it.
    // We update the status to 'CANCELLED' only if the booking_id matches AND the user_id matches.
    $sql = "UPDATE bookings SET booking_status = 'CANCELLED' WHERE booking_id = ? AND user_id = ?";
    
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);
    // Bind the parameters (i = integer)
    $stmt->bind_param("ii", $booking_id, $user_id);
    
    // Execute the query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // If a row was updated, it means the cancellation was successful
            $msg = "Ticket cancelled successfully.";
        } else {
            // If no rows were updated, it means either the booking ID is wrong OR it doesn't belong to this user
            $msg = "Error: Ticket not found or access denied.";
        }
    } else {
        $msg = "Database Error: " . $conn->error;
    }
    
    $stmt->close();
} else {
    $msg = "No booking ID provided.";
}

// Redirect back to the tickets page with a message (you might want to handle this message display on view_tickets.php)
// For now, we'll just go back.
header("Location: view_user_history.php?msg=" . urlencode($msg));
exit();
?>
