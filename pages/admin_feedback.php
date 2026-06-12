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
<html lang="en">
<head>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div class="container">
        <div class="no-print" style="text-align: center; margin-bottom: 30px;">
            <a href="dashboard.php" class="btn" style="background-color: #4a5568;">← Back to Dashboard Hub</a>
            <button onclick="window.print()" class="btn" style="background-color: #64748b; border: none; margin-left: 10px; cursor: pointer;">Print Feedback Audit</button>
        </div>
        <table>
            <tbody>
                <?php
                if ($res_report && mysqli_num_rows($res_report) > 0):
                    while ($fb_row = mysqli_fetch_assoc($res_report)): ?>
                    <td style="font-weight: 600;"><?= htmlspecialchars($fb_row['user_name']) ?></td>
                    <td style="max-width: 300px; font-style: italic; color: #444;">"<?= htmlspecialchars($fb_row['comments']) ?>"</td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="7" style="text-align: center; padding: 50px; color: #999;">The feedback registry is currently empty. No reviews have been submitted yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="js/footer.js"></script>
</body>
</html>