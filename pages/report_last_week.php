<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * AUDIT REPORT: LAST WEEK BOOKING LOGS (report_last_week.php)       // [4] Title identifying this script as the retrospective weekly audit.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: Provides a snapshot of all sales activity for the previous week. // [6] Main objective: historical performance auditing.
 * This is vital for historical performance auditing and comparing    // [7] Task: week-over-week growth analysis.
 * week-over-week growth.                                            // [8] Focus: comparative operational data.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {      // [13] Access Control: Verify if requester possesses the 'ADMIN' credential.
    header("Location: login.html");                                  // [14] Security Redirect: Kick unauthorized visitors to the login gate.
    exit();                                                          // [15] Halt Execution: Ensure no fiscal metrics are leaked to guests.
}                                                                    // [16] Close security barrier.

$sql = "SELECT b.booking_id, b.booking_time, CONCAT(u.first_name, ' ', u.last_name) AS passenger_name, r.from_location, r.to_location, b.seat_number, b.booking_status, r.cost 
        FROM bookings b 
        JOIN users u ON b.user_id = u.user_id 
        JOIN routes r ON b.route_id = r.route_id 
        WHERE YEARWEEK(b.booking_time, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1) 
        ORDER BY b.booking_time DESC"; 
$result = $conn->query($sql);                                        
$total_revenue = 0;                                                  
?>                                                                   

<!DOCTYPE html>                                                         <!-- [20] Define standard HTML5 document type. -->
<html lang="en">                                                     <!-- [21] Root element identifying English as layout language. -->
<head>                                                               <!-- [22] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [23] Declare UTF-8 for international character support. -->
    <title>Last Week's Intelligence Report - Wema Travellers</title>    <!-- [24] Website title for browser selection. -->
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

<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>                                <!-- [37] Inject the unified administrative header. -->
    <div style="height: 100px;"></div>                                   <!-- [38] Fixed header offset buffer. -->

    <div class="report-container">                                       <!-- [39] Open analytical card. -->
        <a href="admin_insights.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; display: inline-block; width: auto; padding: 12px 35px; margin-bottom: 30px; font-weight: 700;">← Back to Insights Hub</a> <!-- [40] Exit cmd. -->
        <h2 style="color: var(--purple); margin-bottom: 5px;">📉 Last Week's Performance Audit</h2> <!-- [41] title. -->
        <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 10px;">Aggregated ticket sales and commuter activity for the previous ISO-8601 week.</p> <!-- [42] desc. -->

        <table class="data-table">                                       <!-- [43] Start audit grid. -->
            <thead><tr><th>Ref ID</th><th>Stamp</th><th>Full Name</th><th>Travel Segment</th><th>Seat</th><th>Price</th><th>Log Status</th></tr></thead> 
            <tbody>                                                      <!-- [45] Records start. -->
                <?php if ($result->num_rows > 0): while($row = $result->fetch_assoc()): ?> <!-- [46] Conditional loop through records. -->
                <tr><td><strong style="color: #94a3b8;"><?= $row['booking_id'] ?></strong></td> 
                    <td style="font-family: 'Courier New', monospace; font-size: 0.85rem; color: #475569;"><?= $row['booking_time'] ?></td> 
                    <td style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($row['passenger_name']) ?></td> 
                    <td style="color: #64748b;"><?= htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) ?></td> 
                    <td><span style="background: #f8fafc; color: #475569; padding: 5px 10px; border-radius: 6px; font-weight: 800; border: 1px solid #e2e8f0;"><?= $row['seat_number'] ?></span></td> 
                    <td style="font-weight: 700; color: #0f172a;">$<?= number_format($row['cost'], 2) ?></td>
                    <td style="color: <?= ($row['booking_status'] == 'CANCELLED') ? '#ef4444' : '#10b981' ?>; font-weight: 900; text-transform: uppercase; font-size: 0.85rem;"><?= $row['booking_status'] ?></td> 
                </tr>                                                    
                <?php 
                    if($row['booking_status'] !== 'CANCELLED') { $total_revenue += $row['cost']; }
                endwhile; ?>
                <tr style="background-color: #f8fafc; font-size: 1.1rem; border-top: 2px solid var(--purple);">
                    <td colspan="5" style="text-align: right; font-weight: 800; color: var(--purple);">TOTAL REVENUE:</td>
                    <td colspan="2" style="font-weight: 900; color: #10b981; font-size: 1.3rem;">$<?= number_format($total_revenue, 2) ?></td>
                </tr>
                <?php else: ?>                                 
                <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 100px;"><div style="font-size: 1.25rem; font-style: italic;">No booking records found for the previous week instance.</div></td></tr> <!-- [55] empty state. -->
                <?php endif; ?>                                          <!-- [56] end check. -->
            </tbody>                                                     <!-- [57] end table body. -->
        </table>                                                         <!-- [58] end grid. -->
    </div>                                                               <!-- [59] end card. -->
    <div style="height: 120px;"></div>                                   <!-- [60] buffer. -->
    <script src="js/footer.js"></script>                                 <!-- [61] inject footer. -->
</body>                                                              <!-- [62] end body. -->
</html>                                                              <!-- [63] end document. -->
