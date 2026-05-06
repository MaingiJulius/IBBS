<?php
/**
 * SYSTEM AUDIT DASHBOARD (admin_logs.php)
 * Purpose: This page is the 'Control Room' where admins can see the activity logs.
 * It shows every major action taken in the system, like who deleted what.
 */

// [1] Include the database connection so we can read the 'Logs' table.
require_once 'db_connection.php';

// [2] Start the session to verify if the user is a valid Administrator.
session_start();

// [3] Security Check: Only people with the 'ADMIN' role can see this page.
// If a regular passenger tries to access this URL, they are kicked back to the login page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.html");
    exit();
}

/**
 * DATABASE TASK: Fetch all activity logs.
 * We order them by 'log_id' in DESCENDING order so that the NEWEST logs appear at the top.
 */
$sql = "SELECT * FROM Logs ORDER BY log_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Audit Logs - Wema Travellers</title>
    <!-- [4] Basic Metadata for mobile responsiveness and styling -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    
    <!-- [5] Page-specific CSS for a clean, professional table layout -->
    <style>
        /* White box to hold the logs */
        .logs-container { max-width: 1200px; margin: 30px auto; padding: 25px; background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border: 1px solid #e1e8ed; }
        
        /* Table styling */
        .logs-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 0.95rem; }
        .logs-table th, .logs-table td { padding: 15px; text-align: left; border-bottom: 1px solid #edf2f7; }
        
        /* Header styling with the theme color (Purple) */
        .logs-table th { background-color: var(--purple); color: white; font-weight: 700; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; }
        
        /* Badges: Colored labels to quickly identify 'DELETIONS' (Red) or 'UPDATES' (Yellow) */
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .badge-deletion { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-update { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        
        /* Date formatting for better readability */
        .date-cell { font-family: 'Courier New', Courier, monospace; color: #4a5568; font-weight: bold; }
    </style>
</head>
<body class="admin-role">
    <!-- [6] Include the Top Navigation Bar -->
    <script src="js/header2.js"></script>
    
    <!-- [7] Spacer to prevent content from hiding behind the fixed header -->
    <div style="height: 100px;"></div>

    <div class="logs-container">
        <!-- [8] Page Heading and Subtext -->
        <h2 style="color: var(--purple); margin-bottom: 5px;">📜 System Audit & Activity Trail</h2>
        <p style="color: #718096; margin-bottom: 25px; font-size: 0.9rem;">
            This ledger tracks every sensitive modification. It provides accountability for all administrative and user actions.
        </p>

        <!-- [9] The Logs Table -->
        <table class="logs-table">
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>User / Performer</th>
                    <th>Action</th>
                    <th>Activity Description</th>
                    <th>Time</th>
                    <th>Date (Y/M/D)</th>
                </tr>
            </thead>
            <tbody>
                <!-- [10] Check if any logs actually exist in the database -->
                <?php if ($result->num_rows > 0): ?>
                    <!-- [11] Loop through each log and display it as a row -->
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <!-- [12] Unique ID for the log -->
                            <td style="color: #94a3b8; font-weight: bold;">#<?= $row['log_id'] ?></td>
                            
                            <!-- [13] Name of the person who performed the action -->
                            <td>
                                <strong><?= htmlspecialchars($row['name']) ?></strong> 
                                <span style="display:block; font-size:0.75rem; color:#94a3b8;">User ID: <?= $row['user_id'] ?></span>
                            </td>
                            
                            <!-- [14] Action Type with a colored badge -->
                            <td>
                                <span class="badge <?= $row['type'] === 'DELETION' ? 'badge-deletion' : 'badge-update' ?>">
                                    <?= $row['type'] ?>
                                </span>
                            </td>
                            
                            <!-- [15] Description of exactly what was changed -->
                            <td style="color: #2d3748;"><?= htmlspecialchars($row['description']) ?></td>
                            
                            <!-- [16] Time of the event -->
                            <td style="font-size: 0.85rem; color: #718096;"><?= $row['time'] ?></td>
                            
                            <!-- [17] Date of the event (formatting '-' to '/' for aesthetics) -->
                            <td class="date-cell"><?= str_replace('-', '/', $row['date']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <!-- [18] If no logs are found, show this message -->
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 60px; color: #a0aec0; font-style: italic;">
                            System Notification: No activity logs have been recorded in the database yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- [19] Footer spacing and scripts -->
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
<?php
// [20] Close the database connection to stay efficient.
$conn->close();
?>

