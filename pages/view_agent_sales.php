<?php
// =================================================================
// AGENT SALES REPORT (view_agent_sales.php)
// =================================================================
// This report shows the Administrator how well each Agent is performing.
// It tracks how many tickets each agent has sold and the total money they've brought in.
// =================================================================

// Include DB connection.
require_once 'db_connection.php';
// Start session.
session_start();

// --- SECURITY CHECK ---
// Sales performance is private. Only the 'ADMIN' can see this report.
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ADMIN') {
    die("Access Denied: Performance reports are restricted to Administrators.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Sales Performance - Wema Travellers</title>
    <!-- Import CSS -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Table styling for the agent performance report */
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

        /* Styling for the sales performance table */
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
    <!-- Standard Navbar script -->
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <div class="back-btn-container">
        <!-- Back link -->
        <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Back to Dashboard</a>
    </div>

    <div class="view-container">
        <h2>Agent Sales Performance Report</h2>

        <!-- SECTION: Sales Ranking Table -->
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Staff Name (Agent)</th>
                    <th>Tickets Sold (Bookings)</th>
                    <th>Total Revenue Contribution (KES)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // FETCH SALES DATA
                // This SQL query finds all users whose role is 'AGENT'.
                // It then joins with the 'bookings' and 'routes' tables to sum up their sales.
                $sql = "SELECT u.first_name, u.last_name, COUNT(b.booking_id) as total_bookings, SUM(r.cost) as total_sales
                        FROM users u
                        JOIN bookings b ON u.user_id = b.user_id
                        JOIN routes r ON b.route_id = r.route_id
                        WHERE u.role = 'AGENT'
                        GROUP BY u.user_id
                        ORDER BY total_sales DESC"; // Top performing agents show at the top
                
                // Execute Query.
                $result = $conn->query($sql);
                
                // If there are agents who have made sales...
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
                        echo "<td>" . $row['total_bookings'] . " tickets</td>";
                        echo "<td>" . number_format($row['total_sales'], 2) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    // If no agent has made a sale yet
                    echo "<tr><td colspan='3' style='text-align:center;'>No agent sales recorded yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Padding for bottom -->
    <div style="height: 100px;"></div>
    <!-- Universal footer -->
    <script src="js/footer.js"></script>
</body>
</html>
