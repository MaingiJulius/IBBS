<?php                                                                // [1] Open PHP script tag to initialize server-side execution of the cancellation logic.
/**                                                                  // [2] Open multi-line documentation block for script metadata.
 * ================================================================= // [3] Visual separator for documentation block.
 * PASSENGER OPERATION: TICKET VOIDANCE (user_cancel_ticket.php)      // [4] State the script's core purpose: handling user-initiated ticket cancellations.
 * ================================================================= // [5] Visual separator for documentation block.
 * Purpose: This script allows a logged-in passenger to self-cancel   // [6] Document the primary objective: enabling self-service cancellation from the dashboard.
 * a previously purchased ticket from their personal dashboard.      // [7] Document context of the cancellation action.
 * Features: Identity matching, status mutation, and safety checks.    // [8] Document key features including security and status updates.
 * ================================================================= // [9] Visual separator for documentation block.
 */                                                                  // [10] Close the multi-line documentation block.
                                                                     // [11] Empty line for code readability.
require_once 'db_connection.php';                                    // [12] Import the database connection code to allow interaction with the MySQL server.
require_once 'logger.php';                                           // [12.5] Import the activity logging utility.
session_start();                                                     // [14] Initialize or resume the current session to access the active user's credentials.
                                                                     // [15] Empty line for code readability.
/* --- [3] AUTHENTICATION DEFENSE --- */                             // [16] Section marker: Enforcing access control and user authentication rules.
                                                                     // [17] Empty line for code readability.
if (!isset($_SESSION['user_id'])) {                                  // [18] Check if a valid session 'user_id' exists; if not, the user is unauthenticated.
    header("Location: login.html");                                  // [19] Redirect the unauthenticated user back to the login page.
    exit();                                                          // [20] Immediately halt further execution to prevent unauthorized operations.
}                                                                    // [21] Close the authentication check block.
                                                                     // [22] Empty line for code readability.
$user_id = $_SESSION['user_id'];                                     // [23] Assign the authenticated session user ID to a local variable for database queries.
                                                                     // [24] Empty line for code readability.
/* --- [4] TRANSACTIONAL LOGIC: VOID COMMAND --- */                  // [25] Section marker: Processing the requested ticket cancellation.
                                                                     // [26] Empty line for code readability.
if (isset($_GET['booking_id'])) {                                    // [27] Check if the 'booking_id' parameter was successfully passed via the URL query string.
    $booking_id = intval($_GET['booking_id']);                       // [28] Retrieve the 'booking_id' and strictly convert it to an integer to prevent SQL injection.
                                                                     // [29] Empty line for code readability.
    /* --- [5] OWNERSHIP DEFENSE ENGINE --- */                       // [30] Section marker: Verifying the user actually owns the ticket they are trying to cancel.
                                                                     // [31] Empty line for code readability.
    $sql = "UPDATE bookings SET booking_status = 'CANCELLED' WHERE booking_id = ? AND user_id = ?"; // [32] Define the SQL query to update the booking status to 'CANCELLED', ensuring it matches both the specific booking and the currently logged-in user.
    $stmt = $conn->prepare($sql);                                    // [33] Send the SQL template to the MySQL server to prepare it securely for parameter binding.
    $stmt->bind_param("ii", $booking_id, $user_id);                  // [34] Safely bind the integer variables ($booking_id, $user_id) to the prepared statement's placeholders ('?').
                                                                     // [35] Empty line for code readability.
    if ($stmt->execute()) {                                          // [36] Execute the prepared SQL statement against the database and check if execution succeeded.
        if ($stmt->affected_rows > 0) {                              // [37] Evaluate if any database rows were actually changed (meaning the ticket was found and belonged to the user).
            // [AUDIT LOG] Record the user-initiated cancellation.
            logActivity($user_id, $_SESSION['name'], 'UPDATE', "User-initiated Ticket Cancellation (BID: $booking_id)");

            $msg = "Success: Your travel ticket has been voided successfully."; // [38] Set the success notification message variable since rows were updated.
        } else {                                                     // [39] Otherwise, if no rows were updated, it means either the ticket doesn't exist or it doesn't belong to this user.
            $msg = "Security Error: Ticket not found or access denied for this record."; // [40] Set a security error message notifying that the operation failed ownership validation.
        }                                                            // [41] Close the affected rows condition block.
    } else {                                                         // [42] Otherwise, if the execution of the SQL query itself encountered an error.
        $msg = "System Fault: " . $conn->error;                      // [43] Set a system fault message appending the precise MySQL execution error string.
    }                                                                // [44] Close the execution check block.
                                                                     // [45] Empty line for code readability.
    $stmt->close();                                                  // [46] Close the prepared statement and free the memory resources associated with it.
} else {                                                             // [47] Otherwise, if no 'booking_id' parameter was found in the URL.
    $msg = "Protocol Error: No booking identifier was provided for the operation."; // [48] Set a protocol error message explaining that the necessary identification parameter is missing.
}                                                                    // [49] Close the initial booking ID condition block.
                                                                     // [50] Empty line for code readability.
/* --- [6] REDIRECTION & FEEDBACK HUB --- */                         // [51] Section marker: Sending the user back to the interface with the appropriate feedback.
                                                                     // [52] Empty line for code readability.
header("Location: view_user_history.php?msg=" . urlencode($msg));    // [53] Issue an HTTP redirect instruction targeting the user's history page, attaching the URL-encoded feedback string.
exit();                                                              // [54] Immediately halt execution to ensure the redirect instruction is processed smoothly without further output.
?>                                                                   <!-- [55] Close the PHP tag block to formally end server-side execution. -->
