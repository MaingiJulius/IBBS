<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * ADMINISTRATION: USER MANAGEMENT HUB (view_users_sorted.php)       // [4] Title identifying this script as the master user dashboard.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script provides an interface for staff members      // [6] Description: bridge for staff to audit the user registry.
 * (Admins & Agents) to audit the entire system's user database.    // [7] Target audience: high-level personnel.
 * Capabilities: User Registration, Role Assignment, Edit, and Delete. // [8] Functional summary of management capabilities.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

// [1] DATABASE BRIDGE: Import the $conn connection configuration.   // [11] Documentation for the database integration step.
require_once 'db_connection.php';                                    // [12] Import the database bridge object ($conn) for MySQL communication.

// [2] SESSION STATE: Start the engine to track who is currently accessing the page. // [13] Documentation for session handling.
session_start();                                                    // [14] Initialize or resume the user session to identify the requester.

/* --- [3] ACCESS WALL: ROLE-BASED SECURITY --- */                   // [15] Marker for the primary security access control check.

/**                                                                  // [16] Documentation for security check logic.
 * Security Check Logic:                                             // [17] Header for authorization verification.
 * - is_set($_SESSION['role']): Ensures they are logged in.          // [18] Authentication prerequisite.
 * - in_array(...): Ensures their role is either 'ADMIN' or 'AGENT'. // [19] Role-level prerequisite.
 * - If check fails: Terminate script with a clear 'Access Denied' message. // [20] Outcome for unauthorized access.
 */                                                                  // [21] Close documentation block.
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) { // [22] Verify staff permission via role hierarchy.
    die("Access Denied: Staff Authorization Required.");             // [23] Halt execution and display error if unauthorized.
}                                                                    // [24] Close security validation boundary.

/* --- [4] TRANSACTIONAL LOGIC: DELETE OPERATION --- */               // [25] Marker for processing the 'Delete' action via URL parameter.

// Condition: Check if the 'delete_user' variable exists in the URL queries. // [26] Documentation for deletion condition.
if (isset($_GET['delete_user'])) {                                   // [27] Detect if 'delete_user' exists in the incoming query string.
    // Capture the unique ID of the target user to be removed.        // [28] Documentation for ID capture.
    $uid = $_GET['delete_user'];                                     // [29] Capture the specific user ID targeted for removal.
    
    /* --- [4.5] SELF-DELETION PROTECTION --- */                       // [30] Safety check to prevent administrative lockout.
    // Logic: Prevent an Admin from locking themselves out of the system. // [31] Rationale for self-deletion block.
    if ($uid == $_SESSION['user_id']) {                              // [32] Logic: Prevent the active user from deleting themselves.
        // Redirection with a specific error flag in the URL.        // [33] documentation for error redirect.
        header("Location: view_users_sorted.php?err=System Safety: You cannot delete your own active session account!"); // [34] Redirect with self-safety error message.
    } else {                                                         // [35] Else block for standard deletion of other users.
        // Standard Deletion Flow:                                    // [36] Header for normal deletion logic.
        // PREPARE: Formulate the DELETE SQL template with security placeholders. // [37] Documentation for SQL prep.
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?"); // [38] Prepare the SQL command for secure execution.
        // BIND: Inject the user ID safely as an integer ('i').        // [39] Documentation for binding.
        $stmt->bind_param("i", $uid);                                // [40] Bind the target ID to the placeholder as an integer.
        // EXECUTE: Command the database to remove the matching record. // [41] Documentation for execution.
        $stmt->execute();                                            // [42] Commit the deletion on the database server.
        // TEARDOWN: Close the statement instance.                    // [43] Documentation for cleanup.
        $stmt->close();                                              // [44] Release the statement resource.
        
        // Success Redirection: Refresh the list with a confirmation note. // [45] Documentation for success redirect.
        header("Location: view_users_sorted.php?msg=Success: The selected user record has been deleted."); // [46] Redirect with success confirmation.
    }                                                                // [47] Close conditional safety block.
    // EXIT: Ensure no further code runs after the header relocation. // [48] Documentation for exit.
    exit();                                                          // [49] Halt processing following the redirect header.
}                                                                    // [50] Close the deletion logic boundary.

/* --- [5] TRANSACTIONAL LOGIC: CREATE NEW USER --- */               // [51] Marker for processing new user registrations.

// Condition: Detect if the 'add_user' form was submitted via POST.   // [52] Documentation for POST detection.
if (isset($_POST['add_user'])) {                                     // [53] Detect if 'add_user' form was submitted via POST method.
    // DATA CAPTURE: Map raw form inputs to local PHP variables.      // [54] Documentation for data mapping.
    $first = $_POST['first_name'];      // Passenger/Staff First Name. // [55] Extract new user's first name from form data.
    $last = $_POST['last_name'];       // Passenger/Staff Last Name.  // [56] Extract new user's last name from form data.
    $email = $_POST['email'];           // Primary communication/login ID. // [57] Extract new user's email login identifier.
    $phone = $_POST['phone_number'];    // Contact marker.             // [58] Extract new user's mobile contact number.
    $role = $_POST['role'];             // Identity: PASSENGER | AGENT | ADMIN. // [59] Extract the systemic permissions level (role).
    
    /* --- [5.5] SECURITY: CRYPTOGRAPHIC HASHING --- */               // [60] Section for secure password processing.
    // We never store visible passwords. password_hash() creates a secure one-way hash. // [61] Rationale for hashing.
    // PASSWORD_DEFAULT ensures the most modern algorithm (currently Bcrypt) is used. // [62] Algorithm selection rationale.
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);    // [63] Generate a one-way secure cryptographic password hash.

    // SQL PREPARATION: Build the insertion template.                 // [64] Documentation for INSERT preparation.
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?, ?)"); // [65] Prepare the INSERT SQL command.
    
    // PARAMETER BINDING: Map the 6 string inputs ("ssssss") to the query placeholders. // [66] Documentation for parameter binding.
    $stmt->bind_param("ssssss", $first, $last, $email, $phone, $pass, $role); // [67] Map the 6 string variables to the query placeholders.
    
    // EXECUTION BLOCK: Run the query and handle potential outcome scenarios. // [68] Documentation for execution block.
    if($stmt->execute()) {                                           // [69] Attempt to commit the new user record to the database.
        // CASE: SUCCESS.                                             // [70] Success scenario.
        $msg = "Success: A new profile for " . $role . " has been initialized."; // [71] Define success message.
    } else {                                                         // [72] Error scenario.
        // CASE: DB FAILURE (e.g. Email collision / Unique Constraint violation). // [73] Rationale for failure.
        $msg = "System Error: The user could not be initialized. " . $conn->error; // [74] Define error message with DB feedback.
    }                                                                // [75] Close execution check.
    
    // FINALIZATION: Close statement and refresh UI.                  // [76] Documentation for finalization.
    $stmt->close();                                                  // [77] Release the statement resource.
    header("Location: view_users_sorted.php?msg=" . urlencode($msg)); // [78] Redirect to clear form and display status message.
    exit();                                                          // [79] Halt further execution to ensure redirect behavior.
}                                                                    // [80] Close POST logic boundary.
?>                                                                   <!-- [81] Close PHP processing and prepare for document rendering. -->

<!DOCTYPE html>                                                         <!-- [82] Define the document type as standard HTML5. -->
<html lang="en">                                                     <!-- [83] Root element defining the content language as English. -->
<head>                                                               <!-- [84] Head section containing non-visible document metadata. -->
    <meta charset="UTF-8">                                           <!-- [85] Specify UTF-8 character encoding for the document. -->
    <title>User Directory Management - Wema Travellers</title>         <!-- [86] Set the browser tab title for the user directory page. -->
    
    <link rel="stylesheet" href="css/main.css">                      <!-- [87] Load the primary shared CSS stylesheet. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [88] Load global layout and branding style variables. -->
    
    <style>                                                          /* [89] Start internal CSS block for page-specific UI layout. */
        .view-container {                                            /* [90] Define the primary content card container. */
            max-width: 1200px;                                       /* [91] Limit max width for optimal layout on large screens. */
            margin: 30px auto;                                       /* [92] Apply vertical spacing and horizontal centering. */
            padding: 35px;                                           /* [93] Internal cushioning within the main card. */
            background: #ffffff;                                     /* [94] High-contrast white background signature. */
            border-radius: 12px;                                     /* [95] Modern rounded aesthetic for the dashboard card. */
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);                /* [96] Soft shadow for premium depth perception. */
        }                                                            /* [97] End container definition. */

        .back-btn-container { padding: 20px; max-width: 1200px; margin: 0 auto; } /* [98] Layout for the return navigation link. */
        .crud-table { width: 100%; border-collapse: collapse; margin-top: 30px; } /* [99] Base layout for the data grid table. */
        .crud-table th, .crud-table td { padding: 18px; border-bottom: 1px solid #edf2f7; text-align: left; } /* [100] standard cells. */
        .crud-table th { background-color: var(--purple); color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; } /* [101] header style. */
        .action-btn { padding: 8px 15px; border-radius: 8px; text-decoration: none; color: #ffffff; font-size: 0.85rem; display: inline-block; } /* [102] buttons. */
        .btn-delete { background-color: #ef4444; }                    /* [103] alert color for deletion. */
        .add-form { background: #f8fafc; padding: 30px; border-radius: 12px; margin-bottom: 40px; border: 1px solid #e2e8f0; } /* [104] entry form. */
        .form-row { display: flex; gap: 20px; flex-wrap: wrap; }      /* [105] horizontal form fields layout. */
        .form-group { flex: 1; min-width: 240px; }                    /* [106] field spacing logic. */
        .form-group label { display: block; margin-bottom: 8px; font-weight: 700; font-size: 0.85rem; color: #4a5568; } /* [107] labels. */
    </style>                                                         <!-- [108] Terminate internal CSS block. -->
</head>                                                              <!-- [109] End document head. -->

<body>                                                               <!-- [110] Open the document body for visible content. -->
    <script src="js/header2.js"></script>                                <!-- [111] Inject the global administrative navigation bar. -->
    <div style="height: 100px;"></div>                                   <!-- [112] Fixed header offset buffer. -->
    
    <div class="back-btn-container">                                      <!-- [113] Container for the return shortcut. -->
        <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; padding: 12px 30px;">← Return to Main Hub</a> <!-- [114] nav link. -->
    </div>                                                               <!-- [115] End return container. -->

    <div class="view-container">                                         <!-- [116] Main UI content card start. -->
        <h2 style="color: var(--purple); margin-bottom: 5px;">👥 User System Directory</h2> <!-- [117] Page header. -->
        <p style="color: #718096; margin-bottom: 30px; font-size: 0.95rem;">Auditing all registered profiles (Passengers, Agents, and Administrators).</p> <!-- [118] Subheader. -->

        <?php if(isset($_GET['msg'])): ?>                                <!-- [119] Conditional logic for success feedback. -->
            <div style="background: #f0fff4; color: #22543d; padding: 15px; border-radius: 8px; border-left: 6px solid #38a169; margin-bottom: 20px; font-weight: 600;"> ✅ <?= htmlspecialchars($_GET['msg']) ?> </div> <!-- [120] msg box. -->
        <?php endif; ?>                                                   <!-- [121] Close success check. -->
        
        <?php if(isset($_GET['err'])): ?>                                <!-- [122] Conditional logic for error feedback. -->
            <div style="background: #fff5f5; color: #9b2c2c; padding: 15px; border-radius: 8px; border-left: 6px solid #e53e3e; margin-bottom: 20px; font-weight: 600;"> ❌ <?= htmlspecialchars($_GET['err']) ?> </div> <!-- [123] err box. -->
        <?php endif; ?>                                                   <!-- [124] Close error check. -->

        <div class="add-form">                                           <!-- [125] Wrapper for account provisioning interface. -->
            <h3>🆕 Initialize New Account</h3>                           <!-- [126] Form title area. -->
            <form method="POST">                                         <!-- [127] Form definition targeting self with POST method. -->
                <div class="form-row">                                   <!-- [128] Responsive row for data entry fields. -->
                    <div class="form-group"><label>First Name</label><input type="text" name="first_name" class="input" placeholder="Samuel" required></div> <!-- [129] field. -->
                    <div class="form-group"><label>Last Name</label><input type="text" name="last_name" class="input" placeholder="Mwangi" required></div> <!-- [130] field. -->
                    <div class="form-group"><label>Email ID</label><input type="email" name="email" class="input" placeholder="name@domain.com" required></div> <!-- [131] field. -->
                    <div class="form-group"><label>Contact Phone</label><input type="text" name="phone_number" class="input" placeholder="0712 XXX XXX" required></div> <!-- [132] field. -->
                    <div class="form-group"><label>Security Password</label><input type="password" name="password" class="input" placeholder="Set temporary pass..." required></div> <!-- [133] field. -->
                    <div class="form-group"><label>Official Role</label><select name="role" class="input" required><option value="PASSENGER">PASSENGER</option><option value="AGENT">AGENT</option><option value="ADMIN">ADMIN</option></select></div> <!-- [134] field. -->
                </div>                                                   <!-- [135] end layout row. -->
                <button type="submit" name="add_user" class="button regular-button pink-background" style="margin-top: 25px; padding: 12px 40px;">Finalize Registration</button> <!-- [136] submit btn. -->
            </form>                                                      <!-- [137] end form. -->
        </div>                                                           <!-- [138] end provision box. -->

        <table class="crud-table">                                       <!-- [139] Main registry data table definition. -->
            <thead><tr><th>ID</th><th>Full Name</th><th>Verified Email</th><th>Mobile Contact</th><th>Identity Role</th><th>Admin Commands</th></tr></thead> <!-- [140] headers. -->
            <tbody>                                                      <!-- [141] Open body for record rendering loop. -->
                <?php                                                     // [142] Re-open PHP for data retrieval.
                $sql = "SELECT * FROM users ORDER BY user_id ASC";       // [143] Define query to fetch all system personnel in sequence.
                $result = $conn->query($sql);                            // [144] Direct communication with MySQL to fetch user collection.
                while($row = $result->fetch_assoc()):                    // [145] Iterator: Process the result set row by row.
                ?>                                                        <!-- [146] Interrupt PHP to output the specific HTML row. -->
                <tr>                                                     <!-- [147] Data row definition for a single user record. -->
                    <td><strong style="color: #718096;"><?= $row['user_id'] ?></strong></td> <!-- [148] unique id cell. -->
                    <td style="font-weight: 700; color: #2d3748;"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td> <!-- [149] name. -->
                    <td style="color: #4a5568;"><?= htmlspecialchars($row['email']) ?></td> <!-- [150] email cell. -->
                    <td style="font-family: monospace; letter-spacing: 0.5px;"><?= htmlspecialchars($row['phone_number']) ?></td> <!-- [151] phone cell. -->
                    <td><span style="background: <?= ($row['role'] == 'ADMIN' ? '#faf5ff' : ($row['role'] == 'AGENT' ? '#f0fff4' : '#ebf8ff')) ?>; color: <?= ($row['role'] == 'ADMIN' ? '#6b46c1' : ($row['role'] == 'AGENT' ? '#2f855a' : '#2b6cb0')) ?>; padding: 5px 12px; border-radius: 50px; font-weight: 900; font-size: 0.7rem; border: 1px solid currentColor;"><?= $row['role'] ?></span></td> <!-- [152] badge. -->
                    <td>                                                  <!-- [153] Administrative command container. -->
                        <a href="edit_user.php?user_id=<?= $row['user_id'] ?>" class="action-btn" style="background-color: #3182ce; margin-right: 10px;">Update</a> <!-- [154] update shortcut. -->
                        <a href="?delete_user=<?= $row['user_id'] ?>" class="action-btn btn-delete" onclick="return confirm('CRITICAL WARNING: Permanent deletion? Proceed?')">Delete</a> <!-- [155] delete. -->
                    </td>                                                <!-- [156] end commands. -->
                </tr>                                                    <!-- [157] end record row. -->
                <?php endwhile; ?>                                       <!-- [158] End database rendering iteration. -->
            </tbody>                                                     <!-- [159] Close data body. -->
        </table>                                                         <!-- [160] End data grid. -->
    </div>                                                               <!-- [161] End central card. -->

    <div style="height: 120px;"></div>                                   <!-- [162] Buffer for scrolling clearance. -->
    <script src="js/footer.js"></script>                                 <!-- [163] Inject global site footer script. -->
</body>                                                              <!-- [164] End visible body section. -->
</html>                                                              <!-- [165] Formal document termination. -->
