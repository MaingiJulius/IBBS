<?php
// =================================================================
// BOOKINGS MANAGEMENT PAGE (view_admin_bookings.php)
// =================================================================
// This is the control center for all ticket sales.
// Admins allow staff to view every booking, cancel tickets, or delete records.
// =================================================================

// 1. Include database connection.
require_once 'db_connection.php';
// 2. Start session.
session_start();

// --- SECURITY CHECK ---
// Access restrict to ADMIN or AGENT only.
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'ADMIN' && $_SESSION['role'] != 'AGENT')) {
    die("Access Denied: Staff Only Area.");
}

// --- CANCEL BOOKING LOGIC ---
// If 'cancel_booking' ID is passed in URL.
if (isset($_GET['cancel_booking'])) {
    $bid = $_GET['cancel_booking'];
    // Update status to 'CANCELLED'. We do NOT delete the row, so we keep a record of it.
    $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'CANCELLED' WHERE booking_id = ?");
    $stmt->bind_param("i", $bid);
    $stmt->execute();
    $stmt->close();
    // Redirect with message.
    header("Location: view_admin_bookings.php?msg=Booking has been cancelled.");
    exit();
}

// --- DELETE BOOKING LOGIC ---
// If 'delete_booking' ID is passed.
if (isset($_GET['delete_booking'])) {
    $bid = $_GET['delete_booking'];
    // Permanently remove the row (hard delete).
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $bid);
    $stmt->execute();
    $stmt->close();
    // Redirect with message.
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
                // FETCH DATA JOINING 3 TABLES
                // 1. bookings (main data)
                // 2. users (booker info)
                // 3. routes (trip info)
                $sql = "SELECT b.booking_id, b.booking_time, u.first_name as booker_first, u.last_name as booker_last, 
                                b.passenger_name, b.passenger_age, b.passenger_dob, b.passenger_id_number,
                                r.from_location, r.to_location, b.seat_number, b.booking_status 
                        FROM bookings b
                        JOIN users u ON b.user_id = u.user_id
                        JOIN routes r ON b.route_id = r.route_id
                        ORDER BY b.booking_time DESC";
                
                $result = $conn->query($sql);
                
                // LOOP through results
                while($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $row['booking_id'] ?></td>
                    <td><?= $row['booking_time'] ?></td>
                    <td>
                        <!-- PASSENGER INFO SECTION -->
                        <strong>Name:</strong> <?= htmlspecialchars($row['passenger_name']) ?><br>
                        
                        <?php if ($row['passenger_age'] > 0): ?>
                            <!-- Case 1: AGE EXIST. Display it. -->
                            <small>Age: <?= $row['passenger_age'] ?> 
                            <?php if($row['passenger_dob']): ?>
                                (DOB: <?= $row['passenger_dob'] ?>)
                            <?php endif; ?>
                             | ID: <?= htmlspecialchars($row['passenger_id_number']) ?></small><br>
                        <?php else: ?>
                            <!-- Case 2: AGE MISSING. Show warning and Add DOB Form. -->
                            <!-- This logic allows Admin to fix missing data post-booking. -->
                            <small style="color:red;">Age Missing!</small><br>
                            <form action="update_passenger_dob.php" method="POST" style="margin-top:5px;">
                                <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                                <input type="date" name="dob" required style="padding:2px; font-size:0.8em;">
                                <button type="submit" style="font-size:0.8em; cursor:pointer;">Set DOB</button>
                            </form>
                            <small>ID: <?= htmlspecialchars($row['passenger_id_number']) ?></small><br>
                        <?php endif; ?>
                        
                        <!-- Show who actually booked the ticket (could be an Agent) -->
                        <small style="color: #666;">(Booked by: <?= htmlspecialchars($row['booker_first'] . ' ' . $row['booker_last']) ?>)</small>
                    </td>
                    <td><?= htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) ?></td>
                    <td><?= $row['seat_number'] ?></td>
                    <td>
                        <!-- Status Badge (Green/Red) -->
                        <span style="color: <?= ($row['booking_status'] == 'CANCELLED') ? 'red' : 'green' ?>; font-weight:bold;">
                            <?= $row['booking_status'] ?>
                        </span>
                    </td>
                    <td>
                        <!-- BUTTONS -->
                        <?php if($row['booking_status'] != 'CANCELLED'): ?>
                            <!-- CANCEL Button: Only visible if active -->
                            <a href="?cancel_booking=<?= $row['booking_id'] ?>" class="action-btn btn-cancel" onclick="return confirm('Do you really want to cancel this booking?')">Cancel</a>
                        <?php endif; ?>
                        <!-- DELETE Button: Always visible -->
                        <a href="?delete_booking=<?= $row['booking_id'] ?>" class="action-btn btn-delete" onclick="return confirm('WARNING: Permanent deletion of this record. Proceed?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer Space -->
    <div style="height: 100px;"></div>
    <!-- Inject Footer -->
    <script src="js/footer.js"></script>
</body>
</html>
