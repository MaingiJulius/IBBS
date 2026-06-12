<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'ADMIN' && $_SESSION['role'] != 'AGENT')) {
    die("Security Restriction: You do not have permission to view internal occupancy reports.");
}
?>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'staff') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="view-container">
        <div class="no-print" style="margin-bottom: 30px;">
            <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; padding: 12px 30px; font-weight: 700;">← Back to Main Dashboard</a>
            <button onclick="window.print()" class="button regular-button" style="text-decoration:none; background-color: #64748b; color: white; border-radius: 50px; border: none; padding: 12px 30px; margin-left: 10px; font-weight: 700; cursor: pointer;">Print Report</button>
        </div>
        <h2 class="no-print" style="color: var(--purple); margin-bottom: 5px;">📊 Fleet Occupancy Audit</h2>
        <p class="no-print" style="color: #64748b; margin-bottom: 35px;">Real-time analysis of seat distribution and sales efficiency across active routes.</p>
        <table class="crud-table">
            <tbody>
                <?php
                $sql = "SELECT bs.bus_name, bs.reg_no, bs.max_passengers, r.departure_date, COUNT(b.booking_id) as booked_seats FROM buses bs JOIN routes r ON bs.bus_id = r.bus_id LEFT JOIN bookings b ON r.route_id = b.route_id GROUP BY bs.bus_id, r.route_id, r.departure_date ORDER BY r.departure_date DESC";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)):
                    $max = ($row['max_passengers'] > 0) ? $row['max_passengers'] : 1;
                    $occupancy_percent = ($row['booked_seats'] / $max) * 100;
                    $status_color = ($occupancy_percent > 85) ? '#ef4444' : (($occupancy_percent > 50) ? '#f59e0b' : '#38a169');
                ?>
                <tr><td><strong style="color: #1e293b;"><?= htmlspecialchars($row['bus_name']) ?></strong></td>
                    <td style="font-family: 'Courier New', monospace; color: #475569; font-weight: 600;"><?= htmlspecialchars($row['reg_no']) ?></td>
                    <td style="color: var(--purple); font-weight: 900;"><?= $row['booked_seats'] ?> Sold</td>
                    <td><div style="display:flex; align-items:center; gap:8px;"><span style="background: <?= $status_color ?>10; color: <?= $status_color ?>; padding: 6px 14px; border-radius: 8px; font-weight: 900; font-size: 0.85rem; border: 1px solid <?= $status_color ?>30; text-transform: uppercase;"><?= number_format($occupancy_percent, 1) ?>% Load</span></div></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div style="height: 120px;"></div><script src="js/footer.js"></script>
    <script src="js/table_manager.js"></script>
</body>
</html>