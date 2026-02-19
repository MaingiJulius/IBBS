<?php
/**
 * STAFF MANAGEMENT (admin_drivers.php)
 * Purpose: This interface allows Administrators to manage the human resources 
 * (the professional drivers) of Wema Travellers.
 * Admins can register new drivers, see currently active drivers, and 
 * remove drivers who are no longer with the company.
 */

// --- INITIALIZATION ---
// Link the primary database bridge.
require_once 'db_connection.php';

// Start a session to verify the identity of the current user.
session_start();

/**
 * --- SECURITY ACCESS CONTROL ---
 * We must ensure that only authorized 'ADMIN' users can modify staff records.
 * Unauthorized users are blocked immediately.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    die("Security Error: Access denied. Driver management is restricted to Administrators.");
}

// =================================================================
// LOGIC: PROCESSING SYSTEM ACTIONS
// =================================================================

/**
 * --- ACTION: REGISTER A NEW DRIVER ---
 * Triggered when the Admin fills the registration form and clicks "Register Driver".
 */
if (isset($_POST['add_driver'])) {
    
    // CAPTURE: Gather the data from the form fields.
    $national_id = $_POST['national_id']; // Official Government ID
    $full_name   = $_POST['full_name'];   // Legal Name
    $phone       = $_POST['phone'];       // Mobile Contact
    $email       = $_POST['email'];       // Work Email

    // PREPARE: Set up the SQL template for insertion.
    $stmt_reg = $conn->prepare("INSERT INTO drivers (national_id, full_name, phone, email) VALUES (?, ?, ?, ?)");
    
    // BIND: Connect our PHP variables to the SQL placeholders "ssss" (4 strings).
    $stmt_reg->bind_param("ssss", $national_id, $full_name, $phone, $email);
    
    // EXECUTE: Command the database to save the record.
    $stmt_reg->execute();
    
    // CLEAN: Close the tool.
    $stmt_reg->close();
    
    // REDIRECT: Reload the page with a clean URL and a success flag.
    header('Location: admin_drivers.php?msg=System: Driver record created successfully.');
    exit();
}

/**
 * --- ACTION: REMOVE A DRIVER ---
 * Triggered by clicking the 'Delete' link next to a specific driver.
 */
if (isset($_GET['remove_driver'])) {
    
    // RETRIEVE: Identify the target driver by their unique ID.
    $driver_id = $_GET['remove_driver'];
    
    // PREPARE: Command to remove the specific driver record.
    $stmt_rem = $conn->prepare("DELETE FROM drivers WHERE driver_id = ?");
    
    // BIND: Link the integer ID.
    $stmt_rem->bind_param("i", $driver_id);
    
    /**
     * ATTEMPT: Execute deletion.
     * Note: If a driver is currently linked to a bus, the database might 
     * block this deletion to preserve data integrity.
     */
    if($stmt_rem->execute()) {
        $msg = "Success: Driver has been removed from the registry.";
    } else {
        // Error case: Usually a Foreign Key constraint fail.
        $msg = "Error: This driver is currently assigned to a bus. Please update the bus fleet before deletion.";
    }
    
    $stmt_rem->close();
    
    // REDIRECT: Send the result message back.
    header("Location: admin_drivers.php?msg=" . urlencode($msg));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Professional Staff - Wema Travellers</title>
    
    <!-- site-wide stylesheets -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* PAGE STYLING: Local visual rules for the drivers view */
        .view-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .back-btn-container {
            padding: 20px 0;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Table: Displays the list of registered staff */
        .crud-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .crud-table th, .crud-table td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            text-align: left;
        }

        /* Branding: Use Wema Purple for header */
        .crud-table th {
            background-color: var(--purple);
            color: white;
            font-weight: 600;
        }

        /* Action link styling (Delete button) */
        .action-btn {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 0.85em;
            font-weight: bold;
            transition: opacity 0.2s;
        }
        .btn-delete { background-color: #f44336; } /* Standard Red */
        .btn-delete:hover { opacity: 0.8; }
        
        /* The Card-style form for Adding Drivers */
        .add-form {
            background: #fdfdfd;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 40px;
            border: 1px solid #eee;
        }

        .add-form h3 { 
            margin-top: 0; 
            color: #444; 
            margin-bottom: 20px;
        }
        
        /* Grid-like form layout */
        .form-row { display: flex; gap: 15px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 200px; }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <!-- Standard Site Navigation -->
    <script src="js/header2.js"></script>
    
    <!-- Offset for fixed header -->
    <div style="height: 100px;"></div>

    <div class="container" style="max-width:1200px; margin: 0 auto;">
        <div class="back-btn-container">
            <!-- Navigation link to the Central Control Panel -->
            <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Control Panel Home</a>
        </div>

        <div class="view-container">
            <h2>Drivers & Staff Registry</h2>

            <!-- NOTIFICATION: Display any success or error messages from POST actions -->
            <?php if(isset($_GET['msg'])): ?>
                <div style="padding: 15px; margin-bottom: 25px; border-radius: 6px; 
                             background-color: <?= strpos($_GET['msg'], 'Error') !== false ? '#ffeeee' : '#eeffee' ?>;
                             color: <?= strpos($_GET['msg'], 'Error') !== false ? '#cc0000' : '#007700' ?>;
                             border: 1px solid <?= strpos($_GET['msg'], 'Error') !== false ? '#ffcccc' : '#ccffcc' ?>;">
                    <strong>Notice:</strong> <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <!-- SECTION: DATA ENTRY - Adding a new professional to the list -->
            <div class="add-form">
                <h3>New Staff Registration</h3>
                <form method="POST">
                    <div class="form-row">
                        <!-- Box 1: Name -->
                        <div class="form-group">
                            <label>Full Legal Name</label>
                            <input type="text" name="full_name" class="input" placeholder="e.g. John Kamau" required>
                        </div>
                        <!-- Box 2: Identification -->
                        <div class="form-group">
                            <label>National ID / Passport</label>
                            <input type="text" name="national_id" class="input" placeholder="ID Number" required>
                        </div>
                        <!-- Box 3: Communication - Phone -->
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="input" placeholder="0712345678" required>
                        </div>
                        <!-- Box 4: Communication - Email -->
                        <div class="form-group">
                            <label>Work Email Address</label>
                            <input type="email" name="email" class="input" placeholder="john.k@wematravellers.com" required>
                        </div>
                    </div>
                    <!-- Commit the new staff record -->
                    <button type="submit" name="add_driver" class="button regular-button pink-background" style="margin-top: 15px;">Finalize Registration</button>
                </form>
            </div>

            <!-- SECTION: REGISTRY LIST - Overview of all staff -->
            <table class="crud-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>ID Number</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Operating Vehicle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // QUERY: Fetch every driver. 
                    // JOIN: We check the 'buses' table to see if this driver is assigned to a vehicle.
                    $sql_list = "SELECT d.*, b.bus_name 
                                 FROM drivers d 
                                 LEFT JOIN buses b ON d.driver_id = b.driver_id 
                                 ORDER BY d.driver_id DESC";
                    $res_list = $conn->query($sql_list);
                    
                    // RENDER: Loop through each record and create a table row.
                    while($crew_row = $res_list->fetch_assoc()):
                    ?>
                    <tr>
                        <!-- System ID -->
                        <td>#<?= $crew_row['driver_id'] ?></td>
                        
                        <!-- Formatted Name -->
                        <td style="font-weight: 600; color: #333;"><?= htmlspecialchars($crew_row['full_name']) ?></td>
                        
                        <!-- Government Tracking info -->
                        <td><?= htmlspecialchars($crew_row['national_id']) ?></td>
                        
                        <!-- Contact Details -->
                        <td><?= htmlspecialchars($crew_row['phone']) ?></td>
                        <td><?= htmlspecialchars($crew_row['email']) ?></td>
                        
                        <!-- Operational Status -->
                        <td>
                            <?php if($crew_row['bus_name']): ?>
                                <span style="color: #2e7d32; font-weight: bold;">( <?= htmlspecialchars($crew_row['bus_name']) ?> )</span>
                            <?php else: ?>
                                <span style="color: #999; font-style: italic;">No Bus Assigned</span>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <!-- DESTRUCTIVE ACTION: Remove record with a safety check -->
                            <a href="?remove_driver=<?= $crew_row['driver_id'] ?>" 
                               class="action-btn btn-delete" 
                               onclick="return confirm('CRITICAL: This will permanently remove this driver from the active staff list. \n\nAre you absolutely sure?')">Delete Record</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Layout Spacing -->
    <div style="height: 100px;"></div>
    
    <!-- Site Footer component -->
    <script src="js/footer.js"></script>
</body>
</html>
