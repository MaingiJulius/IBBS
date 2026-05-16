<?php
// <?php (opening tag) tells the server to start interpreting the code as PHP.

/**
 * PASSENGER DASHBOARD (dashboard.php)
 */

// require_once (require once) includes the database connection.
require_once 'db_connection.php';

// session_start (session start) starts the user session.
session_start();

// --- SECURITY CHECK ---
// if (if) check for login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// $ (dollar sign) variable marker.
// role (role) label.
// = (equals sign) assignment.
// $_SESSION['role'] (session data).
$role = $_SESSION['role'];

// --- REDIRECTION LOGIC ---
if ($role === 'ADMIN') {
    header("Location: admin_dashboard.php");
    exit();
} elseif ($role === 'AGENT') {
    header("Location: agent_dashboard.php");
    exit();
}

// $ (dollar sign) variable marker.
// user_id (user i d) label.
$user_id = $_SESSION['user_id'];

// $ (dollar sign) variable marker.
// sql (s q l) label.
$sql = "SELECT first_name FROM users WHERE user_id = ?";

// mysqli_prepare (MySQL Improved prepare) prepares the bridge.
// ( $conn , $sql ) (connection variable , SQL variable).
$stmt = mysqli_prepare($conn, $sql);

// mysqli_stmt_bind_param (MySQL Improved statement bind parameter).
// ( $stmt , "i" , $user_id )
// "i" means 1 integer (number).
mysqli_stmt_bind_param($stmt, "i", $user_id);

// mysqli_stmt_execute (MySQL Improved statement execute) runs the query.
mysqli_stmt_execute($stmt);

// $ (dollar sign) variable marker.
// res (result) label.
$res = mysqli_stmt_get_result($stmt);

// $ (dollar sign) variable marker.
// user_data (user data) label.
$user_data = mysqli_fetch_assoc($res);

// mysqli_stmt_close (MySQL Improved statement close).
mysqli_stmt_close($stmt);
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
