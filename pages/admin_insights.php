<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
> (greater than sign) is the standard declaration for a modern web page. -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Intel - Wema Travellers</title>
    <link rel="stylesheet" href="css/style.css">
    "stylesheet" href (reference) = (equals sign) "css/style.css" > (greater than sign). -->
    <link rel="stylesheet" href="css/main.css">
    "stylesheet" href (reference) = (equals sign) "css/main.css" > (greater than sign). -->
    <style>
        .container { width: 90%; max-width: 1000px; margin: 50px auto; text-align: center; }
        .report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 40px; }
        .report-link { display: block; padding: 30px; background: white; border: 1px solid #ddd; border-radius: 10px; text-decoration: none; color: black; font-weight: bold; }
        .report-link:hover { background: var(--purple); color: white; }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="container">
        <a href="admin_dashboard.php" class="button" style="width:auto; padding:10px 20px;">← Back to Panel</a>
        <h1>Data Insights Hub</h1>
        <p>Access business intelligence reports and filtered analytics.</p>
        <div class="report-grid">
            <a href="report_this_week.php" class="report-link">📊 Weekly Report</a>
            <a href="report_last_week.php" class="report-link">⏮️ Last Week Audit</a>
            <a href="report_this_month.php" class="report-link">📅 Monthly Sales</a>
            <a href="report_last_month.php" class="report-link">🕒 Previous Month</a>
            <a href="report_this_year.php" class="report-link">💰 Annual Report</a>
        </div>
    </div>
    <script src="js/footer.js"></script>
</body>
</html>