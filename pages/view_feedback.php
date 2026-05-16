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
    $stmt->bind_param("i", $fid);                                    /* bind (bind) _ (underscore) param (parameter) is the tool that securely 
                                                                        injects the data into the query blueprint. ( starts. "i" (integer number) 
                                                                        , (comma) $fid (target ID data) ) ends. ; (semicolon). */
    
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
</head>                                                              <!-- [43] Close head section. -->

<body class="<?= strtolower($_SESSION['role'] ?? 'staff') ?>-role"> <!-- [44] Start visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [45] Inject the unified administrative header. -->
    <div style="height: 100px;"></div>                                   <!-- [46] Fixed header offset buffer. -->

    <div class="view-container">                                         <!-- [50] Open sentiment dashboard card. -->
        <div class="no-print" style="margin-bottom: 30px;">
            <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; padding: 12px 30px; font-weight: 700;">← Back to Command Center</a>
            <!-- (less than sign) ! (exclamation mark) - (dash) - (dash) starts an HTML comment.
                 window (w i n d o w) is the browser object.
                 . (dot) access operator.
                 print (p r i n t) is the command to open the print dialog.
                 ( (bracket) ) (bracket) executes the command.
                 - (dash) - (dash) (greater than sign) ends the logic explanation. -->
            <button onclick="window.print()" class="button regular-button" style="text-decoration:none; background-color: #64748b; color: white; border-radius: 50px; border: none; padding: 12px 30px; margin-left: 10px; font-weight: 700; cursor: pointer;">Print Feedback Report</button>
        </div>
        <h2 class="no-print" style="color: var(--purple); margin-bottom: 5px;">⭐ Guest Satisfaction & Reviews</h2>
        <!-- class (label) = "no-print" (do not print) is a special instruction 
             that tells the printer to skip this header to keep the report clean. -->
        <p class="no-print" style="color: #718096; margin-bottom: 30px;">Analyzing passenger feedback and ratings submitted across all travel segments.</p>
        <!-- class (label) = "no-print" (do not print) ensures this description 
             stays hidden on paper. -->

        <?php if(isset($_GET['msg'])): ?>                                <!-- [53] Open notification logic. -->
            <div class="no-print" style="background: #f0fff4; color: #22543d; padding: 15px; border-radius: 8px; border-left: 6px solid #38a169; margin-bottom: 20px; font-weight: 600;">✅ <?= htmlspecialchars($_GET['msg']) ?></div> <!-- [54] toast. -->
        <?php endif; ?>                                                   <!-- [55] close check. -->

        <table class="crud-table">                                       <!-- [56] Open review data grid. -->
            <thead><tr><th>Processed</th><th>Traveler Identity</th><th>Sentiment</th><th>Written Dossier</th><th>Trip Path</th><th class="no-print">Control Deck</th></tr></thead> <!-- [57] headers. -->
            <tbody>                                                      <!-- [58] Records start. -->
                <?php $sql = "SELECT f.*, u.first_name, u.last_name, r.from_location, r.to_location FROM feedback f JOIN users u ON f.user_id = u.user_id JOIN routes r ON f.route_id = r.route_id ORDER BY f.feedback_date DESC"; // [59] Aggregate query logic.
                // mysqli_query (MySQL Improved query) procedural function.
                $result = mysqli_query($conn, $sql); 
                /* $ (variable) result (result list) = (assignment). 
                   mysqli_query (MySQL query) is the command that sends the instruction 
                   to the database server. ( starts. $conn (bridge) , (comma) 
                   $sql (the instruction) ) ends. ; (semicolon). */
                // mysqli_fetch_assoc (MySQL Improved fetch associative) procedural function.
                while($row = mysqli_fetch_assoc($result)): ?> <!-- while (while) starts a loop. $row (row container) pulls data. 
                                                                     mysqli_fetch_assoc (fetch associative) converts raw data into labeled pieces. 
                                                                     ( starts. $result (result source). ) ends. : (colon) starts the loop block. -->
                <tr><td style="color: #718096; white-space: nowrap; font-size: 0.9rem;"><?= $row['feedback_date'] ?></td> <!-- [61] timestamp. -->
                    <td style="font-weight: 700; color: #2d3748;"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td> <!-- html (HyperText) special (special) chars (characters) is a security tool 
                                                                                                                                                 that encodes text for safety. ( starts the tool. $row (row data) ['first_name'] 
                                                                                                                                                 (data piece) . (connects) ' ' (space) . (connects) $row ['last_name'] (family name) 
                                                                                                                                                 ) ends the tool. -->
                    <td style="color: #ecc94b; font-size: 1.1rem; letter-spacing: 2px;"><?= str_repeat("★", $row['rating']) ?></td> <!-- [63] star visualizer. -->
                    <td style="color: #4a5568; line-height: 1.6; max-width: 300px;"><?= htmlspecialchars($row['comments']) ?></td> <!-- htmlspecialchars (security tool) ( $row ['comments'] (user review data) ) -->
                    <td style="font-size: 0.85rem; color: #718096; font-weight: 600;"><?= htmlspecialchars($row['from_location'] . ' → ' . $row['to_location']) ?></td> <!-- htmlspecialchars (security tool) ( $row ['from_location'] . ' → ' . $row ['to_location'] ) -->
                    <td class="no-print"><a href="?delete_feedback=<?= $row['feedback_id'] ?>" class="action-btn btn-delete" onclick="return confirm('SECURITY WARNING: Permanently DELETE this entry? Irreversible.')">Delete</a></td> <!-- [66] moderation cmd. -->
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
