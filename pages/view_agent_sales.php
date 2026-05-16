<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * ADMINISTRATION: AGENT PERFORMANCE AUDIT (view_agent_sales.php)    // [4] Title identifying this script as the agent performance audit.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script provides the Administrator with a performance // [6] Main objective: agent productivity leaderboard.
 * leaderboard, ranking Agents by their productivity and revenue generation. // [7] Functionality: financial performance auditing.
 * Features: Multi-table Sales Aggregation, fiscal ranking.           // [8] Goal: strategic performance evaluation.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ADMIN') {       // [13] Security Perimeter: Verify requester possesses the 'ADMIN' credential.
    die("Access Denied: High-Level Personnel Performance Data. Administrator Authorization Required for Audit."); // [14] Halt execution with descriptive error.
}                                                                    // [15] Close security barrier.
?>                                                                   <!-- [16] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [17] Define standard HTML5 document type for modern browsers. -->
<html lang="en">                                                     <!-- [18] Root element identifying English as layout language. -->
<head>                                                               <!-- [19] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [20] Declare UTF-8 for international character support. -->
    <title>Agent Productivity Report - Wema Travellers</title>          <!-- [21] Website title. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [22] Responsive scaling for mobile device compatibility. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [23] Load shared component style assets. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [24] Load global project branding variables. -->
</head>                                                              <!-- [32] Close head section. -->

<body class="<?= strtolower($_SESSION['role'] ?? 'admin') ?>-role"> <!-- [33] Start visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [34] Inject the unified administrative header. -->
    <div style="height: 100px;"></div>                                   <!-- [35] Fixed header offset buffer. -->

    <div class="view-container">                                         <!-- [39] Open analytical card. -->
        <div class="no-print" style="margin-bottom: 30px;">
            <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; padding: 12px 30px; font-weight: 700;">← Back to Main Hub</a>
            <!-- (less than sign) ! (exclamation mark) - (dash) - (dash) starts an HTML comment.
                 window (w i n d o w) is the browser object.
                 . (dot) access operator.
                 print (p r i n t) is the command to open the print dialog.
                 ( (bracket) ) (bracket) executes the command.
                 - (dash) - (dash) (greater than sign) ends the logic explanation. -->
            <button onclick="window.print()" class="button regular-button" style="text-decoration:none; background-color: #64748b; color: white; border-radius: 50px; border: none; padding: 12px 30px; margin-left: 10px; font-weight: 700; cursor: pointer;">Print Report</button>
        </div>
        <h2 class="no-print" style="color: var(--purple); margin-bottom: 5px;">📈 Agent Productivity Leaderboard</h2>
        <!-- class (label) = "no-print" (do not print) tells the machine to hide 
             this title on the final paper report. -->
        <p class="no-print" style="color: #718096; margin-bottom: 30px;">Auditing ticket sales volume and gross fiscal contribution per staff member.</p>
        <!-- class (label) = "no-print" (do not print) hides the performance analysis text. -->

        <table class="crud-table">                                       <!-- [42] Start performance grid. -->
            <thead><tr><th>Staff Identity (Agent)</th><th>Sales Volume (Tickets)</th><th>Fiscal Contribution (KES)</th></tr></thead> <!-- [43] Head. -->
            <tbody>                                                      <!-- [44] Records start. -->
                <?php                                                     // [45] Re-open PHP for analytical processing.
                $sql = "SELECT u.first_name, u.last_name, COUNT(b.booking_id) as total_bookings, SUM(r.cost) as total_sales FROM users u JOIN bookings b ON u.user_id = b.user_id JOIN routes r ON b.route_id = r.route_id WHERE u.role = 'AGENT' GROUP BY u.user_id ORDER BY total_sales DESC"; // [46] Performance Query: Joins 3 tables to calculate per-agent aggregates and rank by revenue.
                // mysqli_query (MySQL Improved query) procedural function.
                $result = mysqli_query($conn, $sql); 
                // mysqli_num_rows (MySQL Improved number of rows) procedural function.
                if (mysqli_num_rows($result) > 0) {
                    // mysqli_fetch_assoc (MySQL Improved fetch associative) procedural function.
                    while($row = mysqli_fetch_assoc($result)) {
                        /* while (while) starts a loop. $row (row container) pulls data. 
                           mysqli_fetch_assoc (fetch associative) converts raw data into labeled pieces. 
                           ( starts. $result (result source). ) ends. */
                        echo "<tr>";                                     // [50] Open record row.
                        echo "<td style='font-weight: 700; color: #1e293b;'>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>"; 
                        /* . (dot) connects strings. html (HyperText) special (special) chars (characters) 
                           is a security tool that encodes text. ( starts. $row (data row) ['first_name'] 
                           (label) . (connects) ' ' (space) . (connects) $row ['last_name'] (label) ) ends. */
                        echo "<td><span style='background: #fdf2f8; color: #be185d; padding: 4px 10px; border-radius: 4px; font-weight: 800; border: 1px solid #fbcfe8; font-size: 0.85rem;'>" . $row['total_bookings'] . " TICKETS</span></td>"; // [52] Volume badge.
                        echo "<td style='font-weight: 900; color: #2d3748; letter-spacing: 0.5px; font-family: monospace;'>" . number_format($row['total_sales'], 2) . " /-</td>"; // [53] Fiscal sum cell.
                        echo "</tr>";                                    // [54] Close row.
                    }                                                    // [55] End loop.
                } else {                                                 // [56] Handle empty personnel dataset.
                    echo "<tr><td colspan='3' style='text-align:center; padding: 60px; color: #a0aec0; font-style: italic;'>No agent performance data initialized. Analytics will populate as bookings occur.</td></tr>"; // [57] Result.
                }                                                        // [58] End check.
                ?>                                                       <!-- [59] Close PHP. -->
            </tbody>                                                     <!-- [60] End body. -->
        </table>                                                         <!-- [61] End grid. -->
    </div>                                                               <!-- [62] End card. -->
    <div style="height: 120px;"></div><script src="js/footer.js"></script>
    <script src="js/table_manager.js"></script>
</body>                                                              <!-- [64] End body. -->
</html>                                                              <!-- [65] End document. -->
