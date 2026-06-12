<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Master Control Panel - Wema Travellers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f7f9fc; margin: 0; padding: 0; }
        .container { width: 90%; max-width: 1200px; margin: 30px auto; padding: 20px; }
        .welcome-banner { background: var(--purple); color: #ffffff; padding: 50px 20px; border-radius: 16px; margin-bottom: 40px; text-align: center; }
        .welcome-banner h1 { margin: 0; font-size: 2.5rem; }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="container">
        <div class="welcome-banner">
            <h1>Administrative Operations</h1>
            <p>Authorized access granted to: <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></p>
        </div>
        <div style="text-align: center; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e8ed;">
            <h3 style="color: var(--purple);">Welcome to the Control Center</h3>
            <p style="color: #5a6b7d;">All administrative tools and reports are now organized in the navigation bar above.</p>
            <p style="color: #5a6b7d;">Select a category (<strong>Manage Actors</strong>, <strong>Logistics</strong>, or <strong>Reports</strong>) to begin your operations.</p>
        </div>
    </div>
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>