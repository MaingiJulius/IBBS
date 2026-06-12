<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ADMIN') {
    die("Access Denied: Highly Sensitive Financial Data. Administrator Authorization Required.");
}
?>
<!DOCTYPE html>                                                         <!-- [17] Define the document type as standard HTML5. -->
<html lang="en">                                                     <!-- [18] Root element defining English as the content language. -->
<head>                                                               <!-- [19] Metadata and resource head section. -->
    <meta charset="UTF-8">                                           <!-- [20] Declare UTF-8 encoding for currency and textual symbols. -->
    <title>Financial Report: System Revenue - Wema Travellers</title>     <!-- [21] Website title for the browser tab. -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="back-btn-container no-print">
        <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; padding: 12px 30px;">← Return to Main Hub</a> <!-- [43] link. -->
    </div>
    <div class="view-container">
        <h2 class="no-print" style="color: var(--purple); margin-bottom: 5px;">💰 Revenue Insights & Performance</h2>
             on paper printouts. -->
        <p class="no-print" style="color: #718096; margin-bottom: 30px;">Analyzing gross earnings across all active bus routes based on finalized bookings.</p>
        <div class="no-print">
            <button onclick="window.print()" class="button regular-button pink-background" style="margin-bottom: 20px;">🖨️ Print Financial Statement</button>
        </div>
        <table class="crud-table">
            <thead>                                                      <!-- [49] Data label row. -->
                <tr><th>Route ID</th><th>Travel Segment</th><th>Schedule Date</th><th>Occupancy (Seats)</th><th>Gross Earnings (KES)</th></tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT r.route_id, r.from_location, r.to_location, r.departure_date, COUNT(b.booking_id) as total_bookings, SUM(r.cost) as revenue FROM bookings b JOIN routes r ON b.route_id = r.route_id WHERE b.booking_status = 'PAID' GROUP BY r.route_id ORDER BY revenue DESC";
                $result = mysqli_query($conn, $sql);
                $grand_total = 0;
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $grand_total += $row['revenue'];
                        echo "<tr>";
                        echo "<td><strong style='color: #4a5568;'>" . $row['route_id'] . "</strong></td>";
                        echo "<td style='font-weight: 600; color: #1e293b;'>" . htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) . "</td>";
                        echo "<td style='color: #718096;'>" . $row['departure_date'] . "</td>";
                        echo "<td><span style='background: #f0fff4; color: #2f855a; padding: 4px 10px; border-radius: 4px; font-weight: 800;'>" . $row['total_bookings'] . " Sold</span></td>";
                        echo "<td style='font-weight: 900; color: #2d3748;'>" . number_format($row['revenue'], 2) . " /-</td>";
                        echo "</tr>";
                    }
                    echo "<tr style='background-color: #f7fafc; border-top: 2px solid var(--purple);'>";
                    echo "    <td colspan='4' style='text-align:right'><strong>ACCUMULATED SYSTEM REVENUE:</strong></td>";
                    echo "    <td><strong style='color: var(--purple); font-size: 1.2rem;'>" . number_format($grand_total, 2) . " KES</strong></td>";
                    echo "</tr>";
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding: 80px; color: #a0aec0; font-style: italic;'>Financial ledger empty.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div style="height: 120px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>