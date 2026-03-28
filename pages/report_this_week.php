<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * AUDIT REPORT: WEEKLY BOOKING LOGS (report_this_week.php)          // [4] Title identifying this script as the weekly performance audit.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: Provides a snapshot of all sales activity for the current week. // [6] Main objective: audit revenue and monitor occupancy.
 * This is vital for auditing revenue and monitoring fleet occupancy  // [7] Goal: short-term logistical oversight.
 * on a short-term basis.                                            // [8] Focus: weekly operational trends.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {      // [13] Access Control: Verify if requester possesses the 'ADMIN' credential.
    header("Location: login.html");                                  // [14] Security Redirect: Kick unauthorized visitors to the login gate.
    exit();                                                          // [15] Halt Execution: Ensure no fiscal metrics are leaked to guests.
}                                                                    // [16] Close security barrier.

$sql = "SELECT b.booking_id, b.booking_time, CONCAT(u.first_name, ' ', u.last_name) AS passenger_name, r.from_location, r.to_location, b.seat_number, b.booking_status FROM bookings b JOIN users u ON b.user_id = u.user_id JOIN routes r ON b.route_id = r.route_id WHERE YEARWEEK(b.booking_time, 1) = YEARWEEK(CURDATE(), 1) ORDER BY b.booking_time DESC"; // [17] Analytical Query: Aggregates weekly logs using ISO-8601 week calculation.
$result = $conn->query($sql);                                        // [18] Execute the data retrieval command on the MySQL server.
?>                                                                   <!-- [19] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [20] Define standard HTML5 document type. -->
<html lang="en">                                                     <!-- [21] Root element identifying English as layout language. -->
<head>                                                               <!-- [22] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [23] Declare UTF-8 for international character support. -->
    <title>Weekly Intelligence Report - Wema Travellers</title>         <!-- [24] Website title for browser selection. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [25] Responsive scaling for mobile devices. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [26] Load global project branding variables. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [27] Load shared component style assets. -->
    <style>                                                          /* [28] Page-specific internal CSS architecture. */
        .report-container { width: 95%; max-width: 1200px; margin: 40px auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.04); } /* [29] main card. */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 35px; } /* [30] audit grid. */
        .data-table th, .data-table td { border-bottom: 1px solid #f1f5f9; padding: 18px; text-align: left; } /* [31] cell layout. */
        .data-table th { background-color: var(--purple); color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; } /* [32] branding. */
        .data-table tr:nth-child(even) { background-color: #fbfcfe; } /* [33] zebra striping. */
    </style>                                                         <!-- [34] Terminate internal CSS block. -->
</head>                                                              <!-- [35] Close head section. -->

<body>                                                               <!-- [36] Start visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [37] Inject the unified administrative header. -->
    <div style="height: 100px;"></div>                                   <!-- [38] Insert a structural spacer div with a fixed height of 100 pixels to prevent the main content from being obscured by the fixed-position navigation header. -->
    <div class="report-container">                                       <!-- [39] Open the primary div container styled as a white analytical card to encapsulate the weekly report data. -->
        <a href="admin_insights.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; display: inline-block; width: auto; padding: 12px 35px; margin-bottom: 30px; font-weight: 700;">← Back to Insights Hub</a> <!-- [40] Render a hyperlink button pointing back to the admin_insights.php dashboard, styled with a purple background and rounded borders. -->
        <h2 style="color: var(--purple); margin-bottom: 5px;">📈 Weekly Performance Audit</h2> <!-- [41] Render the primary section header using an h2 tag with a purple text color and a trend-up emoji. -->
        <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 10px;">Aggregated ticket sales and commuter activity for the current ISO-8601 week.</p> <!-- [42] Output a descriptive paragraph explaining that this report summarizes sales and activity for the current ISO-8601 week. -->

        <table class="data-table">                                       <!-- [43] Open the HTML table element used to display the analytical data grid. -->
            <thead><tr><th>Ref ID</th><th>Stamp</th><th>Full Name</th><th>Travel Segment</th><th>Seat</th><th>Log Status</th></tr></thead> <!-- [44] Define the table header row containing labels for Reference ID, Timestamp, Passenger Name, Travel Segment, Seat, and Log Status. -->
            <tbody>                                                      <!-- [45] Open the table body section where the dynamic weekly data rows will be injected. -->
                <?php if ($result->num_rows > 0): while($row = $result->fetch_assoc()): ?> <!-- [46] Initialize a PHP conditional check and a while loop to iterate through every record returned by the weekly SQL result set. -->
                <tr><td><strong style="color: #94a3b8;"><?= $row['booking_id'] ?></strong></td> <!-- [47] Output the unique booking identifier inside a styled bold tag. -->
                    <td style="font-family: 'Courier New', monospace; font-size: 0.85rem; color: #475569;"><?= $row['booking_time'] ?></td> <!-- [48] Output the exact creation timestamp of the booking using a monospace font. -->
                    <td style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($row['passenger_name']) ?></td> <!-- [49] Output the passenger's full name securely converted with htmlspecialchars to prevent XSS. -->
                    <td style="color: #64748b;"><?= htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) ?></td> <!-- [50] Output the journey segment combining origin and destination, sanitized against XSS. -->
                    <td><span style="background: #f8fafc; color: #475569; padding: 5px 10px; border-radius: 6px; font-weight: 800; border: 1px solid #e2e8f0;"><?= $row['seat_number'] ?></span></td> <!-- [51] Output the mapped seat number inside a custom-styled span block with a light background. -->
                    <td style="color: <?= ($row['booking_status'] == 'CANCELLED') ? '#ef4444' : '#10b981' ?>; font-weight: 900; text-transform: uppercase; font-size: 0.85rem;"><?= $row['booking_status'] ?></td> <!-- [52] Output the operational status using dynamic PHP logic to assign a red color for CANCELLED elements and green for active ones. -->
                </tr>                                                    <!-- [53] Close the HTML row element for the current iterated record. -->
                <?php endwhile; else: ?>                                 <!-- [54] Terminate the while loop and define the 'else' block which executes if zero booking records were found. -->
                <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 100px;"><div style="font-size: 1.25rem; font-style: italic;">No booking records initialized for the current week instance.</div></td></tr> <!-- [55] Output a formatted table row with a spanning cell indicating that no bookings were found for the current week. -->
                <?php endif; ?>                                          <!-- [56] Close the PHP conditional if/else structure for the weekly report. -->
            </tbody>                                                     <!-- [57] Close the HTML table body element which contains the rendered weekly rows. -->
        </table>                                                         <!-- [58] Close the HTML table element. -->
    </div>                                                               <!-- [59] Close the primary weekly report container div card. -->
    <div style="height: 120px;"></div>                                   <!-- [60] Insert a bottom spacer div to provide visual padding at the end of the scrollable page. -->
    <script src="js/footer.js"></script>                                 <!-- [61] Inject the external footer JavaScript file to render the site-wide footer. -->
</body>                                                              <!-- [62] Close the visible body section of the HTML document. -->
</html>                                                              <!-- [63] Formal termination of the HTML document structure. -->. -->
