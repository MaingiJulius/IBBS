<?php
// =================================================================
// DIGITAL TICKETS PAGE (view_tickets.php)
// =================================================================
// This page displays "Active Tickets".
// It mimics a real-world app where you'd show a QR code to the driver.
// =================================================================

// 1. Include DB and Start Session.
require_once 'db_connection.php';
session_start();

// --- SECURITY CHECK ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// FETCH ACTIVE TICKETS
// We select bookings where status is 'PAID'.
$sql = "SELECT b.*, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name 
        FROM bookings b
        JOIN routes r ON b.route_id = r.route_id
        JOIN buses bs ON b.bus_id = bs.bus_id
        WHERE b.user_id = ? AND b.booking_status = 'PAID'
        ORDER BY r.departure_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
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

    <div class="ticket-container">
        <h2 style="text-align:center; color: var(--purple); margin-bottom: 30px;">Digital Travel Passes</h2>
        
        <!-- Check if we have tickets -->
        <?php if ($result->num_rows > 0): ?>
            <!-- Loop through each ticket -->
            <?php while($ticket = $result->fetch_assoc()): ?>
                <div class="ticket-card">
                    
                    <!-- Left Side: Trip Details -->
                    <div class="ticket-info">
                        <h3><?= htmlspecialchars($ticket['from_location']) ?> to <?= htmlspecialchars($ticket['to_location']) ?></h3>
                        <p><strong>Date:</strong> <?= $ticket['departure_date'] ?> at <?= $ticket['departure_time'] ?></p>
                        <p><strong>Seat:</strong> <?= $ticket['seat_number'] ?> | <strong>Bus:</strong> <?= htmlspecialchars($ticket['bus_name']) ?></p>
                        <p><strong>Passenger:</strong> <?= htmlspecialchars($ticket['passenger_name'] ?: $_SESSION['name']) ?></p>
                    </div>
                    
                    <!-- Right Side: Token and Controls -->
                    <div class="ticket-qr">
                        <!-- Display Full Token Hash -->
                        <strong>TOKEN:</strong><br>
                        <span style="font-size: 0.8em; word-break: break-all;"><?= $ticket['qr_token'] ?></span>
                        
                        <!-- Cancel Button -->
                        <div style="margin-top: 10px;">
                            <a href="user_cancel_ticket.php?booking_id=<?= $ticket['booking_id'] ?>" 
                               class="button" 
                               style="background-color: #ff4d4d; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 0.8em;"
                               onclick="return confirm('Are you sure you want to cancel this ticket? You might not be able to undo this.')">
                               Cancel Ticket
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        
        <?php else: ?>
            <!-- No Tickets State -->
            <div class="no-tickets">
                <p>You don't have any active tickets yet.</p>
                <a href="book.php" class="button regular-button green-background" style="text-decoration:none;">Book Your First Trip</a>
            </div>
        <?php endif; ?>

        <!-- Back Link -->
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
