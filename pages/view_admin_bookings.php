<?php
// =================================================================
// BOOKINGS MANAGEMENT PAGE (view_admin_bookings.php)
// =================================================================
// This is the control center for all ticket sales.
// Admins allow staff to view every booking, cancel tickets, or delete records.
// =================================================================

// 1. DATA BRIDGE: Include the database connection configuration.
require_once 'db_connection.php';

// 2. IDENTITY: Start session to identify the current logged-in staff member.
session_start();

// --- SECURITY CHECK: STAFF AUTHORIZATION ---
// We restrict access ONLY to users with the 'ADMIN' or 'AGENT' role.
// If the visitor is a regular 'PASSENGER', they are denied entry.
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'ADMIN' && $_SESSION['role'] != 'AGENT')) {
    // Terminate script execution and display an Access Denied message.
    die("Access Denied: Staff Only Area.");
}

// --- LOGIC: CANCEL BOOKING ---
// Triggered if the 'cancel_booking' ID is detected in the URL parameters ($_GET).
if (isset($_GET['cancel_booking'])) {
    $bid = $_GET['cancel_booking']; // The unique ID of the booking to be cancelled.
    
    // SQL: Update the status to 'CANCELLED'. 
    // IMPORTANT: We do NOT delete the row here; we just change its state to "Cancelled".
    // This allows the bus company to keep a history of "No-shows" or "Refunds".
    $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'CANCELLED' WHERE booking_id = ?");
    $stmt->bind_param("i", $bid); // Bind the ID as an integer ('i').
    $stmt->execute(); // Execute the command.
    $stmt->close(); // Close the prepared statement tool.
    
    // User Feedback: Redirect back with a success message in the URL.
    header("Location: view_admin_bookings.php?msg=Booking has been cancelled.");
    exit(); // Stop any further PHP processing.
}

// --- LOGIC: DELETE BOOKING (Staff/Admin Only) ---
// Triggered if 'delete_booking' is passed via the URL query string.
if (isset($_GET['delete_booking'])) {
    $bid = $_GET['delete_booking']; // Capture the ID to delete.
    
    // SQL: Permanently remove the record from the database (Hard Delete).
    // CAUTION: This action is irreversible once committed.
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $bid); // Bind ID.
    $stmt->execute(); // Execute.
    $stmt->close(); // Close tool.
    
    // User Feedback: Redirect back with a deletion confirmation.
    header("Location: view_admin_bookings.php?msg=Booking record has been deleted.");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings - Wema Travellers</title>
    <!-- CSS Links -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* --- INTERNAL STYLES --- */
        .view-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .back-btn-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Table Styles */
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

        /* Action Buttons */
        .action-btn {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 0.9em;
            margin-right: 5px;
        }
        .btn-delete { background-color: #ff4d4d; } /* Red */
        .btn-cancel { background-color: #ffa500; } /* Orange */
    </style>
</head>
<body>
    <!-- Header -->
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <!-- Back Button -->
    <div class="back-btn-container">
        <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Back to Dashboard</a>
    </div>

    <!-- Content -->
    <div class="view-container">
        <h2>Manage Passenger Bookings</h2>

        <!-- Messages -->
        <?php if(isset($_GET['msg'])): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($_GET['msg']) ?></p>
        <?php endif; ?>

        <!-- BOOKINGS TABLE -->
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Date/Time</th>
                    <th>Passenger Details</th>
                    <th>Travel Route</th>
                    <th>Seat No.</th>
                    <th>Status</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // --- STEP 1: DEFINE DATA QUERY ---
                // We perform a 3-Way JOIN to pull a complete "Passport" for the ticket:
                // 1. FROM 'bookings' (b): Contains the seat and status.
                // 2. JOIN 'users' (u): Identifies WHO performed the booking action (could be different from the passenger).
                // 3. JOIN 'routes' (r): Identifies WHERE the bus is going.
                $sql = "SELECT b.booking_id, b.booking_time, u.first_name as booker_first, u.last_name as booker_last, 
                                b.passenger_name, b.passenger_age, b.passenger_dob, b.passenger_id_number,
                                r.from_location, r.to_location, b.seat_number, b.booking_status 
                        FROM bookings b
                        JOIN users u ON b.user_id = u.user_id
                        JOIN routes r ON b.route_id = r.route_id
                        ORDER BY b.booking_time DESC"; // Show newest sales first.
                
                // --- STEP 2: EXECUTE AND LOOP ---
                $result = $conn->query($sql); // Execute the SQL.
                
                // We use a 'while' loop to draw one table row per booking found in the database.
                while($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <!-- UNIQUE ID: The primary key of the booking. -->
                    <td><?= $row['booking_id'] ?></td>
                    
                    <!-- TIMESTAMP: When the ticket was paid for. -->
                    <td><?= $row['booking_time'] ?></td>
                    
                    <!-- PASSENGER DOSSIER -->
                    <td>
                        <!-- Displays the name of the person actually sitting in the seat. -->
                        <strong>Name:</strong> <?= htmlspecialchars($row['passenger_name']) ?><br>
                        
                        <!-- DATA VALIDATION UI: -->
                        <?php if ($row['passenger_age'] > 0): ?>
                            <!-- CASE A: The record is complete. Display Age/ID. -->
                            <small>Age: <?= $row['passenger_age'] ?> 
                            <?php if($row['passenger_dob']): ?>
                                (DOB: <?= $row['passenger_dob'] ?>)
                            <?php endif; ?>
                             | ID: <?= htmlspecialchars($row['passenger_id_number']) ?></small><br>
                        <?php else: ?>
                            <!-- CASE B: DATA DEFICIENCY WARNING -->
                            <!-- This logic targets older records or office errors where age wasn't captured. -->
                            <small style="color:red; font-weight:800;">[!] AGE DATA MISSING</small><br>
                            <!-- REPAIR ACTION: Provide a quick form for the Admin to fix the record manually. -->
                            <form action="update_passenger_dob.php" method="POST" style="margin-top:5px; background: #fff5f5; padding: 5px; border-radius: 4px;">
                                <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                                <input type="date" name="dob" required style="padding:2px; font-size:0.8em;"> <!-- Date of Birth Picker. -->
                                <button type="submit" style="font-size:0.8em; cursor:pointer;">Update DOB</button> <!-- Fixes the database record. -->
                            </form>
                            <small>ID: <?= htmlspecialchars($row['passenger_id_number']) ?></small><br>
                        <?php endif; ?>
                        
                        <!-- AUDIT TRAIL: Show who (Agent/Self) created this ticket. -->
                        <small style="color: #666; font-style: italic;">(Created by: <?= htmlspecialchars($row['booker_first'] . ' ' . $row['booker_last']) ?>)</small>
                    </td>

                    <!-- DESTINATION INFO -->
                    <td><?= htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) ?></td>
                    
                    <!-- SEAT ALLOCATION -->
                    <td><?= $row['seat_number'] ?></td>
                    
                    <!-- BUSINESS STATUS -->
                    <td>
                        <!-- Status Badge: Red for Cancelled, Green for Paid/Active. -->
                        <span style="color: <?= ($row['booking_status'] == 'CANCELLED') ? 'red' : 'green' ?>; font-weight:bold;">
                            <?= $row['booking_status'] ?>
                        </span>
                    </td>

                    <!-- OFFICER COMMANDS -->
                    <td>
                        <!-- If the ticket is active, show the 'Cancel' command button. -->
                        <?php if($row['booking_status'] != 'CANCELLED'): ?>
                            <a href="?cancel_booking=<?= $row['booking_id'] ?>" class="action-btn btn-cancel" onclick="return confirm('Do you really want to cancel this booking?')">Cancel</a>
                        <?php endif; ?>
                        <!-- Always show 'Delete' but warn the staff about permanent loss of data. -->
                        <a href="?delete_booking=<?= $row['booking_id'] ?>" class="action-btn btn-delete" onclick="return confirm('WARNING: Permanent deletion of this record. Proceed?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?> <!-- End the loop. -->
            </tbody>
        </table>
    </div>

    <!-- Footer Space -->
    <div style="height: 100px;"></div>
    <!-- Inject Footer -->
    <script src="js/footer.js"></script>
</body>
</html>
