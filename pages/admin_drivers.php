<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * STAFF MANAGEMENT (admin_drivers.php)                              // [3] Title identifying this script as the personnel management hub.
 * Purpose: This interface allows Administrators to manage the human resources // [4] Main objective: manage professional drivers.
 * (the professional drivers) of Wema Travellers.                    // [5] Functionality: staff registry oversight.
 * Admins can register new drivers, see currently active drivers, and // [6] Task: CRUD operations for professional staff.
 * remove drivers who are no longer with the company.                // [7] Task: personnel retirement/deletion.
 */                                                                  // [8] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [9] Import database bridge object ($conn) for MySQL communication.
require_once 'logger.php';                                           // [9.5] Import logging utility for audit trail.
session_start();                                                    // [10] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {      // [11] Security Barrier: Verify requester identity and 'ADMIN' credential.
    die("Security Error: Access denied. Driver management is restricted to Administrators."); // [12] Halt execution with descriptive error.
}                                                                    // [13] Close security barrier.

if (isset($_POST['add_driver'])) {                                   // [14] Action Handler: Intercept form submission for new driver registration.
    $national_id = $_POST['national_id'];                            // [15] Capture: Official government identifier for tracking.
    $full_name   = $_POST['full_name'];                              // [16] Capture: Legal name of the driver professional.
    $phone       = $_POST['phone'];                                  // [17] Capture: Primary mobile contact channel.
    $email       = $_POST['email'];                                  // [18] Capture: Digital work contact record.
    $stmt_reg = $conn->prepare("INSERT INTO drivers (national_id, full_name, phone, email) VALUES (?, ?, ?, ?)"); // [19] Prepare secure SQL insertion statement.
    $stmt_reg->bind_param("ssss", $national_id, $full_name, $phone, $email); // [20] Bind data variables to statement placeholders.
    $stmt_reg->execute();                                            // [21] Commit new driver record to the MySQL engine.
    $stmt_reg->close();                                              // [22] Release statement resource memory.
    header('Location: admin_drivers.php?msg=System: Driver record created successfully.'); // [23] Refresh page with success notification.
    exit();                                                          // [24] Terminate script post-redirect.
}                                                                    // [25] End registration block.

if (isset($_GET['remove_driver'])) {                                 // [26] Action Handler: Intercept GET request for driver deletion.
    $driver_id = $_GET['remove_driver'];                             // [27] Capture: Target driver ID from URL parameter.
    $stmt_rem = $conn->prepare("DELETE FROM drivers WHERE driver_id = ?"); // [28] Prepare secure SQL deletion statement.
    $stmt_rem->bind_param("i", $driver_id);                          // [29] Bind target ID to statement.
    
    // [AUDIT LOG] Record the deletion.
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed Driver Record (DID: $driver_id)");

    if($stmt_rem->execute()) { $msg = "Success: Driver has been removed from the registry."; } // [30] Attempt execution and log success.
    else { $msg = "Error: This driver is currently assigned to a bus. Please update the bus fleet before deletion."; } // [31] Log failure (likely FK constraint).
    $stmt_rem->close();                                              // [32] Release memory.
    header("Location: admin_drivers.php?msg=" . urlencode($msg));     // [33] Refresh page with result message.
    exit();                                                          // [34] Terminate script.
}                                                                    // [35] End deletion block.
?>                                                                   <!-- [36] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [37] Define standard HTML5 document type. -->
<html lang="en">                                                     <!-- [38] Root element identifying English. -->
<head>                                                               <!-- [39] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [40] UTF-8 character encoding. -->
    <title>Manage Professional Staff - Wema Travellers</title>          <!-- [41] Browser tab title. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [42] Global layout assets. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [43] Global branding assets. -->
    <style>                                                          /* [44] Internal CSS for driver management layout. */
        .view-container { max-width: 1200px; margin: 20px auto; padding: 30px; background: #ffffff; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); } /* [45] Main container. */
        .back-btn-container { padding: 20px 0; max-width: 1200px; margin: 0 auto; } /* [46] Navigation layout. */
        .crud-table { width: 100%; border-collapse: collapse; margin-top: 20px; } /* [47] Data grid. */
        .crud-table th, .crud-table td { padding: 15px; border-bottom: 1px solid #f0f0f0; text-align: left; } /* [48] Cell styles. */
        .crud-table th { background-color: var(--purple); color: white; font-weight: 600; } /* [49] Branding. */
        .action-btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; color: white; font-size: 0.85em; font-weight: bold; transition: opacity 0.2s; } /* [50] Action buttons. */
        .btn-delete { background-color: #f44336; }                   /* [51] Destructive button. */
        .btn-delete:hover { opacity: 0.8; }                          /* [52] Interaction. */
        .add-form { background: #fdfdfd; padding: 25px; border-radius: 10px; margin-bottom: 40px; border: 1px solid #eee; } /* [53] Reg form card. */
        .add-form h3 { margin-top: 0; color: #444; margin-bottom: 20px; } /* [54] Form title. */
        .form-row { display: flex; gap: 15px; flex-wrap: wrap; }     /* [55] Responive grid. */
        .form-group { flex: 1; min-width: 200px; }                   /* [56] Form field box. */
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #666; font-size: 0.9em; } /* [57] Field labels. */
    </style>                                                         <!-- [58] End internal CSS. -->
</head>                                                              <!-- [59] End head. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">                                                               <!-- [60] Start visible body. -->
    <script src="js/header2.js"></script>                                <!-- [61] Inject global header. -->
    <div style="height: 100px;"></div>                                   <!-- [62] Layout offset. -->

    <div class="container" style="max-width:1200px; margin: 0 auto;">    <!-- [63] Content constraint. -->
        <div class="back-btn-container"><a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Control Panel Home</a></div> <!-- [64] Return path. -->

        <div class="view-container">                                     <!-- [65] Open management card. -->
            <h2>Drivers & Staff Registry</h2>                            <!-- [66] Title. -->
            <?php if(isset($_GET['msg'])): ?>                            <!-- [67] Check for system notifications. -->
            <div style="padding: 15px; margin-bottom: 25px; border-radius: 6px; background-color: <?= strpos($_GET['msg'], 'Error') !== false ? '#ffeeee' : '#eeffee' ?>; color: <?= strpos($_GET['msg'], 'Error') !== false ? '#cc0000' : '#007700' ?>; border: 1px solid <?= strpos($_GET['msg'], 'Error') !== false ? '#ffcccc' : '#ccffcc' ?>;"><strong>Notice:</strong> <?= htmlspecialchars($_GET['msg']) ?></div> <!-- [68] Display notification. -->
            <?php endif; ?>                                              <!-- [69] End notification check. -->

            <div class="add-form">                                       <!-- [70] Open registration form. -->
                <h3>New Staff Registration</h3>                          <!-- [71] Form Title. -->
                <form method="POST" id="driverForm" onsubmit="return validateForm()">
                    <div class="form-row">               <!-- [72] Open form layout. -->
                        <div class="form-group"><label>Full Legal Name</label><input type="text" name="full_name" id="full_name" class="input" placeholder="e.g. John Kamau" onmouseout="validateFullName()"></div> <!-- [73] Name entry. -->
                        <div class="form-group"><label>ID / PASSPORT / BIRTH CERT. NO</label><input type="text" name="national_id" id="national_id" class="input" placeholder="ID/PASSPORT/BIRTH CERT. NO" onmouseout="validateID()"></div> <!-- [74] ID entry. -->
                        <div class="form-group"><label>Phone Number</label><input type="text" name="phone" id="phone" class="input" placeholder="0712345678" onmouseout="validatePhone()"></div> <!-- [75] Phone entry. -->
                        <div class="form-group"><label>Work Email Address</label><input type="text" name="email" id="email" class="input" placeholder="john.k@wematravellers.com" onmouseout="validateEmail()"></div> <!-- [76] Email entry. -->
                    </div>
                    <button type="submit" name="add_driver" class="button regular-button pink-background" style="margin-top: 15px;">Finalize Registration</button>
                </form> <!-- [77] Submit btn. -->
            </div>                                                       <!-- [78] End form. -->

            <script>
                // Custom JS Validation for New Staff Registration
                function validateFullName() {
                    var val = document.getElementById("full_name").value.trim();
                    if (val.length < 3) {
                        alert("Please enter a valid full name (min 3 characters).");
                        document.getElementById("full_name").focus();
                        return false;
                    }
                    return true;
                }

                function validateID() {
                    var val = document.getElementById("national_id").value.trim();
                    if (val.length < 5) {
                        alert("ID/PASSPORT/BIRTH CERT. NO is required (min 5 characters).");
                        document.getElementById("national_id").focus();
                        return false;
                    }
                    return true;
                }

                function validatePhone() {
                    var val = document.getElementById("phone").value.trim();
                    if (val.length < 9 || isNaN(val)) {
                        alert("Please enter a valid numeric phone number.");
                        document.getElementById("phone").focus();
                        return false;
                    }
                    return true;
                }

                function validateEmail() {
                    var email = document.getElementById("email").value.trim();
                    if (email.length == 0 || email.indexOf("@") == -1 || email.indexOf(".") == -1) {
                        alert("Please enter a valid work email address.");
                        document.getElementById("email").focus();
                        return false;
                    }
                    return true;
                }

                function validateForm() {
                    if (!validateFullName()) return false;
                    if (!validateID()) return false;
                    if (!validatePhone()) return false;
                    if (!validateEmail()) return false;
                    return true;
                }
            </script>


            <table class="crud-table">                                   <!-- [79] Start registry grid. -->
                <thead><tr><th>Ref ID</th><th>Name</th><th>ID Number</th><th>Phone</th><th>Email</th><th>Operating Vehicle</th><th>Actions</th></tr></thead> <!-- [80] Head. -->
                <tbody>                                                  <!-- [81] Staff list start. -->
                    <?php $sql_list = "SELECT d.*, b.bus_name FROM drivers d LEFT JOIN buses b ON d.driver_id = b.driver_id ORDER BY d.driver_id DESC"; $res_list = $conn->query($sql_list); while($crew_row = $res_list->fetch_assoc()): ?> <!-- [82] Loop through personnel records with vehicle JOIN. -->
                    <tr><td><?= $crew_row['driver_id'] ?></td><td style="font-weight: 600; color: #333;"><?= htmlspecialchars($crew_row['full_name']) ?></td> <!-- [83] ID/Name. -->
                        <td><?= htmlspecialchars($crew_row['national_id']) ?></td><td><?= htmlspecialchars($crew_row['phone']) ?></td><td><?= htmlspecialchars($crew_row['email']) ?></td> <!-- [84] ID/Contact details. -->
                        <td><?php if($crew_row['bus_name']): ?><span style="color: #2e7d32; font-weight: bold;">( <?= htmlspecialchars($crew_row['bus_name']) ?> )</span><?php else: ?><span style="color: #999; font-style: italic;">No Bus Assigned</span><?php endif; ?></td> <!-- [85] Vehicle status. -->
                        <td><a href="?remove_driver=<?= $crew_row['driver_id'] ?>" class="action-btn btn-delete" onclick="return confirm('CRITICAL: This will permanently remove this driver from the active staff list. \n\nAre you absolutely sure?')">Delete Record</a></td> <!-- [86] Delete path. -->
                    </tr>                                                <!-- [87] End row. -->
                    <?php endwhile; ?>                                   <!-- [88] End loop. -->
                </tbody>                                                 <!-- [89] End body. -->
            </table>                                                     <!-- [90] End grid. -->
        </div>                                                           <!-- [91] End view container. -->
    </div>                                                               <!-- [92] End main container. -->
    <div style="height: 100px;"></div><script src="js/footer.js"></script> <!-- [93] Layout/Footer. -->
</body>                                                              <!-- [94] End body. -->
</html>                                                              <!-- [95] End document. -->
