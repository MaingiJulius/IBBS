<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * ADMINISTRATION: ROUTE & SCHEDULE MANAGEMENT (view_routes.php)     // [4] Title identifying this script as the logistics command hub.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script serves as the command center for bus logistics. // [6] Main objective: manage the platform's geographical reach.
 * Features: Route creation, inventory listing, Country-based grouping. // [7] Key components: inventory control and data visualization.
 * ================================================================= // [8] Visual header for administrative documentation clarity.
 */                                                                  // [9] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [10] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [11] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) { // [12] Restrict access to authenticated STAFF (Admin/Agent) only.
    header("Location: login.html");                                  // [13] Redirect unauthorized guests to the security portal.
    exit();                                                          // [14] Halt execution to protect logistics data records.
}                                                                    // [15] Close security barrier.

if (isset($_GET['delete_route'])) {                                  // [16] Action Handle: Detect a deletion request via URL parameter.
    $route_id = $_GET['delete_route'];                               // [17] Map the targeted route ID to a local variable.
    $stmt = $conn->prepare("DELETE FROM routes WHERE route_id = ?"); // [18] Prepare a secure SQL template for record removal.
    $stmt->bind_param("i", $route_id);                               // [19] Safely inject the ID integer into the query template.
    $stmt->execute();                                                // [20] Commit the destructive command to the database engine.
    $stmt->close();                                                  // [21] Release database resources.
    header("Location: view_routes.php?msg=Success: The travel route has been permanently removed."); // [22] Redirect with confirmation message.
    exit();                                                          // [23] Halt logic flow.
}                                                                    // [24] Close deletion block.

if (isset($_POST['add_route'])) {                                    // [25] Action Handle: Detect a new route submission via form POST.
    $from = $_POST['from_location'];                                 // [26] Map origin data.
    $to = $_POST['to_location'];                                     // [27] Map destination data.
    $date = $_POST['departure_date'];                                // [28] Map travel date.
    $time = $_POST['departure_time'];                                // [29] Map clock time.
    $cost = $_POST['cost'];                                          // [30] Map ticket price.
    $bus_id = $_POST['bus_id'];                                      // [31] Map physical bus assignment.
    $stmt = $conn->prepare("INSERT INTO routes (from_location, to_location, departure_date, departure_time, cost, bus_id) VALUES (?, ?, ?, ?, ?, ?)"); // [32] Prepare secure insertion template.
    $stmt->bind_param("ssssdi", $from, $to, $date, $time, $cost, $bus_id); // [33] Bind all 6 parameters (Strings and Decimals).
    $stmt->execute();                                                // [34] Finalize the new record creation in MySQL.
    $stmt->close();                                                  // [35] Release statement resources.
    header("Location: view_routes.php?msg=Success: New travel segment has been initialized."); // [36] Redirect with success toast.
    exit();                                                          // [37] Halt logic flow.
}                                                                    // [38] Close creation block.
?>                                                                   <!-- [39] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [40] Define standard HTML5 document type. -->
<html lang="en">                                                     <!-- [41] Root element identifying English as the layout language. -->
<head>                                                               <!-- [42] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [43] Declare UTF-8 for international character support. -->
    <title>Schedules & Inventory - Wema Travellers</title>                 <!-- [44] Website title for browser identification. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [45] Responsive scaling for mobile devices. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [46] Load shared component style assets. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [47] Load global project branding variables. -->
    <style>                                                          /* [48] Page-specific internal CSS architecture. */
        .view-container { max-width: 1200px; margin: 30px auto; padding: 40px; background: #ffffff; border-radius: 12px; box-shadow: 0 15px 40px rgba(0,0,0,0.06); } /* [49] Main card. */
        .back-btn-container { padding: 20px; max-width: 1200px; margin: 0 auto; } /* [50] layout buffer. */
        .crud-table { width: 100%; border-collapse: collapse; margin-top: 30px; } /* [51] analytics grid. */
        .crud-table th, .crud-table td { padding: 16px; border-bottom: 1px solid #edf2f7; text-align: left; } /* [52] cell padding. */
        .crud-table th { background-color: var(--purple); color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem; } /* [53] branding. */
        .action-btn { padding: 8px 15px; border-radius: 8px; text-decoration: none; color: #ffffff; font-size: 0.85rem; font-weight: 600; display: inline-block; } /* [54] cmd. */
        .btn-delete { background-color: #ef4444; } /* [55] destructive alert. */
        .add-form { background: #f8fafc; padding: 35px; border-radius: 12px; margin-bottom: 40px; border: 1px solid #e2e8f0; } /* [56] form card. */
        .add-form h3 { margin-top: 0; color: var(--purple); margin-bottom: 25px; } /* [57] header. */
        .form-row { display: flex; gap: 20px; flex-wrap: wrap; } /* [58] row layout. */
        .form-group { flex: 1; min-width: 220px; } /* [59] group layout. */
        .form-group label { display: block; margin-bottom: 8px; font-weight: 700; font-size: 0.8rem; color: #4a5568; text-transform: uppercase; } /* [60] labels. */
    </style>                                                         <!-- [61] Terminate internal CSS block. -->
</head>                                                              <!-- [62] Close head section. -->

<body>                                                               <!-- [63] Start visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [64] Inject the unified administrative header. -->
    <div style="height: 100px;"></div>                                   <!-- [65] Fixed header offset buffer. -->
    <div class="back-btn-container">                                      <!-- [66] Navigation wrapper. -->
        <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; padding: 12px 30px; font-weight: 700;">← Back to Command Center</a> <!-- [67] link. -->
    </div>                                                               <!-- [68] Close wrap. -->

    <div class="view-container">                                         <!-- [69] Open dashboard card. -->
        <div class="welcome-banner" style="background: linear-gradient(135deg, var(--purple), var(--pink)); color: #ffffff; padding: 25px; border-radius: 12px; margin-bottom: 30px; text-align: center;"> <!-- [70] banner. -->
            <h2 style="margin:0; font-weight: 900;">🗺️ Travel Route Management Hub</h2> <!-- [71] title. -->
            <p style="margin:10px 0 0; font-size: 0.95rem; opacity: 0.9;">Staff Identity: <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></p> <!-- [72] ID. -->
        </div>                                                           <!-- [73] end banner. -->

        <?php if(isset($_GET['msg'])): ?>                                <!-- [74] Open notification logic. -->
            <div style="background: #f0fff4; color: #22543d; padding: 15px; border-radius: 8px; border-left: 6px solid #38a169; margin-bottom: 25px; font-weight: 600;">✅ <?= htmlspecialchars($_GET['msg']) ?></div> <!-- [75] toast. -->
        <?php endif; ?>                                                   <!-- [76] close check. -->

        <div class="add-form">                                           <!-- [77] Start route creation form. -->
            <h3>🆕 Initialize New travel path</h3>                        <!-- [78] heading. -->
            <form method="POST">                                         <!-- [79] Start form POST submission. -->
                <div class="form-row">                                   <!-- [80] start grid row. -->
                    <div class="form-group"><label>Departure City/Country</label><input type="text" name="from_location" class="input" required placeholder="Kisumu, Kenya"></div> <!-- [81] from. -->
                    <div class="form-group"><label>Arrival Endpoint</label><input type="text" name="to_location" class="input" required placeholder="Kampala, Uganda"></div> <!-- [82] to. -->
                    <div class="form-group"><label>Calendar date</label><input type="date" name="departure_date" class="input" required></div> <!-- [83] date. -->
                    <div class="form-group"><label>Departure time</label><input type="time" name="departure_time" class="input" required></div> <!-- [84] time. -->
                    <div class="form-group"><label>Ticket Price (KES)</label><input type="number" name="cost" class="input" required step="0.01"></div> <!-- [85] cost. -->
                    <div class="form-group"><label>Fleet Assignment</label><select name="bus_id" class="input" required style="cursor: pointer;"> <!-- [86] fleet dropdown. -->
                        <?php $buses = $conn->query("SELECT bus_id, bus_name FROM buses ORDER BY bus_name ASC"); while($b = $buses->fetch_assoc()) { echo "<option value='{$b['bus_id']}'>{$b['bus_name']} (Fleet {$b['bus_id']})</option>"; } ?> <!-- [87] populate buses. -->
                    </select></div>                                      <!-- [88] close select. -->
                </div>                                                   <!-- [89] end grid row. -->
                <button type="submit" name="add_route" class="button regular-button pink-background" style="margin-top:25px; padding:12px 40px; font-weight:800;">COMMIT travel Path</button> <!-- [90] submit. -->
            </form>                                                      <!-- [91] end form. -->
        </div>                                                           <!-- [92] end form area. -->

        <table class="crud-table">                                       <!-- [93] Open schedule inventory grid. -->
            <thead><tr><th>Ref ID</th><th>Source</th><th>Destination</th><th>Travel Date</th><th>Time</th><th>Fleet</th><th>Pricing</th><th>Moderation</th></tr></thead> <!-- [94] head labels. -->
            <tbody>                                                      <!-- [95] Records start. -->
                <?php $sql = "SELECT r.*, b.bus_name, SUBSTRING_INDEX(r.from_location, ', ', -1) as country FROM routes r JOIN buses b ON r.bus_id = b.bus_id ORDER BY country ASC, r.from_location ASC, r.departure_date ASC"; // [96] analytic grouping query.
                $result = $conn->query($sql); $current_country = "";     // [97] Fetch and initialize country tracker.
                while($row = $result->fetch_assoc()):                    // [98] Iterate through transport records.
                    if ($row['country'] != $current_country) {           // [99] check for geographic region shift.
                        $current_country = $row['country'];              // [100] update current region.
                        echo "<tr><td colspan='8' style='background:#f1f5f9; font-weight:800; color:#475569; font-size:0.75rem; text-transform:uppercase; letter-spacing:1.5px; border-left:6px solid var(--purple); padding:12px 18px;'>🌍 Geographic Region: " . htmlspecialchars($current_country) . "</td></tr>"; // [101] separator.
                    } ?>                                                 <!-- [102] resume HTML rendering. -->
                <tr><td><strong style="color: #94a3b8;"><?= $row['route_id'] ?></strong></td> <!-- [103] ID cell. -->
                    <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($row['from_location']) ?></td> <!-- [104] origin. -->
                    <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($row['to_location']) ?></td> <!-- [105] endpoint. -->
                    <td style="color: #4a5568;"><?= $row['departure_date'] ?></td> <!-- [106] date. -->
                    <td style="font-family: monospace; font-weight: 700;"><?= $row['departure_time'] ?></td> <!-- [107] time. -->
                    <td><span style="background:#faf5ff; color:#6b46c1; padding:4px 10px; border-radius:4px; font-weight:800; border:1px solid #e9d8fd;"><?= htmlspecialchars($row['bus_name']) ?></span></td> <!-- [108] fleet. -->
                    <td style="font-weight:900; color:#2d3748;"><?= number_format($row['cost'], 2) ?> KES</td> <!-- [109] price. -->
                    <td style="white-space: nowrap;"><a href="edit_route.php?id=<?= $row['route_id'] ?>" class="action-btn" style="background-color:#3182ce; margin-right:8px;">Modify</a> <!-- [110] edit. -->
                        <a href="?delete_route=<?= $row['route_id'] ?>" class="action-btn btn-delete" onclick="return confirm('FINAL SECURITY WARNING: Delete ID <?= $row['route_id'] ?>? IRREVERSIBLE.')">Delete</a></td> <!-- [111] delete. -->
                </tr>                                                    <!-- [112] end row. -->
                <?php endwhile; ?>                                       <!-- [113] end record iteration. -->
            </tbody>                                                     <!-- [114] end table body. -->
        </table>                                                         <!-- [115] end grid. -->
    </div>                                                               <!-- [116] end card. -->
    <div style="height: 120px;"></div>                                   <!-- [117] bottom spacer. -->
    <script src="js/footer.js"></script>                                 <!-- [118] inject footer. -->
</body>                                                              <!-- [119] end body. -->
</html>                                                              <!-- [120] end document. -->
