<?php
// =================================================================
// DIGITAL TICKETS PAGE (view_tickets.php)
// =================================================================
// This page acts as the passenger's mobile wallet.
// It displays all "Active" (PAID) tickets that haven't been cancelled.
// Each ticket contains a unique Digital Token (Hash) for boarding.
// =================================================================

// 1. DATA BRIDGE: Pull in the database connection settings.
require_once 'db_connection.php';

// 2. IDENTITY: Start the session to identify which passenger is logged in.
session_start();

// --- SECURITY CHECK: AUTHENTICATION ---
// If the 'user_id' is missing from the session, the user is anonymous.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Force redirect to the login page.
    exit(); // Terminate script execution immediately.
}

// Store the logged-in user's ID in a local variable for the SQL query.
$user_id = $_SESSION['user_id'];

// --- QUERY: FETCH ACTIVE TICKETS ---
/**
 * We select all details for the user's tickets.
 * Logic:
 * - SELECT b.*: Grab everything from the 'bookings' table.
 * - JOIN 'routes' (r): To get city names, dates, and times.
 * - JOIN 'buses' (bs): To get the specific bus name (e.g. "Scania 01").
 * - WHERE: Match the user_id AND ensure status is 'PAID'.
 * - ORDER BY: Most recent travel dates first.
 */
$sql = "SELECT b.*, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name 
        FROM bookings b
        JOIN routes r ON b.route_id = r.route_id
        JOIN buses bs ON b.bus_id = bs.bus_id
        WHERE b.user_id = ? AND b.booking_status = 'PAID'
        ORDER BY r.departure_date DESC";

// Prepare the statement to prevent SQL injection.
$stmt = $conn->prepare($sql);
// Bind the user_id as an integer parameter.
$stmt->bind_param("i", $user_id);
// Run the query on the database.
$stmt->execute();
// Capture the rows returned by the query.
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Digital Tickets - Wema Travellers</title>
    <!-- Import CSS -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* --- TICKET CARD STYLES --- */
        .ticket-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        
        /* The Ticket Card visual */
        .ticket-card {
            background: white;
            border-radius: 15px;      /* Rounded edges */
            padding: 25px;
            margin-bottom: 25px;      /* Gap between tickets */
            display: flex;            /* Items side-by-side */
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); /* Shadow depth */
            border-left: 10px solid var(--purple);  /* Accent bar on left */
        }
        
        .ticket-info h3 { margin: 0; color: var(--purple); }
        
        /* The QR Code Placeholder Box */
        .ticket-qr {
            background: #eee;
            width: auto;              /* Fit content */
            padding: 10px;
            display: block;
            border-radius: 8px;
            font-size: 0.7em;
            text-align: center;
        }
        
        /* Message when no tickets found */
        .no-tickets {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <div class="ticket-container"> <!-- Wrapper for the ticket list UI. -->
        <h2 style="text-align:center; color: var(--purple); margin-bottom: 30px;">Digital Travel Passes</h2>
        
        <!-- Logic Check: Do we have any rows in the database result? -->
        <?php if ($result->num_rows > 0): ?>
            <!-- Loop through each ticket found by the SQL query. -->
            <?php while($ticket = $result->fetch_assoc()): ?>
                <div class="ticket-card"> <!-- Individual Ticket Card. -->
                    
                    <!-- LEFT SIDE: Routing and Schedule Details -->
                    <div class="ticket-info">
                        <!-- Origin to Destination Cities. -->
                        <h3><?= htmlspecialchars($ticket['from_location']) ?> to <?= htmlspecialchars($ticket['to_location']) ?></h3>
                        <!-- Travel Date and Time. -->
                        <p><strong>Date:</strong> <?= $ticket['departure_date'] ?> at <?= $ticket['departure_time'] ?></p>
                        <!-- Specific seat assigned and bus vehicle name. -->
                        <p><strong>Seat:</strong> <?= $ticket['seat_number'] ?> | <strong>Bus:</strong> <?= htmlspecialchars($ticket['bus_name']) ?></p>
                        <!-- Identifying the passenger (either the booker or a guest). -->
                        <p><strong>Passenger:</strong> <?= htmlspecialchars($ticket['passenger_name'] ?: $_SESSION['name']) ?></p>
                    </div>
                    
                    <!-- RIGHT SIDE: Security Token and Actions -->
                    <div class="ticket-qr">
                        <!-- SECURITY TOKEN: This is the digital fingerprint used to verify the ticket. -->
                        <strong>TOKEN:</strong><br>
                        <span style="font-size: 0.8em; word-break: break-all;"><?= $ticket['qr_token'] ?></span>
                        
                        <!-- CANCEL BUTTON: Allows the user to void the ticket if plans change. -->
                        <div style="margin-top: 10px;">
                            <!-- Redirects to the cancellation processing script with the specific booking_id. -->
                            <a href="user_cancel_ticket.php?booking_id=<?= $ticket['booking_id'] ?>" 
                               class="button" 
                               style="background-color: #ff4d4d; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 0.8em;"
                               onclick="return confirm('Are you sure you want to cancel this ticket? You might not be able to undo this.')">
                                Cancel Ticket
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?> <!-- End of ticket loop. -->
        
        <?php else: ?>
            <!-- EMPTY STATE: Shown if the passenger has ZERO active tickets. -->
            <div class="no-tickets">
                <p>You don't have any active tickets yet.</p>
                <!-- Call to action link to the booking engine. -->
                <a href="book.php" class="button regular-button green-background" style="text-decoration:none;">Book Your First Trip</a>
            </div>
        <?php endif; ?>

        <!-- BACK LINK: Simple navigation to return to the dashboard hub. -->
        <a href="dashboard.php" style="display:block; text-align:center; margin-top:30px; color:var(--purple); font-weight:bold; text-decoration:none;">← Back to Dashboard</a>
    </div>

    <!-- Spacer Footer -->
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
<?php 
// Clean up
$stmt->close(); 
$conn->close(); 
?>
