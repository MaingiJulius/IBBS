<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * ADMINISTRATION: CUSTOMER FEEDBACK MODERATION (view_feedback.php)  // [4] Title identifying this script as the customer satisfaction dashboard.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script provides staff with an interface to audit    // [6] Main objective: maintain customer service quality standards.
 * and moderate user-submitted reviews and ratings.                // [7] Task: review and moderate passenger sentiments.
 * Features: Multi-table aggregation, interactive list, and secure deletion. // [8] Technical scope of the script.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
require_once 'logger.php';                                           // [11.5] Import logging utility for audit trail.
session_start();                                                    // [12] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'ADMIN' && $_SESSION['role'] != 'AGENT')) { // [13] Restrict access to authenticated STAFF (Admin/Agent) only.
    die("Access Denied: High-Level Personnel Authentication Required for Sentiment Audit."); // [14] Halt execution for unauthorized personnel.
}                                                                    // [15] Close security barrier.

if (isset($_GET['delete_feedback'])) {                               // [16] Action Handle: Detect a moderation request (deletion) via URL parameter.
    $fid = $_GET['delete_feedback'];                                 // [17] Map the targeted feedback ID to a local variable.
    $stmt = $conn->prepare("DELETE FROM feedback WHERE feedback_id = ?"); // [18] Prepare a secure SQL template for record removal.
    $stmt->bind_param("i", $fid);                                    // [19] Safely inject the ID integer into the query template.
    
    // [AUDIT LOG] Record the deletion before execution.
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed Customer Feedback (FID: $fid)");

    $stmt->execute();                                                // [20] Commit the destructive command to the database engine.
    $stmt->close();                                                  // [21] Release database resources.
    header("Location: view_feedback.php?msg=Success: Customer sentiment entry has been permanently DELETED."); // [22] Redirect with confirmation message.
    exit();                                                          // [23] Halt logic flow.
}                                                                    // [24] Close deletion block.
?>                                                                   <!-- [25] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [26] Define standard HTML5 document type. -->
<html lang="en">                                                     <!-- [27] Root element identifying English as the layout language. -->
<head>                                                               <!-- [28] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [29] Declare UTF-8 for international character support. -->
    <title>Customer Satisfaction Review - Wema Travellers Hub</title>    <!-- [30] Website title for browser identification. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [31] Responsive scaling for mobile devices. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [32] Load shared component style assets. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [33] Load global project branding variables. -->
    <style>                                                          /* [34] Page-specific internal CSS architecture. */
        .view-container { max-width: 1200px; margin: 30px auto; padding: 40px; background: #ffffff; border-radius: 12px; box-shadow: 0 15px 40px rgba(0,0,0,0.06); } /* [35] Main card. */
        .back-btn-container { padding: 20px; max-width: 1200px; margin: 0 auto; } /* [36] navigation buffer. */
        .crud-table { width: 100%; border-collapse: collapse; margin-top: 30px; } /* [37] sentiment grid. */
        .crud-table th, .crud-table td { padding: 18px; border-bottom: 1px solid #edf2f7; text-align: left; } /* [38] cell padding. */
        .crud-table th { background-color: var(--purple); color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem; } /* [39] branding. */
        .action-btn { padding: 8px 15px; border-radius: 8px; text-decoration: none; color: #ffffff; font-size: 0.85rem; font-weight: 600; display: inline-block; } /* [40] cmd. */
        .btn-delete { background-color: #ef4444; } /* [41] destructive alert. */
    </style>                                                         <!-- [42] Terminate internal CSS block. -->
</head>                                                              <!-- [43] Close head section. -->

<body class="<?= strtolower($_SESSION['role'] ?? 'staff') ?>-role"> <!-- [44] Start visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [45] Inject the unified administrative header. -->
    <div style="height: 100px;"></div>                                   <!-- [46] Fixed header offset buffer. -->
    <div class="back-btn-container">                                      <!-- [47] Navigation wrapper. -->
        <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; padding: 12px 30px; font-weight: 700;">← Back to Command Center</a> <!-- [48] link. -->
    </div>                                                               <!-- [49] Close wrap. -->

    <div class="view-container">                                         <!-- [50] Open sentiment dashboard card. -->
        <h2 style="color: var(--purple); margin-bottom: 5px;">⭐ Guest Satisfaction & Reviews</h2> <!-- [51] title. -->
        <p style="color: #718096; margin-bottom: 30px;">Analyzing passenger feedback and ratings submitted across all travel segments.</p> <!-- [52] description. -->

        <?php if(isset($_GET['msg'])): ?>                                <!-- [53] Open notification logic. -->
            <div style="background: #f0fff4; color: #22543d; padding: 15px; border-radius: 8px; border-left: 6px solid #38a169; margin-bottom: 20px; font-weight: 600;">✅ <?= htmlspecialchars($_GET['msg']) ?></div> <!-- [54] toast. -->
        <?php endif; ?>                                                   <!-- [55] close check. -->

        <table class="crud-table">                                       <!-- [56] Open review data grid. -->
            <thead><tr><th>Processed</th><th>Traveler Identity</th><th>Sentiment</th><th>Written Dossier</th><th>Trip Path</th><th>Control Deck</th></tr></thead> <!-- [57] headers. -->
            <tbody>                                                      <!-- [58] Records start. -->
                <?php $sql = "SELECT f.*, u.first_name, u.last_name, r.from_location, r.to_location FROM feedback f JOIN users u ON f.user_id = u.user_id JOIN routes r ON f.route_id = r.route_id ORDER BY f.feedback_date DESC"; // [59] Aggregate query logic.
                $result = $conn->query($sql); while($row = $result->fetch_assoc()): ?> <!-- [60] Iterate through feedback entries. -->
                <tr><td style="color: #718096; white-space: nowrap; font-size: 0.9rem;"><?= $row['feedback_date'] ?></td> <!-- [61] timestamp. -->
                    <td style="font-weight: 700; color: #2d3748;"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td> <!-- [62] identity. -->
                    <td style="color: #ecc94b; font-size: 1.1rem; letter-spacing: 2px;"><?= str_repeat("★", $row['rating']) ?></td> <!-- [63] star visualizer. -->
                    <td style="color: #4a5568; line-height: 1.6; max-width: 300px;"><?= htmlspecialchars($row['comments']) ?></td> <!-- [64] comment body. -->
                    <td style="font-size: 0.85rem; color: #718096; font-weight: 600;"><?= htmlspecialchars($row['from_location'] . ' → ' . $row['to_location']) ?></td> <!-- [65] route context. -->
                    <td><a href="?delete_feedback=<?= $row['feedback_id'] ?>" class="action-btn btn-delete" onclick="return confirm('SECURITY WARNING: Permanently DELETE this entry? Irreversible.')">Delete</a></td> <!-- [66] moderation cmd. -->
                </tr>                                                    <!-- [67] end row. -->
                <?php endwhile; ?>                                       <!-- [68] end iteration. -->
            </tbody>                                                     <!-- [69] end table body. -->
        </table>                                                         <!-- [70] end grid. -->
    </div>                                                               <!-- [71] end card. -->
    <div style="height: 120px;"></div>                                   <!-- [72] buffer. -->
    <script src="js/footer.js"></script>                                 <!-- [73] inject footer. -->
    <script src="js/table_manager.js"></script>
</body>                                                              <!-- [74] end body. -->
</html>                                                              <!-- [75] end document. -->
