<?php
require_once 'db_connection.php';
require_once 'logger.php';
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'ADMIN' && $_SESSION['role'] != 'AGENT')) {
    die("Access Denied: High-Level Personnel Authentication Required for Sentiment Audit.");
}
if (isset($_GET['delete_feedback'])) {
    $fid = $_GET['delete_feedback'];
    $sql = "DELETE FROM feedback WHERE feedback_id = $fid";
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed Customer Feedback (FID: $fid)");
    mysqli_query($conn, $sql);
    header("Location: view_feedback.php?msg=Success: Customer sentiment entry has been permanently DELETED.");
    exit();
}
?>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'staff') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="view-container">
        <div class="no-print" style="margin-bottom: 30px;">
            <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; padding: 12px 30px; font-weight: 700;">← Back to Command Center</a>
            <button onclick="window.print()" class="button regular-button" style="text-decoration:none; background-color: #64748b; color: white; border-radius: 50px; border: none; padding: 12px 30px; margin-left: 10px; font-weight: 700; cursor: pointer;">Print Feedback Report</button>
        </div>
        <h2 class="no-print" style="color: var(--purple); margin-bottom: 5px;">⭐ Guest Satisfaction & Reviews</h2>
        <p class="no-print" style="color: #718096; margin-bottom: 30px;">Analyzing passenger feedback and ratings submitted across all travel segments.</p>
        <?php if(isset($_GET['msg'])): ?>
        <?php endif; ?>
        <table class="crud-table">
            <thead><tr><th>Processed</th><th>Traveler Identity</th><th>Sentiment</th><th>Comment</th><th>Trip Path</th><th class="no-print">Control Deck</th></tr></thead>
            <tbody>
                <?php
                $sql = "SELECT f.*, u.first_name, u.last_name, u.email, r.from_location, r.to_location FROM feedback f JOIN users u ON f.user_id = u.user_id JOIN routes r ON f.route_id = r.route_id ORDER BY f.feedback_date DESC";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td style="font-weight: 700; color: #2d3748;"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td style="font-size: 0.85rem; color: #4a5568;"><?= htmlspecialchars($row['email']) ?></td>
                        <td style="font-size: 1.1rem; letter-spacing: 2px;">
                            <?php
                            $rating = intval($row['rating']);
                            echo str_repeat('⭐', $rating) . str_repeat('☆', 5 - $rating);
                            ?>
                        </td>
                        <td style="color: #4a5568; line-height: 1.6; max-width: 300px;"><?= htmlspecialchars($row['comments']) ?></td>
                        <td style="font-size: 0.85rem; color: #718096; font-weight: 600;"><?= htmlspecialchars($row['from_location'] . ' → ' . $row['to_location']) ?></td>
                        <td class="no-print">
                            <a href="view_feedback.php?delete_feedback=<?= $row['feedback_id'] ?>"
                               class="button regular-button"
                               style="background:#ef4444; color:white; border-radius:50px; padding:6px 16px; font-size:0.8rem; text-decoration:none; font-weight:700;"
                               onclick="return confirm('Delete this feedback entry?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="js/footer.js"></script>
    <script src="js/table_manager.js"></script>
</body>
</html>