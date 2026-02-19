<?php
// =================================================================
// BUS OCCUPANCY REPORT (view_bus_occupancy.php)
// =================================================================
// This page shows how full our buses are for each upcoming trip.
// It helps staff see which buses are nearly full and which are still empty.
// =================================================================

// Include DB connection.
require_once 'db_connection.php';
// Start session.
session_start();

// --- SECURITY CHECK ---
// This data is for staff members (Admins and Agents).
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'ADMIN' && $_SESSION['role'] != 'AGENT')) {
    die("Access Denied: Staff Only Area.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus Occupancy - Wema Travellers</title>
    <!-- Import Stylesheets -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* CSS styling for the occupancy table */
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

        /* Table styling for showing bus capacity data */
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
    <!-- Standard Navbar -->
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <!-- Back Button -->
    <div class="back-btn-container">
        <!-- Return to Dashboard -->
        <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Back to Dashboard</a>
    </div>

    <!-- Content Area -->
    <div class="view-container">
        <h2>Bus Occupancy Status</h2>

        <!-- SECTION: Occupancy Table -->
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Bus Display Name</th>
                    <th>Registration No.</th>
                    <th>Travel Date</th>
                    <th>Max Capacity</th>
                    <th>Seats Booked</th>
                    <th>Remaining Seats</th>
                    <th>Fullness (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // FETCH OCCUPANCY DATA
                // This SQL query joins 'buses', 'routes', and 'bookings'.
                // It counts how many bookings exist for each route/bus combination.
                // GROUP BY ensures we get one row per Bus trip.
                $sql = "SELECT bs.bus_name, bs.reg_no, bs.max_passengers, r.departure_date, COUNT(b.booking_id) as booked_seats
                        FROM buses bs
                        JOIN routes r ON bs.bus_id = r.bus_id
                        LEFT JOIN bookings b ON r.route_id = b.route_id
                        GROUP BY bs.bus_id, r.route_id, r.departure_date
                        ORDER BY r.departure_date DESC";
                
                // Execute Query.
                $result = $conn->query($sql);
                
                // Show each bus trip and its current population
                while($row = $result->fetch_assoc()):
                    // MATH: (Booked / Total) * 100 = Percentage
                    // Note: If max_passengers is 0, this would error, but max is filtered on input.
                    $occupancy = ($row['booked_seats'] / $row['max_passengers']) * 100;
                    
                    // Logic to change the text color based on how full the bus is:
                    // RED if > 80% (Nearly full)
                    // ORANGE if > 50% (Half full)
                    // GREEN otherwise (Plenty of space)
                    $color = ($occupancy > 80) ? 'red' : (($occupancy > 50) ? 'orange' : 'green');
                ?>
                <tr>
                    <!-- Display Bus Name -->
                    <td><?= htmlspecialchars($row['bus_name']) ?></td>
                    <!-- Display Reg No -->
                    <td><?= htmlspecialchars($row['reg_no']) ?></td>
                    <!-- Display Date -->
                    <td><?= $row['departure_date'] ?></td>
                    <!-- Display Total Capacity -->
                    <td><?= $row['max_passengers'] ?> seats</td>
                    <!-- Display Booked Count -->
                    <td><?= $row['booked_seats'] ?> people</td>
                    <!-- CALCULATION: Max - Booked = Remaining -->
                    <td><?= ($row['max_passengers'] - $row['booked_seats']) ?> seats</td>
                    <!-- Display Percentage with Color -->
                    <td style="color: <?= $color ?>; font-weight:bold;"><?= number_format($occupancy, 1) ?>%</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Spacing and Footer -->
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
