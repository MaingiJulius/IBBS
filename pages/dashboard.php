<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$role = $_SESSION['role'];
if ($role === 'ADMIN') {
    header("Location: admin_dashboard.php");
    exit();
} elseif ($role === 'AGENT') {
    header("Location: agent_dashboard.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$sql="SELECT first_name FROM users WHERE user_id=$user_id";
$res=mysqli_query($conn,$sql);
$user_data = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passenger Dashboard - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-container { max-width: 1000px; margin: 40px auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .dashboard-header { text-align: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 40px; }
        .action-grid { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; }
        .action-card { flex: 1; min-width: 250px; text-align: center; }
        .action-card h3 { color: var(--purple); margin-bottom: 15px; font-size: 1.4em; }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Passenger Dashboard</h2>
            <p>Welcome back, <?= htmlspecialchars($user_data['first_name']) ?>! What would you like to do today?</p>
        </div>
        <div class="action-grid">
            <div class="action-card">
                <h3>Book trip</h3>
                <a href="book.php" class="button regular-button green-background" style="text-decoration:none; padding: 20px 40px;">BOOK NOW</a>
            </div>
            <div class="action-card">
                <h3>View history</h3>
                <a href="view_user_history.php" class="button regular-button pink-background" style="text-decoration:none; padding: 20px 40px;">VIEW HISTORY</a>
            </div>
            <div class="action-card">
                <h3>View ticket</h3>
                <a href="view_tickets.php" class="button regular-button purple-background" style="text-decoration:none; padding: 20px 40px;">VIEW TICKETS</a>
            </div>
        </div>
    </div>
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>