<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * BUS FLEET MANAGEMENT (admin_buses_report.php)                     // [3] Title identifying this script as the fleet control center.
 * Purpose: This page serves as the control center for Wema Travellers' physical assets. // [4] Main objective: asset life-cycle management.
 * Admins use this to:                                               // [5] Functionality: operational administrative tasks.
 * 1. Register new buses (Registration numbers, Names, Seat Limits).  // [6] Task: vehicle registration and capacity definition.
 * 2. View the current fleet list.                                   // [7] Task: fleet inventory oversight.
 * 3. Assign/Change drivers for specific buses.                      // [8] Task: staff/asset mapping and operational assignments.
 * 4. Remove buses from the system (provided they have no active routes). // [9] Task: fleet decommissioning with integrity checks.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {      // [13] Security Barrier: Verify requester identity and 'ADMIN' credential.
    die("Security Error: Access denied. Fleet management requires Administrative privileges."); // [14] Halt execution with descriptive error.
}                                                                    // [15] Close security barrier.

if (isset($_POST['add_bus'])) {                                      // [16] Action Handler: Intercept form submission for new vehicle registration.
    $reg_no         = $_POST['reg_no'];                              // [17] Capture: Official government identifier (Plate Number).
    $bus_name       = $_POST['bus_name'];                            // [18] Capture: Marketing/Operational name of the vehicle.
    $max_passengers = $_POST['max_passengers'];                      // [19] Capture: Legal seating capacity of the unit.
    $stmt_add = $conn->prepare("INSERT INTO buses (reg_no, bus_name, max_passengers) VALUES (?, ?, ?)"); // [20] Prepare secure SQL insertion statement.
    $stmt_add->bind_param("ssi", $reg_no, $bus_name, $max_passengers);  // [21] Bind data variables to statement placeholders.
    $stmt_add->execute();                                            // [22] Commit new vehicle record to the MySQL engine.
    $stmt_add->close();                                              // [23] Release statement resource memory.
    header('Location: admin_ buses_report.php?msg=System: New bus record created successfully.'); // [24] Refresh page with success notification.
    exit();                                                          // [25] Terminate script post-redirect.
}                                                                    // [26] End registration block.

if (isset($_GET['remove_bus'])) {                                    // [27] Action Handler: Intercept GET request for vehicle decommissioning.
    $bus_id = $_GET['remove_bus'];                                   // [28] Capture: Target vehicle ID from URL parameter.
    $stmt_del = $conn->prepare("DELETE FROM buses WHERE bus_id = ?"); // [29] Prepare secure SQL deletion statement.
    $stmt_del->bind_param("i", $bus_id);                             // [30] Bind target ID to statement.
    if($stmt_del->execute()) { $msg = "Success: Bus record permanently removed from fleet."; } // [31] Attempt execution and log success.
    else { $msg = "Error: Database Integrity Violation. This bus is still assigned to active routes. Please delete or re-assign those routes before removing the vehicle."; } // [32] Log failure (likely active route dependencies).
    $stmt_del->close();                                              // [33] Release resource memory.
    header("Location: admin_buses_report.php?msg=" . urlencode($msg)); // [34] Refresh page with result message.
    exit();                                                          // [35] Terminate script post-redirect.
}                                                                    // [36] End decommissioning block.

if (isset($_POST['assign_driver'])) {                                // [37] Action Handler: Intercept crew assignment update.
    $bus_id = $_POST['bus_id'];                                      // [38] Capture: Target vehicle identifier.
    $driver_id = !empty($_POST['driver_id']) ? $_POST['driver_id'] : null; // [39] Logik: Map selection to ID or NULL for de-assignment.
    $stmt_upd = $conn->prepare("UPDATE buses SET driver_id = ? WHERE bus_id = ?"); // [40] Prepare secure SQL update command.
    $stmt_upd->bind_param("ii", $driver_id, $bus_id);                // [41] Bind integers to statement placeholders.
    $stmt_upd->execute();                                            // [42] Commit staff change to the fleet ledger.
    $stmt_upd->close();                                              // [43] Release statement resource.
    header('Location: admin_buses_report.php?msg=Staff Update: Driver assignment refreshed.'); // [44] Refresh page with update status.
    exit();                                                          // [45] Terminate script post-redirect.
}                                                                    // [46] End assignment block.
?>                                                                   <!-- [47] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [48] Define standard HTML5 document type. -->
<html lang="en">                                                     <!-- [49] Root element identifying English. -->
<head>                                                               <!-- [50] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [51] UTF-8 character encoding. -->
    <title>Fleet & Driver Management - Wema Travellers</title>         <!-- [52] Browser tab title. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [53] Global layout assets. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [54] Global branding assets. -->
    <style>                                                          /* [55] Internal CSS for fleet management layout. */
        .view-container { max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); } /* [56] Main container. */
        .back-btn-container { padding: 20px; max-width: 1200px; margin: 0 auto; } /* [57] Navigation layout. */
        .crud-table { width: 100%; border-collapse: collapse; margin-top: 20px; } /* [58] Data grid. */
        .crud-table th, .crud-table td { padding: 12px; border: 1px solid #eeeeee; text-align: left; } /* [59] Cell styles. */
        .crud-table th { background-color: var(--purple); color: white; font-weight: 600; } /* [60] Branding. */
        .action-btn { padding: 6px 12px; border-radius: 4px; text-decoration: none; color: white; font-size: 0.85em; font-weight: bold; } /* [61] Action buttons. */
        .btn-delete { background-color: #e74c3c; }                   /* [62] Destructive button. */
        .btn-delete:hover { background-color: #c0392b; }             /* [63] Interaction. */
        .add-form { background: #f8f9fa; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #dee2e6; } /* [64] Inventory form card. */
        .add-form h3 { margin-top: 0; color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 10px; } /* [65] Form title. */
        .form-row { display: flex; gap: 15px; flex-wrap: wrap; }     /* [66] Responive grid. */
        .form-group { flex: 1; min-width: 200px; }                   /* [67] Form field box. */
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; } /* [68] Field labels. */
    </style>                                                         <!-- [69] End internal CSS. -->
</head>                                                              <!-- [70] End head. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">                                                               <!-- [71] Start visible body. -->
    <script src="js/header2.js"></script>                                <!-- [72] Inject global navigation. -->
    <div style="height: 100px;"></div>                                   <!-- [73] Fixed header offset. -->

    <div class="back-btn-container"><a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Control Panel Home</a></div> <!-- [74] Exit path. -->

    <div class="view-container">                                     <!-- [75] Open fleet card. -->
        <h2>Bus Fleet & Crew Management</h2>                        <!-- [76] Title. -->
        <?php if(isset($_GET['msg'])): ?><div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;"><strong>System Notice:</strong> <?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?> <!-- [77] Status notification. -->

        <div class="add-form">                                       <!-- [78] Open registration form. -->
            <h3>Fleet Registration Form</h3>                         <!-- [79] Form title. -->
            <form method="POST"><div class="form-row">               <!-- [80] Open form layout. -->
                <div class="form-group"><label>Vehicle Plate No.</label><input type="text" name="reg_no" class="input" required placeholder="e.g. KCA 001Z"></div> <!-- [81] Plate entry. -->
                <div class="form-group"><label>Bus Display Name</label><input type="text" name="bus_name" class="input" required placeholder="e.g. Scania Luxury"></div> <!-- [82] Name entry. -->
                <div class="form-group"><label>Total Seat Count</label><input type="number" name="max_passengers" class="input" required value="40" min="10" max="100"></div> <!-- [83] Cap entry. -->
            </div><button type="submit" name="add_bus" class="button regular-button pink-background" style="margin-top: 15px;">Add Vehicle to Fleet</button></form> <!-- [84] Save btn. -->
        </div>                                                       <!-- [85] End form. -->

        <table class="crud-table">                                   <!-- [86] Start fleet grid. -->
            <thead><tr><th>Ref ID</th><th>Bus Identity</th><th>Plate Number</th><th>Capacity</th><th>Crew Assignment (Driver)</th><th>Operations</th></tr></thead> <!-- [87] Head. -->
            <tbody>                                                  <!-- [88] Inventory list start. -->
                <?php $drivers_res = $conn->query("SELECT driver_id, full_name FROM drivers ORDER BY full_name ASC"); $drivers_list = []; while($d = $drivers_res->fetch_assoc()) { $drivers_list[] = $d; } // [89] Cache personnel for dropdown population. ?>
                <?php $sql_fleet = "SELECT b.*, d.full_name as driver_name FROM buses b LEFT JOIN drivers d ON b.driver_id = d.driver_id ORDER BY b.bus_id DESC"; $result_fleet = $conn->query($sql_fleet); while($bus_row = $result_fleet->fetch_assoc()): ?> <!-- [90] Loop through fleet records with driver JOIN. -->
                <tr><td><?= $bus_row['bus_id'] ?></td><td><?= htmlspecialchars($bus_row['bus_name']) ?></td><td style="font-family: monospace; font-weight: bold;"><?= htmlspecialchars($bus_row['reg_no']) ?></td><td><?= $bus_row['max_passengers'] ?> Seats</td> <!-- [91] Vehicle stats. -->
                    <td><form method="POST" style="display:flex; gap:8px; align-items:center;"><input type="hidden" name="bus_id" value="<?= $bus_row['bus_id'] ?>"><select name="driver_id" class="input" style="margin-top:0; height:36px; padding:0 8px; flex:1;"><option value="">-- [ UNASSIGNED ] --</option><?php foreach($drivers_list as $crew): ?><option value="<?= $crew['driver_id'] ?>" <?= ($crew['driver_id'] == $bus_row['driver_id'] ? 'selected' : '') ?>><?= htmlspecialchars($crew['full_name']) ?></option><?php endforeach; ?></select><button type="submit" name="assign_driver" class="button regular-button pink-background" style="height:36px; margin-top:0; padding:0 15px; font-size:0.85em; border-radius:4px; box-shadow:none;">Update</button></form></td> <!-- [92] Staff mapping. -->
                    <td><a href="?remove_bus=<?= $bus_row['bus_id'] ?>" class="action-btn btn-delete" onclick="return confirm('CRITICAL WARNING: This will permanently delete the vehicle record. \n\nContinue?')">Delete Bus</a></td> <!-- [93] Decommission path. -->
                </tr>                                                <!-- [94] End row. -->
                <?php endwhile; ?>                                   <!-- [95] End loop. -->
            </tbody>                                                 <!-- [96] End body. -->
        </table>                                                     <!-- [97] End grid. -->
    </div>                                                           <!-- [98] End fleet card. -->
    <div style="height: 100px;"></div><script src="js/footer.js"></script> <!-- [99] Layout/Footer. -->
</body>                                                              <!-- [100] End body. -->
</html>                                                              <!-- [101] End document. -->
