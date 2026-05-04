<?php
/**
 * ACTIVITY LOGS (admin_logs.php)
 * Purpose: Allows administrators to monitor deletions and updates in the system.
 * Accessible only to: ADMIN role.
 */

require_once 'db_connection.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.html");
    exit();
}

// Fetch logs
$sql = "SELECT * FROM Logs ORDER BY log_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs - IBBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .logs-container { max-width: 1200px; margin: 30px auto; padding: 20px; background: white; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .logs-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .logs-table th, .logs-table td { padding: 15px; text-align: left; border-bottom: 1px solid #edf2f7; }
        .logs-table th { background-color: var(--purple); color: white; font-weight: bold; text-transform: uppercase; font-size: 0.85rem; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; }
        .badge-deletion { background: #fee2e2; color: #991b1b; }
        .badge-update { background: #fef3c7; color: #92400e; }
        .date-cell { font-family: monospace; color: #4a5568; }
    </style>
</head>
<body class="admin-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <div class="logs-container">
        <h2 style="color: var(--purple);">📜 System Activity Logs</h2>
        <p style="color: #718096; margin-bottom: 20px;">Monitoring sensitive data mutations across the platform.</p>

        <table class="logs-table">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Performer</th>
                    <th>Action Type</th>
                    <th>Description</th>
                    <th>Time</th>
                    <th>Date (yyyy/mm/dd)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['log_id'] ?></td>
                            <td><strong><?= htmlspecialchars($row['name']) ?></strong> (UID: <?= $row['user_id'] ?>)</td>
                            <td>
                                <span class="badge <?= $row['type'] === 'DELETION' ? 'badge-deletion' : 'badge-update' ?>">
                                    <?= $row['type'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= $row['time'] ?></td>
                            <td class="date-cell"><?= str_replace('-', '/', $row['date']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 40px; color: #a0aec0;">No logs recorded yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
<?php
$conn->close();
?>
