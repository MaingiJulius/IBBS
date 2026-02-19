<?php
// =================================================================
// REVENUE REPORT PAGE (view_revenue_report.php)
// =================================================================
// This is the Financial Report page for the Administrator.
// It calculates how much money Wema Travellers is making from each bus route.
// =================================================================

// Include DB connection.
require_once 'db_connection.php';
// Start session.
session_start();

// --- SECURITY CHECK ---
// Money reports are highly sensitive! Only the 'ADMIN' can see this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ADMIN') {
    die("Access Denied: Highly Sensitive Financial Data. Admins Only.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Revenue Report - Wema Travellers</title>
    <!-- Import Styles -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* CSS styling for the financial table */
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

        /* Styling for the revenue data table */
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

    <div class="back-btn-container">
        <!-- Back link -->
        <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Back to Dashboard</a>
    </div>

    <div class="view-container">
        <h2>Revenue Generation Report</h2>

        <!-- SECTION: Financial Breakdown Table -->
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Route ID</th>
                    <th>Full Route Path</th>
                    <th>Travel Date</th>
                    <th>No. of Tickets Sold</th>
                    <th>Total Earned (KES)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // FETCH REVENUE DATA
                // This SQL query is "smart". It:
                // 1. Joins bookings and routes.
                // 2. Only counts 'PAID' bookings.
                // 3. Groups them by Route ID so we see one row per trip.
                // 4. Sums the cost of all tickets for that route.
                $sql = "SELECT r.route_id, r.from_location, r.to_location, r.departure_date, COUNT(b.booking_id) as total_bookings, SUM(r.cost) as revenue
                        FROM bookings b
                        JOIN routes r ON b.route_id = r.route_id
                        WHERE b.booking_status = 'PAID'
                        GROUP BY r.route_id
                        ORDER BY revenue DESC"; // Most profitable routes show at the top
                
                // Execute Query.
                $result = $conn->query($sql);
                
                $grand_total = 0; // Variable to store the sum of ALL revenue
                
                if ($result->num_rows > 0) {
                    // Loop through each route's revenue result
                    while($row = $result->fetch_assoc()) {
                        $grand_total += $row['revenue']; // Add this route's money to the total
                        echo "<tr>";
                        echo "<td>" . $row['route_id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) . "</td>";
                        echo "<td>" . $row['departure_date'] . "</td>";
                        echo "<td>" . $row['total_bookings'] . " seats booked</td>";
                        echo "<td>" . number_format($row['revenue'], 2) . "</td>";
                        echo "</tr>";
                    }
                    // Show a final "Grand Total" row at the bottom of the table
                    echo "<tr style='background-color:#eee;'><td colspan='4' style='text-align:right'><strong>TOTAL SYSTEM REVENUE:</strong></td><td><strong>" . number_format($grand_total, 2) . " KES</strong></td></tr>";
                } else {
                     // If no tickets have been sold yet
                     echo "<tr><td colspan='5' style='text-align:center;'>No tickets have been sold yet. Sales records will appear here.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Padding and Footer -->
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
