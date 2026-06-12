<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ADMIN') {
    die("Access Denied: High-Level Personnel Performance Data. Administrator Authorization Required for Audit.");
}
?>
<!DOCTYPE html>                                                         <!-- [17] Define standard HTML5 document type for modern browsers. -->
<html lang="en">
<head>
    <meta charset="UTF-8">                                           <!-- [20] Declare UTF-8 for international character support. -->
    <title>Agent Productivity Report - Wema Travellers</title>          <!-- [21] Website title. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [22] Responsive scaling for mobile device compatibility. -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'admin') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="view-container">
        <div class="no-print" style="margin-bottom: 30px;">
            <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; padding: 12px 30px; font-weight: 700;">← Back to Main Hub</a>
            <button onclick="window.print()" class="button regular-button" style="text-decoration:none; background-color: #64748b; color: white; border-radius: 50px; border: none; padding: 12px 30px; margin-left: 10px; font-weight: 700; cursor: pointer;">Print Report</button>
        </div>
        <h2 class="no-print" style="color: var(--purple); margin-bottom: 5px;">📈 Agent Productivity Leaderboard</h2>
        <p class="no-print" style="color: #718096; margin-bottom: 30px;">Auditing ticket sales volume and gross fiscal contribution per staff member.</p>
        <table class="crud-table">
            <thead><tr><th>Staff Identity (Agent)</th><th>Sales Volume (Tickets)</th><th>Fiscal Contribution (KES)</th></tr></thead> <!-- [43] Head. -->
            <tbody>
                <?php
                $sql = "SELECT u.first_name, u.last_name, COUNT(b.booking_id) as total_bookings, SUM(r.cost) as total_sales FROM users u JOIN bookings b ON u.user_id = b.user_id JOIN routes r ON b.route_id = r.route_id WHERE u.role = 'AGENT' GROUP BY u.user_id ORDER BY total_sales DESC";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td style='font-weight: 700; color: #1e293b;'>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
                        echo "<td><span style='background: #fdf2f8; color: #be185d; padding: 4px 10px; border-radius: 4px; font-weight: 800; border: 1px solid #fbcfe8; font-size: 0.85rem;'>" . $row['total_bookings'] . " TICKETS</span></td>";
                        echo "<td style='font-weight: 900; color: #2d3748; letter-spacing: 0.5px; font-family: monospace;'>" . number_format($row['total_sales'], 2) . " /-</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center; padding: 60px; color: #a0aec0; font-style: italic;'>No agent performance data initialized. Analytics will populate as bookings occur.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div style="height: 120px;"></div><script src="js/footer.js"></script>
    <script src="js/table_manager.js"></script>
</body>
</html>