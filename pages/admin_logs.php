<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.html");
    exit();
}
$sql = "SELECT * FROM Logs ORDER BY log_id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Audit Logs - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .logs-container { max-width: 1200px; margin: 30px auto; padding: 25px; background: white; border-radius: 12px; }
        .logs-table { width: 100%; border-collapse: collapse; }
        .logs-table th, .logs-table td { padding: 15px; border-bottom: 1px solid #eee; }
        .logs-table th { background: var(--purple); color: white; }
    </style>
</head>
<body class="admin-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="logs-container">
        <h2 style="color: var(--purple);">📜 System Audit Trail</h2>
        <table class="logs-table">
            <thead>
                <tr>
                    <th>Ref ID</th><th>Performer</th><th>Action</th><th>Description</th><th>Time</th><th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>#<?= $row['log_id'] ?></td>
                            <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                            <td><?= $row['type'] ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= $row['time'] ?></td>
                            <td><?= $row['date'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>