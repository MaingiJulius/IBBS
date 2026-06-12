<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.html");
    exit();
}
$sql = "SELECT b.booking_id, b.booking_time, CONCAT(u.first_name, ' ', u.last_name) AS passenger_name, r.from_location, r.to_location, b.seat_number, b.booking_status, r.cost
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN routes r ON b.route_id = r.route_id
        WHERE PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM CURDATE()), EXTRACT(YEAR_MONTH FROM b.booking_time)) = 1
        ORDER BY b.booking_time DESC";
$result = mysqli_query($conn, $sql);
$total_revenue = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retrospective Audit: Previous Month - Wema Travellers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .report-container { width: 95%; max-width: 1200px; margin: 40px auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.04); }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 35px; }
        .data-table th, .data-table td { border-bottom: 1px solid #f1f5f9; padding: 18px; text-align: left; }
        .data-table th { background-color: var(--purple); color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; }
        .data-table tr:nth-child(even) { background-color: #fbfcfe; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="report-container">
        <div class="no-print" style="margin-bottom: 30px;">
            <a href="admin_insights.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; display: inline-block; width: auto; padding: 12px 35px; font-weight: 700;">← Back to Insights Hub</a>
            <button onclick="window.print()" class="button regular-button" style="text-decoration:none; background-color: #64748b; color: white; border-radius: 50px; border: none; padding: 12px 35px; margin-left: 10px; font-weight: 700; cursor: pointer;">Print Report</button>
        </div>
        <h2 style="color: var(--purple); margin-bottom: 5px;">📉 Historical Performance Review</h2>
        <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 10px;">Analyzing every passenger transaction recorded during the previous complete calendar cycle.</p>
        <table class="data-table">
            <thead><tr><th>Ref ID</th><th>Stamp</th><th>Full Name</th><th>Segment</th><th>Seat Map</th><th>Price</th><th>Status Record</th></tr></thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong style="color: #94a3b8;"><?= $row['booking_id'] ?></strong></td>
                            <td style="font-family: 'Courier New', monospace; font-size: 0.85rem; color: #475569;"><?= $row['booking_time'] ?></td>
                            <td style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($row['passenger_name']) ?></td>
                            <td style="color: #64748b;"><?= htmlspecialchars($row['from_location'] . ' → ' . $row['to_location']) ?></td>
                            <td><span style="background: #f8fafc; color: #475569; padding: 5px 10px; border-radius: 6px; font-weight: 800; border: 1px solid #e2e8f0;"><?= $row['seat_number'] ?></span></td>
                            <td style="font-weight: 700; color: #0f172a;">$<?= number_format($row['cost'], 2) ?></td>
                            <td style="color: <?= ($row['booking_status'] == 'CANCELLED') ? '#ef4444' : '#10b981' ?>; font-weight: 900; text-transform: uppercase; font-size: 0.85rem;"><?= $row['booking_status'] ?></td>
                        </tr>
                        <?php if($row['booking_status'] !== 'CANCELLED') { $total_revenue += $row['cost']; } ?>
                    <?php endwhile; ?>
                    <tr style="background-color: #f8fafc; font-size: 1.1rem; border-top: 2px solid var(--purple);">
                        <td colspan="5" style="text-align: right; font-weight: 800; color: var(--purple);">TOTAL REVENUE:</td>
                        <td colspan="2" style="font-weight: 900; color: #10b981; font-size: 1.3rem;">$<?= number_format($total_revenue, 2) ?></td>
                    </tr>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 100px;"><div style="font-size: 1.25rem; font-style: italic;">No historical records found for the previous month instance.</div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="height: 120px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
re. -->nt. -->