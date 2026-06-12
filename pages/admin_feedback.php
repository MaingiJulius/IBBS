<?php
session_start();
require_once 'db_connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Security Error: Access denied. This report is restricted to authorized Administrators.");
}
$sql_report = "SELECT * FROM feedback_report_view ORDER BY feedback_date DESC";
$res_report = mysqli_query($conn, $sql_report);
if (!$res_report) {
    die("System Error: Unable to generate feedback report. Detail: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>                                                         <!-- [19] Define standard HTML5 document type for modern browsers. -->
<html lang="en">
<head>
    <meta charset="UTF-8">                                           <!-- [22] Declare UTF-8 for international character support. -->
    <title>Quality Audit: Feedback Report - Wema Travellers</title>      <!-- [23] Browser tab title identifier. -->
    <link rel="stylesheet" href="css/style.css">
    </style>                                                         <!-- [37] Terminate internal CSS block. -->
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div class="container">
        <div class="no-print" style="text-align: center; margin-bottom: 30px;">
            <a href="dashboard.php" class="btn" style="background-color: #4a5568;">← Back to Dashboard Hub</a>
            <button onclick="window.print()" class="btn" style="background-color: #64748b; border: none; margin-left: 10px; cursor: pointer;">Print Feedback Audit</button>
        </div>
        <h1>Customer Satisfaction Report</h1>                            <!-- [42] Section title. -->
        <table>
            <thead><tr><th>ID</th><th>Passenger</th><th>Bus ID</th><th>Vehicle Name</th><th>Score (1-5)</th><th>Passenger Comments</th><th>Date Submitted</th></tr></thead> <!-- [44] head labels. -->
            <tbody>
                <?php
                if ($res_report && mysqli_num_rows($res_report) > 0):
                    while ($fb_row = mysqli_fetch_assoc($res_report)): ?>
                <tr><td style="color: #888;"><?= $fb_row['feedback_id'] ?></td> <!-- [47] identifier. -->
                    <td style="font-weight: 600;"><?= htmlspecialchars($fb_row['user_name']) ?></td>
                    <td style="font-family: monospace;"><?= $fb_row['bus_id'] !== null ? htmlspecialchars($fb_row['bus_id']) : '<em>N/A</em>' ?></td> <!-- [49] vehicle link. -->
                    <td><?= $fb_row['bus_name'] !== null ? htmlspecialchars($fb_row['bus_name']) : '<span style="color: #bbb;">No Bus Linked</span>' ?></td> <!-- [50] vehicle branding. -->
                    <td style="font-weight: bold; color: <?= $fb_row['rating'] >= 4 ? '#27ae60' : '#e67e22' ?>;"><?= $fb_row['rating'] ?> / 5</td> <!-- [51] score weighting. -->
                    <td style="max-width: 300px; font-style: italic; color: #444;">"<?= htmlspecialchars($fb_row['comments']) ?>"</td>
                    <td style="font-size: 0.85rem; color: #718096;"><?= $fb_row['feedback_date'] ?></td> <!-- [53] timestamp. -->
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="7" style="text-align: center; padding: 50px; color: #999;">The feedback registry is currently empty. No reviews have been submitted yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="center-button no-print"><a href="dashboard.php" class="btn">← Back to Dashboard Hub</a></div> <!-- [60] return cmd. Wrapped in no-print. -->
    </div>
    <script src="js/footer.js"></script>
</body>
</html>