<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'AGENT' && $_SESSION['role'] !== 'ADMIN')) {
    header("Location: login.html");
    exit();
}
$agent_name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .welcome-banner { background: linear-gradient(135deg, var(--purple), var(--pink)); color: #1e1b4b; padding: 50px 40px; border-radius: 20px; margin-bottom: 40px; text-align: center; }
        .welcome-banner h1 { margin: 0; font-size: 2.8em; }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="dashboard-container">
        <div class="welcome-banner">
            <h1>Agent Portal</h1>
            <p>Ready for duty, <?= htmlspecialchars($agent_name) ?>. Access your control modules via the <strong>Operations</strong> menu above.</p>
        </div>
        <div style="text-align: center; padding: 40px; background: white; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eee;">
            <h3 style="color: var(--purple);">Agent Operations Hub</h3>
            <p style="color: #666;">All your tasks, from booking to reporting, are now conveniently grouped in the navigation bar above.</p>
        </div>
    </div>
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>