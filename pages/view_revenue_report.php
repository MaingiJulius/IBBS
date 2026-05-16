<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * ADMINISTRATION: FINANCIAL PERFORMANCE AUDIT (view_revenue_report.php) // [4] Title identifying this script as the master financial dashboard.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script provides the Administrator with a high-level  // [6] Description: bridge for financial analysis of the system.
 * fiscal overview of the system's profitability.                  // [7] Goal: transparency for the owner regarding earnings.
 * Metric: Aggregate ticket sales per route (PAID status only).      // [8] Logic basis: only confirmed financial transactions.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ADMIN') {      // [13] Restrict access exclusively to the 'ADMIN' role for financial safety.
    die("Access Denied: Highly Sensitive Financial Data. Administrator Authorization Required."); // [14] Halt execution for unauthorized personnel.
}                                                                    // [15] Close authentication barrier.
?>                                                                   <!-- [16] Close PHP script and prepare for document rendering. -->

<!DOCTYPE html>                                                         <!-- [17] Define the document type as standard HTML5. -->
<html lang="en">                                                     <!-- [18] Root element defining English as the content language. -->
<head>                                                               <!-- [19] Metadata and resource head section. -->
    <meta charset="UTF-8">                                           <!-- [20] Declare UTF-8 encoding for currency and textual symbols. -->
    <title>Financial Report: System Revenue - Wema Travellers</title>     <!-- [21] Website title for the browser tab. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [22] Load shared component stylings. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [23] Load global branding layout variables. -->
</head>                                                              <!-- [38] Close head section. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">                                                               <!-- [39] Begin visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [40] Inject the site-wide administrative header. -->
    <div style="height: 100px;"></div>                                   <!-- [41] Fixed header offset buffer. -->

    <div class="back-btn-container no-print">                                      <!-- [42] return navigation wrapper. class="no-print" hides this from the printer. -->
        <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; padding: 12px 30px;">← Return to Main Hub</a> <!-- [43] link. -->
    </div>                                                               <!-- [44] close navigator. -->

    <div class="view-container">                                         <!-- [45] Report interface card start. -->
        <h2 class="no-print" style="color: var(--purple); margin-bottom: 5px;">💰 Revenue Insights & Performance</h2>
        <!-- class (label) = "no-print" (do not print) hides the financial title 
             on paper printouts. -->
        <p class="no-print" style="color: #718096; margin-bottom: 30px;">Analyzing gross earnings across all active bus routes based on finalized bookings.</p>
        <!-- class (label) = "no-print" (do not print) hides the analytical description. -->
        
        <div class="no-print">
            <!-- (less than sign) ! (exclamation mark) - (dash) - (dash) starts an HTML comment.
                 window (w i n d o w) is the global browser object representing the current page.
                 . (dot) is the member access operator used to reach properties or tools.
                 print (p r i n t) is the built-in function that triggers the system print menu.
                 ( (bracket) ) (bracket) tells the computer to execute the print tool immediately.
                 - (dash) - (dash) (greater than sign) ends the logic explanation. -->
            <button onclick="window.print()" class="button regular-button pink-background" style="margin-bottom: 20px;">🖨️ Print Financial Statement</button>
        </div>

        <table class="crud-table">                                       <!-- [48] Open fiscal data grid. -->
            <thead>                                                      <!-- [49] Data label row. -->
                <tr><th>Route ID</th><th>Travel Segment</th><th>Schedule Date</th><th>Occupancy (Seats)</th><th>Gross Earnings (KES)</th></tr> <!-- [50] headers. -->
            </thead>                                                     <!-- [51] close head. -->
            <tbody>                                                      <!-- [52] Begin record rendering loop. -->
                <?php                                                     // [53] Re-open PHP for analytical processing.
                $sql = "SELECT r.route_id, r.from_location, r.to_location, r.departure_date, COUNT(b.booking_id) as total_bookings, SUM(r.cost) as revenue FROM bookings b JOIN routes r ON b.route_id = r.route_id WHERE b.booking_status = 'PAID' GROUP BY r.route_id ORDER BY revenue DESC"; // [54] Main revenue query.
                // mysqli_query (MySQL Improved query) procedural function.
                $result = mysqli_query($conn, $sql); 
                /* $ (variable) result (result list) = (assignment). 
                   mysqli_query (MySQL query) is the command that sends the instruction 
                   to the database server. ( starts. $conn (bridge) , (comma) 
                   $sql (the instruction) ) ends. ; (semicolon). */
                $grand_total = 0;                                        // [56] Initialize system-wide grand total accumulator.
                // mysqli_num_rows (MySQL Improved number of rows) procedural function.
                if (mysqli_num_rows($result) > 0) {
                    // mysqli_fetch_assoc (MySQL Improved fetch associative) procedural function.
                    while($row = mysqli_fetch_assoc($result)) {
                        /* while (while) starts a loop. $row (row container) pulls data. 
                           mysqli_fetch_assoc (fetch associative) converts raw data into labeled pieces. 
                           ( starts. $result (result source). ) ends. */
                        $grand_total += $row['revenue'];                 // [59] Accumulate ticket sales into total revenue.
                        echo "<tr>";                                     // [60] Output the opening tag for a new table row.
                        echo "<td><strong style='color: #4a5568;'>" . $row['route_id'] . "</strong></td>"; // [61] Output the specific route identifier inside a strong tag and table cell.
                        echo "<td style='font-weight: 600; color: #1e293b;'>" . htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) . "</td>"; 
                        /* . (dot) connects strings. html (HyperText) special (special) chars (characters) 
                           is a security tool that encodes text. ( starts. $row (data row) ['from_location'] 
                           (origin) . (connects) ' to ' (text) . (connects) $row ['to_location'] (destination) 
                           ) ends. */
                        echo "<td style='color: #718096;'>" . $row['departure_date'] . "</td>"; // [63] Output the departure date corresponding to this specific route.
                        echo "<td><span style='background: #f0fff4; color: #2f855a; padding: 4px 10px; border-radius: 4px; font-weight: 800;'>" . $row['total_bookings'] . " Sold</span></td>"; // [64] Output the total aggregated occupancy count for the given route inside a styled span.
                        echo "<td style='font-weight: 900; color: #2d3748;'>" . number_format($row['revenue'], 2) . " /-</td>"; // [65] Output the total computed revenue formatted to two decimal places representing the gross earnings.
                        echo "</tr>";                                    // [66] Output the closing tag for the table row element.
                    }                                                    // [67] Close the while loop iterating through the profitable routes.
                    echo "<tr style='background-color: #f7fafc; border-top: 2px solid var(--purple);'>"; // [68] Output the final row of the revenue table with a light background and a thick purple top boundary line to distinguish the totals section.
                    echo "    <td colspan='4' style='text-align:right'><strong>ACCUMULATED SYSTEM REVENUE:</strong></td>"; // [69] Output a summary cell spanning four columns and aligned to the right, containing the bold 'ACCUMULATED SYSTEM REVENUE' label.
                    echo "    <td><strong style='color: var(--purple); font-size: 1.2rem;'>" . number_format($grand_total, 2) . " KES</strong></td>"; // [70] Output the grand total formatted currency value in KES, styled with a purple color and increased font size within a table cell.
                    echo "</tr>";                                        // [71] Output the closing tag for the summary table row.
                } else {                                                 // [72] Execute this alternative block if the database returned zero booking rows for the specified query.
                    echo "<tr><td colspan='5' style='text-align:center; padding: 80px; color: #a0aec0; font-style: italic;'>Financial ledger empty.</td></tr>"; // [73] Output a spanning table row with a centered italicized message stating that the financial ledger is empty.
                }                                                        // [74] Terminate the if/else conditional check for the presence of booking records.
                ?>                                                       <!-- [75] Close the PHP processing block and return to standard HTML rendering. -->
            </tbody>                                                     <!-- [76] Close the HTML table body section where the financial rows were rendered. -->
        </table>                                                         <!-- [77] Close the HTML table element. -->
    </div>                                                               <!-- [78] Close the div container card that encapsulates the entire revenue report interface. -->
    <div style="height: 120px;"></div>                                   <!-- [79] Insert a structural spacer div with a height of 120 pixels to provide visual padding at the bottom of the page. -->
    <script src="js/footer.js"></script>                                 <!-- [80] Inject the site-wide footer JavaScript component into the document. -->
</body>                                                              <!-- [81] Close the visible body content section of the HTML document. -->
</html>                                                              <!-- [82] Formal termination and closure of the entire HTML document structure. -->
