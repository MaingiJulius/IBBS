<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * REPORTING: BUS OCCUPANCY ANALYTICS (view_bus_occupancy.php)       // [4] Title identifying this script as the occupancy analytics engine.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * This interface provides a real-time monitor of seat sales.       // [6] Main objective: real-time sales monitoring.
 * It identifies how "full" each bus is, helping the management      // [7] Task: evaluate vehicle sales efficiency.
 * decide if extra buses are needed for popular routes.              // [8] Goal: operational decision support.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume user session to identify the operative.

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'ADMIN' && $_SESSION['role'] != 'AGENT')) { // [13] Security Firewall: Verify requester is STAFF (ADMIN or AGENT).
    die("Security Restriction: You do not have permission to view internal occupancy reports."); // [14] Halt execution with descriptive error.
}                                                                    // [15] Close security barrier.
?>                                                                   <!-- [16] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [17] Define standard HTML5 document type for modern browsers. -->
<html lang="en">                                                     <!-- [18] Root element identifying English as layout language. -->
<head>                                                               <!-- [19] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [20] Declare UTF-8 for international character support. -->
    <title>Occupancy Analytics - Wema Travellers</title>                <!-- [21] Website title. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [22] Responsive scaling for mobile device compatibility. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [23] Load shared component style assets. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [24] Load global project branding variables. -->
    <style>                                                          /* [25] Page-specific internal CSS architecture. */
        .view-container { max-width: 1200px; margin: 30px auto; padding: 40px; background: #ffffff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); } /* [26] Main card. */
        .back-btn-container { padding: 20px; max-width: 1200px; margin: 0 auto; } /* [27] Nav layout. */
        .crud-table { width: 100%; border-collapse: collapse; margin-top: 30px; } /* [28] Analytical grid. */
        .crud-table th, .crud-table td { padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: left; } /* [29] Cell styles. */
        .crud-table th { background-color: var(--purple); color: #ffffff; font-weight: 700; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; } /* [30] branding. */
    </style>                                                         <!-- [31] Terminate internal CSS block. -->
</head>                                                              <!-- [32] Close head section. -->

<body>                                                               <!-- [33] Start visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [34] Inject the unified administrative header. -->
    <div style="height: 100px;"></div>                                   <!-- [35] Fixed header offset buffer. -->

    <div class="back-btn-container">                                     <!-- [36] Open exit command container. -->
        <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; padding: 12px 30px; font-weight: 700;">← Back to Main Dashboard</a> <!-- [37] Return link. -->
    </div>                                                               <!-- [38] End exit container. -->

    <div class="view-container">                                         <!-- [39] Open analytical card. -->
        <h2 style="color: var(--purple); margin-bottom: 5px;">📊 Fleet Occupancy Audit</h2> <!-- [40] Primary header. -->
        <p style="color: #64748b; margin-bottom: 35px;">Real-time analysis of seat distribution and sales efficiency across active routes.</p> <!-- [41] Sub-header. -->

        <table class="crud-table">                                       <!-- [42] Start fleet grid. -->
            <thead><tr><th>Vehicle Model</th><th>Plate ID</th><th>Travel Date</th><th>Max Cap.</th><th>Booked</th><th>Available</th><th>Occupancy %</th></tr></thead> <!-- [43] Head. -->
            <tbody>                                                      <!-- [44] Records start. -->
                <?php                                                     // [45] Re-open PHP for logistics processing.
                $sql = "SELECT bs.bus_name, bs.reg_no, bs.max_passengers, r.departure_date, COUNT(b.booking_id) as booked_seats FROM buses bs JOIN routes r ON bs.bus_id = r.bus_id LEFT JOIN bookings b ON r.route_id = b.route_id GROUP BY bs.bus_id, r.route_id, r.departure_date ORDER BY r.departure_date DESC"; // [46] Occupancy Query: Joins 3 tables to calculate per-trip sales volume and seat distribution.
                $result = $conn->query($sql);                            // [47] Execute calculation on database.
                while($row = $result->fetch_assoc()):                    // [48] Step through the operational trip stack.
                    $max = ($row['max_passengers'] > 0) ? $row['max_passengers'] : 1; // [49] Zero-division safety logic.
                    $occupancy_percent = ($row['booked_seats'] / $max) * 100; // [50] Business Logic: Calculate sales percentage.
                    $status_color = ($occupancy_percent > 85) ? '#ef4444' : (($occupancy_percent > 50) ? '#f59e0b' : '#38a169'); // [51] UI Logic: Map load factor to colors.
                ?>                                                       <!-- [52] Open HTML row rendering. -->
                <tr><td><strong style="color: #1e293b;"><?= htmlspecialchars($row['bus_name']) ?></strong></td> <!-- [53] Identity. -->
                    <td style="font-family: 'Courier New', monospace; color: #475569; font-weight: 600;"><?= htmlspecialchars($row['reg_no']) ?></td> <!-- [54] Identifier. -->
                    <td style="color: #64748b; font-weight: 600;"><?= $row['departure_date'] ?></td> <!-- [55] timing. -->
                    <td style="font-weight: 600; color: #1e293b;"><?= $row['max_passengers'] ?> Seats</td> <!-- [56] capacity. -->
                    <td style="color: var(--purple); font-weight: 900;"><?= $row['booked_seats'] ?> Sold</td> <!-- [57] sales current. -->
                    <td style="color: #94a3b8; font-weight: 500;"><?= ($row['max_passengers'] - $row['booked_seats']) ?> Free</td> <!-- [58] inventory. -->
                    <td><div style="display:flex; align-items:center; gap:8px;"><span style="background: <?= $status_color ?>10; color: <?= $status_color ?>; padding: 6px 14px; border-radius: 8px; font-weight: 900; font-size: 0.85rem; border: 1px solid <?= $status_color ?>30; text-transform: uppercase;"><?= number_format($occupancy_percent, 1) ?>% Load</span></div></td> <!-- [59] Visual Load status. -->
                </tr>                                                    <!-- [60] close row. -->
                <?php endwhile; ?>                                       <!-- [61] end loop. -->
            </tbody>                                                     <!-- [62] end body. -->
        </table>                                                         <!-- [63] end grid. -->
    </div>                                                               <!-- [64] end card. -->
    <div style="height: 120px;"></div><script src="js/footer.js"></script> <!-- [65] Fixed offset and footer injection. -->
</body>                                                              <!-- [66] end body. -->
</html>                                                              <!-- [67] end document. -->
