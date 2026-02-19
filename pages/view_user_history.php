<?php
// =================================================================
// USER BOOKING HISTORY PAGE (view_user_history.php)
// =================================================================
// This page acts as a "Digital Passport" for the user.
// It lists all tickets they have ever purchased, sorted by most recent.
// =================================================================

// 1. Include database connection.
require_once 'db_connection.php';

// 2. Start Session.
session_start();

// --- SECURITY CHECK ---
// Ensure user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// 3. Get User ID.
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - Wema Travellers</title>
    <!-- Import Styles -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* --- INTERNAL STYLES --- */
        .view-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .back-btn-container {
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Table Design */
        .crud-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .crud-table th, .crud-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .crud-table th {
            background-color: var(--purple);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Inject Header -->
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <!-- Back Button -->
    <div class="back-btn-container">
        <!-- Return to Dashboard -->
        <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Back to Dashboard</a>
    </div>

    <!-- Main Content -->
    <div class="view-container">
        <!-- Welcome Banner -->
        <div class="welcome-banner" style="background: linear-gradient(135deg, var(--purple), var(--pink)); color: #1e1b4b; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <h2 style="margin:0;">My Travel History</h2>
            <p style="margin:5px 0 0; opacity: 0.9;">Hello, <?= htmlspecialchars($_SESSION['name']) ?>! Here is a list of your trip records.</p>
        </div>

        <!-- History Table -->
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Date Booked</th>
                    <th>Route & Departure</th>
                    <th>Passenger</th>
                    <th>Bus</th>
                    <th>Seat</th>
                    <th>Cost (KES)</th>
                    <th>Status</th>
                    <th>Action</th> <!-- New Column for Actions -->
                </tr>
            </thead>
            <tbody>
                <?php
                // FETCH BOOKINGS FOR THIS USER
                // We JOIN details from routes, buses, and users to display names instead of IDs.
                $sql = "SELECT b.*, r.from_location, r.to_location, r.cost, r.departure_date, bs.bus_name, u.first_name, u.last_name 
                        FROM bookings b 
                        JOIN routes r ON b.route_id = r.route_id 
                        JOIN buses bs ON b.bus_id = bs.bus_id
                        JOIN users u ON b.user_id = u.user_id
                        WHERE b.user_id = ? 
                        ORDER BY b.booking_time DESC";
                
                // Prepared Statement for safety.
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                // IF RECORD EXISTS...
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <!-- Date Column: Formatted nicely -->
                    <td><?= date('Y-m-d H:i', strtotime($row['booking_time'])) ?></td>
                    
                    <!-- Route Column -->
                    <td><?= htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) ?><br><small>Departs: <?= $row['departure_date'] ?></small></td>
                    
                    <!-- Passenger Column: Logic to show passenger name OR default to account name -->
                    <td><?= htmlspecialchars(!empty($row['passenger_name']) ? $row['passenger_name'] : ($row['first_name'] . ' ' . $row['last_name'])) ?></td>
                    
                    <!-- Bus Name -->
                    <td><?= htmlspecialchars($row['bus_name']) ?></td>
                    
                    <!-- Seat Number -->
                    <td><?= $row['seat_number'] ?></td>
                    
                    <!-- Cost -->
                    <td><?= number_format($row['cost'], 2) ?></td>
                    
                    <!-- Status Column: Color Coded -->
                    <td>
                        <span style="color: <?= ($row['booking_status'] == 'CANCELLED') ? 'red' : 'green' ?>; font-weight:bold;">
                            <?= $row['booking_status'] ?>
                        </span>
                    </td>

                    <!-- ACTION COLUMN: Logic for Cancellation -->
                    <td>
                        <?php 
                        // FEATURE: PASSENGER CANCELLATION
                        // Passengers can only cancel tickets that are currently 'PAID' or 'CONFIRMED'.
                        // They cannot cancel already cancelled tickets or trips that have passed (logic could be added here for dates).
                        if ($row['booking_status'] == 'PAID' || $row['booking_status'] == 'CONFIRMED'): 
                        ?>
                            <!-- 
                                The 'Cancel' button.
                                - Links to 'user_cancel_ticket.php'.
                                - Sends 'booking_id' as a GET parameter.
                                - Uses a JavaScript confirm() popup to prevent accidental clicks.
                            -->
                            <a href="user_cancel_ticket.php?booking_id=<?= $row['booking_id'] ?>" 
                               class="action-btn btn-cancel"
                               style="color: white; text-decoration: none; font-size: 0.9em; padding: 5px 10px; border-radius: 4px;"
                               onclick="return confirm('Are you sure you want to cancel this ticket? This action cannot be undone.')">
                               Cancel Trip
                            </a>
                        <?php else: ?>
                            <!-- If already cancelled, show nothing or a small text -->
                            <span style="color: grey; font-size: 0.8em;">--</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile;
                } else {
                    // IF NO RECORDS
                      echo "<tr><td colspan='8' style='text-align:center;'>You have no bookings recorded yet. <a href='home.php'>Book your first trip now!</a></td></tr>";
                }
                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Final Spacing -->
    <div style="height: 100px;"></div>
    <!-- Inject Footer -->
    <script src="js/footer.js"></script>
</body>
</html>
