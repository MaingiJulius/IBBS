<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * PASSENGER PORTAL: MY DIGITAL TICKETS (view_tickets.php)           // [4] Title identifying this script as the personal ticket wallet.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script serves as a personal dashboard for passengers // [6] Primary goal: provide travelers with a digital boarding pass vault.
 * to view, manage, and present their valid travel tickets.         // [7] Functionality: retrieval and display of individual bookings.
 * Key Features: Secure Token Display, Real-time status, and Cancellation. // [8] Features: QR-token validation and voiding logic.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume user session to identify the passenger.

if (!isset($_SESSION['user_id'])) {                                  // [13] Verify user is authenticated before exposing travel records.
    header("Location: login.html");                                  // [14] Redirect unauthorized visitors to the security gateway.
    exit();                                                          // [15] Halt further code execution to protect user data.
}                                                                    // [16] Close authentication check.

$user_id = $_SESSION['user_id'];                                     // [17] Map the active session identifier to a local variable for queries.

/**                                                                  // [18] Documentation for multi-table analytical query.
 * SQL ARCHITECTURE:                                                 // [19] Logic header for data retrieval.
 */                                                                  // [20] Close documentation block.
$sql = "SELECT b.*, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name FROM bookings b JOIN routes r ON b.route_id = r.route_id JOIN buses bs ON b.bus_id = bs.bus_id WHERE b.user_id = ? AND b.booking_status = 'PAID' ORDER BY r.departure_date DESC"; // [21] Define data join.
$stmt = $conn->prepare($sql);                                        // [22] Compile SQL template on the database server for injection safety.
$stmt->bind_param("i", $user_id);                                    // [23] Inject user ID safely into the prepared statement placeholder.
$stmt->execute();                                                    // [24] Execute query to fetch finalized travel tickets.
$result = $stmt->get_result();                                       // [25] Capture the resultant rows into a traversable data object.
?>                                                                   <!-- [26] Close PHP logic and prepare for document rendering. -->

<!DOCTYPE html>                                                         <!-- [27] Declare document as standard HTML5. -->
<html lang="en">                                                     <!-- [28] Define language for accessibility. -->
<head>                                                               <!-- [29] Metadata and resource head. -->
    <meta charset="UTF-8">                                           <!-- [30] Set character encoding for universal text support. -->
    <title>My Boarding Passes - Wema Travellers</title>                 <!-- [31] Website title in the tab. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [32] Responsive viewport settings. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [33] Import core styling tokens. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [34] Global theme and layout variables. -->
    <style>                                                          /* [35] Page-specific CSS block. */
        .ticket-container { max-width: 800px; margin: 50px auto; padding: 20px; } /* [36] Wallet wrapper. */
        .ticket-card { background: #ffffff; border-radius: 16px; padding: 30px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-left: 12px solid var(--purple); transition: transform 0.3s ease; } /* [37] Card style. */
        .ticket-card:hover { transform: translateY(-3px); }          /* [38] Hover interaction. */
        .ticket-info h3 { margin: 0 0 10px 0; color: var(--purple); font-size: 1.4rem; } /* [39] Destination title. */
        .ticket-info p { margin: 5px 0; color: #4a5568; line-height: 1.5; } /* [40] Meta-data rows. */
        .ticket-qr { background: #f7fafc; padding: 15px; border-radius: 10px; border: 1px dashed #cbd5e0; text-align: center; min-width: 140px; } /* [41] Token zone. */
        .no-tickets { text-align: center; padding: 80px 40px; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); } /* [42] Empty state. */
    </style>                                                         <!-- [43] End internal CSS. -->
</head>                                                              <!-- [44] Close head. -->

<body>                                                               <!-- [45] Begin document body. -->
<script src="js/header2.js"></script>                                <!-- [46] Inject site-wide navigation bar. -->
<div style="height: 100px;"></div>                                   <!-- [47] Fixed header offset. -->

<div class="ticket-container">                                         <!-- [48] UI wallet start. -->
    <h2 style="text-align:center; color: var(--purple); margin-bottom: 40px; font-weight: 800;">🎫 My Digital Travel Wallet</h2> <!-- [49] page title. -->
    <?php if ($result->num_rows > 0): ?>                             <!-- [50] verify if the passenger wallet holds any active tickets. -->
        <?php while($ticket = $result->fetch_assoc()): ?>            <!-- [51] begin rendering individual boarding passes. -->
            <div class="ticket-card">                                <!-- [52] pass container. -->
                <div class="ticket-info">                            <!-- [53] detail section. -->
                    <h3><?= htmlspecialchars($ticket['from_location']) ?> → <?= htmlspecialchars($ticket['to_location']) ?></h3> <!-- [54] Output the secure journey route wrapped in an h3 header tag. -->
                    <p><strong>📅 Departure:</strong> <?= $ticket['departure_date'] ?> | <strong>⏰ Time:</strong> <?= $ticket['departure_time'] ?></p> <!-- [55] Output the departure date and specified time within a stylized paragraph element. -->
                    <p><strong>💺 Seat:</strong> <span style="color:var(--purple); font-weight:bold;"><?= $ticket['seat_number'] ?></span> | <strong>🚌 Coach:</strong> <?= htmlspecialchars($ticket['bus_name']) ?></p> <!-- [56] Output the assigned seat number wrapped in a styled span, alongside the securely extracted coach name. -->
                    <p><strong>👤 Traveler:</strong> <?= htmlspecialchars($ticket['passenger_name'] ?: $_SESSION['name']) ?></p> <!-- [57] Output the passenger's actual name, defaulting to the session name if it is not specifically defined on the booking log. -->
                </div>                                               <!-- [58] Close the ticket detail and information div section. -->
                <div class="ticket-qr">                              <!-- [59] Open the dedicated div area intended for rendering the validation QR element. -->
                    <strong style="font-size: 0.8rem; color: #718096; letter-spacing: 1px;">BOARDING TOKEN</strong><br> <!-- [60] Output the static text 'BOARDING TOKEN' as a styled strong header for the hash value. -->
                    <span style="font-family: monospace; font-size: 0.9em; word-break: break-all; color: #2d3748; font-weight: 700;"> <?= $ticket['qr_token'] ?> </span> <!-- [61] Output the database-retrieved QR cryptographic hash code within a monospace word-breaking span. -->
                    <div style="margin-top: 15px;">                   <!-- [62] Open the nested action button container with a top margin. -->
                        <a href="user_cancel_ticket.php?booking_id=<?= $ticket['booking_id'] ?>" class="button" style="background-color: #f56565; color: white; padding: 10px 15px; text-decoration: none; border-radius: 8px; font-size: 0.8em; font-weight: 600; display: block;" onclick="return confirm('CANCEL: Proceed irreversibly?')"> ❌ Cancel Ticket </a> <!-- [63] Render an interactive hyperlink formatted as a red cancellation button configured to prompt user confirmation before passing the booking constraint ID context to the controller via GET. -->
                    </div>                                           <!-- [64] Close the action buttons div wrapper. -->
                </div>                                               <!-- [65] Close the div section dedicated to rendering the validation and boarding token data. -->
            </div>                                                   <!-- [66] Close the main ticket card div container that represents a single passenger booking. -->
        <?php endwhile; ?>                                           <!-- [67] Terminate the while loop that iterates through and renders the user's active ticket result set. -->
    <?php else: ?>                                                   <!-- [68] Execute this alternative block if the database returned zero active booking results for the current logged-in user. -->
        <div class="empty-state">                                    <!-- [69] Open a styled div container wrapper to display the empty state placeholder content. -->
            <p style="color: #64748b; font-size: 1.1rem;">You haven't reserved any active tickets yet.</p> <!-- [70] Output a descriptive paragraph message informing the user that no active reservations were found in their account. -->
            <a href="book.php" class="button regular-button pink-background" style="text-decoration:none; display:inline-block; margin-top:20px;">Book Your First Trip →</a> <!-- [71] Render a stylized hyperlink button pointing to the book.php engine to encourage the user to make their first reservation. -->
        </div>                                                       <!-- [72] Close the empty state placeholder div container. -->
    <?php endif; ?>                                                  <!-- [73] Close the PHP conditional if/else structure for the ticket rendering logic. -->
</div>                                                               <!-- [74] Close the primary main-content div container that encapsulates the entire passenger ticket history interface. -->

<div style="height: 100px;"></div>                                   <!-- [75] Insert a structural spacer div with a height of 100 pixels to provide visual padding at the bottom of the content area. -->
<script src="js/footer.js"></script>                                 <!-- [76] Inject the external site-wide footer JavaScript component into the HTML document. -->
<a href="dashboard.php" style="display:block; text-align:center; margin-top:40px; color:var(--purple); font-weight:bold; text-decoration:none; font-size: 1.1rem;"> ← Return to Personal Dashboard </a> <!-- [77] Render a horizontal navigation link allowing the user to return to their personal account dashboard. -->
</body>                                                              <!-- [78] Close the visible body content section of the HTML document. -->
</html>                                                              <!-- [79] Formal termination and closure of the entire HTML document structure. -->
<?php                                                                // [80] Re-open PHP for final resource cleanup and session closure logic.
$stmt->close();                                                      // [81] Explicitly release the memory allocated to the prepared statement object on the database server.
$conn->close();                                                      // [82] Terminate the active connection bridge to the MySQL database server.
?>                                                                   <!-- [83] Final termination of the PHP script execution. -->
