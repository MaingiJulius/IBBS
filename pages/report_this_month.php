<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * AUDIT REPORT: MONTHLY BOOKING ANALYTICS (report_this_month.php)   // [4] Title identifying this script as the monthly sales audit.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script generates a detailed document of all passenger // [6] Main objective: mid-term financial planning.
 * bookings made within the current calendar month.                // [7] Task: aggregate ticketing data by month.
 * It is used for mid-term financial planning and sales tracking.    // [8] Goal: business sales tracking.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {      // [13] Access Control: Verify if requester possesses the 'ADMIN' credential.
    header("Location: login.html");                                  // [14] Security Redirect: Kick unauthorized visitors to the login gate.
    exit();                                                          // [15] Halt Execution: Ensure no fiscal metrics are leaked to guests.
}                                                                    // [16] Close security barrier.

$sql = "SELECT b.booking_id, b.booking_time, CONCAT(u.first_name, ' ', u.last_name) AS passenger_name, r.from_location, r.to_location, b.seat_number, b.booking_status FROM bookings b JOIN users u ON b.user_id = u.user_id JOIN routes r ON b.route_id = r.route_id WHERE MONTH(b.booking_time) = MONTH(CURDATE()) AND YEAR(b.booking_time) = YEAR(CURDATE()) ORDER BY b.booking_time DESC"; // [17] Analytical Query: Aggregates current month logs using MONTH() and YEAR() SQL filters.
$result = $conn->query($sql);                                        // [18] Execute the data retrieval command on the MySQL server.
?>                                                                   <!-- [19] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [20] Define standard HTML5 document type for modern browsers. -->
<html lang="en">                                                     <!-- [21] Root element identifying English as layout language. -->
<head>                                                               <!-- [22] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [23] Declare UTF-8 for international character support. -->
    <title>Monthly Performance Analysis - Wema Travellers</title>        <!-- [24] Website title for browser selection. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [25] Responsive scaling for mobile device compatibility. -->
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
    <div style="height: 100px;"></div>                                   <!-- [38] Fixed header offset buffer. -->

    <div class="report-container">                                       <!-- [39] Open analytical card. -->
        <div class="back-btn-container" style="text-align: left; margin-bottom: 30px;"><a href="admin_insights.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; display: inline-block; width: auto; padding: 12px 35px; font-weight: 700;">← Back to Insights Hub</a></div> <!-- [40] Exit. -->
        <h2 style="color: var(--purple); margin-bottom: 5px;">📅 Monthly Sales Audit</h2> <!-- [41] Title. -->
        <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 10px;">Aggregating every passenger transaction completed within the current calendar cycle.</p> <!-- [42] Desc. -->

        <table class="data-table">                                       <!-- [43] Start audit grid. -->
            <thead><tr><th>Ref ID</th><th>Stamp</th><th>Full Name</th><th>Journey Segment</th><th>Seat Map</th><th>Status Record</th></tr></thead> <!-- [44] head labels. -->
            <tbody>                                                      <!-- [45] Records start. -->
                <?php if ($result->num_rows > 0): while($row = $result->fetch_assoc()): ?> <!-- [46] Loop through monthly records. -->
                <tr><td><strong style="color: #94a3b8;"><?= $row['booking_id'] ?></strong></td> <!-- [47] ID cell. -->
                    <td style="font-family: 'Courier New', monospace; font-size: 0.85rem; color: #475569;"><?= $row['booking_time'] ?></td> <!-- [48] stamp. -->
                    <td style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($row['passenger_name']) ?></td> <!-- [49] identity. -->
                    <td style="color: #64748b;"><?= htmlspecialchars($row['from_location'] . ' → ' . $row['to_location']) ?></td> <!-- [50] journey path. -->
                    <td><span style="background: #f8fafc; color: #475569; padding: 5px 10px; border-radius: 6px; font-weight: 800; border: 1px solid #e2e8f0;"><?= $row['seat_number'] ?></span></td> <!-- [51] seat. -->
                    <td style="color: <?= ($row['booking_status'] == 'CANCELLED') ? '#ef4444' : '#10b981' ?>; font-weight: 900; text-transform: uppercase; font-size: 0.85rem;"><?= $row['booking_status'] ?></td> <!-- [52] status. -->
                </tr>                                                    <!-- [53] end row. -->
                <?php endwhile; else: ?>                                 <!-- [54] end loop. -->
                <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 100px;"><div style="font-size: 1.25rem; font-style: italic;">No ticketing data initialized for the current month cycle.</div></td></tr> <!-- [55] empty. -->
                <?php endif; ?>                                          <!-- [56] end check. -->
            </tbody>                                                     <!-- [57] end table body. -->
        </table>                                                         <!-- [58] end grid. -->
    </div>                                                               <!-- [59] end card. -->
    <div style="height: 120px;"></div>                                   <!-- [60] buffer. -->
    <script src="js/footer.js"></script>                                 <!-- [61] inject footer. -->
</body>                                                              <!-- [62] end body. -->
</html>                                                              <!-- [63] end document. -->
