<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * BUSINESS INTELLIGENCE: ANNUAL BOOKING LEDGER (report_this_year.php) // [4] Title identifying this script as the annual performance audit.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script identifies and displays all bus tickets sold  // [6] Main objective: high-level annual performance review.
 * throughout the entire current calendar year.                      // [7] Task: aggregate ticketing data by year.
 * Impact: Used for high-level annual performance reviews and strategic planning. // [8] Goal: strategic business insights.
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
        WHERE YEAR(b.booking_time) = YEAR(CURDATE()) 
        ORDER BY b.booking_time DESC"; 
$result = $conn->query($sql);                                        
$total_revenue = 0;                                                  
?>                                                                   

<!DOCTYPE html>                                                         <!-- [20] Define standard HTML5 document type for modern browsers. -->
<html lang="en">                                                     <!-- [21] Root element identifying English as layout language. -->
<head>                                                               <!-- [22] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [23] Declare UTF-8 for international character support. -->
    <title>Annual Intelligence Report: Total Year Sales - Wema Travellers</title> <!-- [24] Website title. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [25] Responsive scaling for mobile device compatibility. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [26] Load global project branding variables. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [27] Load shared component style assets. -->
    <style>                                                          /* [28] Page-specific internal CSS architecture. */
        .report-container { width: 95%; max-width: 1200px; margin: 40px auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.04); } /* [29] main card. */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 35px; } /* [30] audit grid. */
        .data-table th, .data-table td { border-bottom: 1px solid #f1f5f9; padding: 18px; text-align: left; } /* [31] cell layout. */
        .data-table th { background-color: var(--purple); color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; } /* [32] branding. */
        .data-table tr:nth-child(even) { background-color: #fbfcfe; } /* [33] zebra striping. */
        .back-link { display: inline-block; margin-bottom: 30px; text-decoration: none; color: white; background-color: var(--purple); padding: 12px 35px; border-radius: 50px; font-weight: 700; transition: opacity 0.2s; } /* [34] cmd button. */
        .back-link:hover { opacity: 0.9; }                          /* [35] interaction. */
    </style>                                                         <!-- [36] Terminate internal CSS block. -->
</head>                                                              <!-- [37] Close head section. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>                                <!-- [39] Inject the unified administrative header. -->
    <div style="height: 100px;"></div>                                   <!-- [40] Fixed header offset buffer. -->

    <div class="report-container">                                       <!-- [41] Open analytical card. -->
        <a href="admin_insights.php" class="back-link">← Back to Insights Hub</a> <!-- [42] Exit cmd. -->
        <h2 style="color: var(--purple); margin-bottom: 5px;">📅 Annual Performance Audit</h2> <!-- [43] title. -->
        <p style="color: #64748b; margin-bottom: 10px; font-size: 1.05rem;">Aggregated ticketing activity recorded since Jan 1st of the current calendar year.</p> <!-- [44] desc. -->

        <table class="data-table">                                       <!-- [45] Start audit grid. -->
            <thead><tr><th>Ref ID</th><th>Stamp</th><th>Full Name</th><th>Travel Segment</th><th>Seat Map</th><th>Price</th><th>Log Status</th></tr></thead> 
            <tbody>                                                      <!-- [47] Records start. -->
                <?php if ($result->num_rows > 0): while($row = $result->fetch_assoc()): ?> <!-- [48] Loop through annual records. -->
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
                <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 100px;"><div style="font-size: 3rem; margin-bottom: 20px;">📉</div><div style="font-size: 1.25rem; font-style: italic;">No booking records detected for the current calendar year.</div></td></tr> <!-- [57] empty state. -->
                <?php endif; ?>                                          <!-- [58] end check. -->
            </tbody>                                                     <!-- [59] end table body. -->
        </table>                                                         <!-- [60] end grid. -->
    </div>                                                               <!-- [61] end card. -->
    <div style="height: 120px;"></div>                                   <!-- [62] buffer. -->
    <script src="js/footer.js"></script>                                 <!-- [63] inject footer. -->
</body>                                                              <!-- [64] end body. -->
</html>                                                              <!-- [65] end document. -->
