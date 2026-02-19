<?php
/**
 * BUS FLEET MANAGEMENT (admin_buses_report.php)
 * Purpose: This page serves as the control center for Wema Travellers' physical assets.
 * Admins use this to:
 * 1. Register new buses (Registration numbers, Names, Seat Limits).
 * 2. View the current fleet list.
 * 3. Assign/Change drivers for specific buses.
 * 4. Remove buses from the system (provided they have no active routes).
 */

// --- STEP 1: INITIALIZATION ---
// Include the database connection script.
require_once 'db_connection.php';

// Start a session to track if the current user is a valid Administrator.
session_start();

/**
 * --- STEP 2: SECURITY CHECK ---
 * We verify two conditions for access:
 * 1. The user must have an active session (user_id is set).
 * 2. The user's role MUST be 'ADMIN'.
 * Failing either check results in immediate termination of the script.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    die("Security Error: Access denied. Fleet management requires Administrative privileges.");
}

// =================================================================
// LOGIC SECTION: HANDLING USER ACTIONS (POST/GET)
// =================================================================

/**
 * --- ACTION: ADD A NEW BUS ---
 * Triggered when the "Register Bus" button is clicked in the form.
 */
if (isset($_POST['add_bus'])) {
    
    // CAPTURE: Take the raw text from the input fields.
    $reg_no         = $_POST['reg_no'];           // Official number plate (e.g., KBA 123A)
    $bus_name       = $_POST['bus_name'];       // Marketing name (e.g., Wema Express)
    $max_passengers = $_POST['max_passengers'];  // Legal seating capacity

    // PREPARE: Create a secure SQL template with placeholders (?) to prevent hacking.
    $stmt_add = $conn->prepare("INSERT INTO buses (reg_no, bus_name, max_passengers) VALUES (?, ?, ?)");
    
    // BIND: Link our variables to the template. "ssi" = String, String, Integer.
    $stmt_add->bind_param("ssi", $reg_no, $bus_name, $max_passengers);
    
    // EXECUTE: Run the command on the database.
    $stmt_add->execute();
    
    // CLEAN: Close the tool to save memory.
    $stmt_add->close();
    
    // REDIRECT: Refresh the page to show the new bus in the table.
    header('Location: admin_buses_report.php?msg=System: New bus record created successfully.');
    exit();
}

/**
 * --- ACTION: REMOVE A BUS ---
 * Triggered when the "Delete" link is clicked, passing a bus_id in the URL.
 */
if (isset($_GET['remove_bus'])) {
    
    // RETRIEVE: Get the specific ID of the bus to be removed.
    $bus_id = $_GET['remove_bus'];

    // PREPARE: Create a DELETE command.
    $stmt_del = $conn->prepare("DELETE FROM buses WHERE bus_id = ?");
    
    // BIND: Link the integer ID.
    $stmt_del->bind_param("i", $bus_id);
    
    /**
     * ATTEMPT: Try to delete the record.
     * Database Integrity Rule: You cannot delete a bus if it is currently linked to 
     * active routes (Foreign Key constraint).
     */
    if($stmt_del->execute()) {
        // Success: The bus was not linked to anything.
        $msg = "Success: Bus record permanently removed from fleet.";
    } else {
        // Failure: The database protected the data because routes still exist.
        $msg = "Error: Database Integrity Violation. This bus is still assigned to active routes. 
                Please delete or re-assign those routes before removing the vehicle.";
    }
    
    $stmt_del->close();
    
    // REDIRECT: Send the result message back via the URL.
    header("Location: admin_buses_report.php?msg=" . urlencode($msg));
    exit();
}

/**
 * --- ACTION: ASSIGN A DRIVER ---
 * Triggered when the 'Update' button in the table row is clicked.
 */
if (isset($_POST['assign_driver'])) {
    
    // CAPTURE: Which bus and which driver were selected?
    $bus_id = $_POST['bus_id'];
    
    // LOGIC: If "-- No Driver --" was picked, we set the value to NULL in the database.
    $driver_id = !empty($_POST['driver_id']) ? $_POST['driver_id'] : null;

    // PREPARE: Update the 'driver_id' column for the specific bus.
    $stmt_upd = $conn->prepare("UPDATE buses SET driver_id = ? WHERE bus_id = ?");
    
    // BIND: Link the driver ID and bus ID as integers.
    $stmt_upd->bind_param("ii", $driver_id, $bus_id);
    
    // EXECUTE: Apply the changes.
    $stmt_upd->execute();
    
    $stmt_upd->close();
    
    // REDIRECT: Refresh to show updated assignment.
    header('Location: admin_buses_report.php?msg=Staff Update: Driver assignment refreshed.');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fleet & Driver Management - Wema Travellers</title>
    
    <!-- Link the central CSS theme and layout rules -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* DASHBOARD OVERRIDES: Local styling for the fleet table */
        .view-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .back-btn-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Styling for the main fleet data table */
        .crud-table {
            width: 100%;
            border-collapse: collapse; /* Merges borders for a clean look */
            margin-top: 20px;
        }

        .crud-table th, .crud-table td {
            padding: 12px;
            border: 1px solid #eeeeee;
            text-align: left;
        }

        /* Branding: Use the standard Wema Purple for header */
        .crud-table th {
            background-color: var(--purple);
            color: white;
            font-weight: 600;
        }

        /* Red Delete link styling */
        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 0.85em;
            font-weight: bold;
        }
        .btn-delete { background-color: #e74c3c; } /* Flat Red */
        .btn-delete:hover { background-color: #c0392b; }
        
        /* The Card-style form for Adding Buses */
        .add-form {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }

        .add-form h3 { 
            margin-top: 0; 
            color: #2c3e50; 
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .form-row { display: flex; gap: 15px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 200px; }
        
        /* Label styling for form inputs */
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>
    <!-- Inject the site-wide Navigation component -->
    <script src="js/header2.js"></script>
    
    <!-- Spacer to offset the fixed horizontal navbar -->
    <div style="height: 100px;"></div>

    <div class="back-btn-container">
        <!-- Navigation Link: Return to the main Administrator panel -->
        <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Control Panel Home</a>
    </div>

    <div class="view-container">
        <h2>Bus Fleet & Crew Management</h2>

        <!-- ALERT: Display any success or error messages from server operations -->
        <?php if(isset($_GET['msg'])): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <strong>System Notice:</strong> <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <!-- SECTION: REGISTRATION - Let Admin enter a new vehicle -->
        <div class="add-form">
            <h3>Fleet Registration Form</h3>
            <form method="POST">
                <div class="form-row">
                    <!-- Box 1: Legal Registration Number -->
                    <div class="form-group">
                        <label>Vehicle Plate No.</label>
                        <input type="text" name="reg_no" class="input" required placeholder="e.g. KCA 001Z">
                    </div>
                    <!-- Box 2: Public Name -->
                    <div class="form-group">
                        <label>Bus Display Name</label>
                        <input type="text" name="bus_name" class="input" required placeholder="e.g. Scania Luxury">
                    </div>
                    <!-- Box 3: Maximum Seats -->
                    <div class="form-group">
                        <label>Total Seat Count</label>
                        <input type="number" name="max_passengers" class="input" required value="40" min="10" max="100">
                    </div>
                </div>
                <!-- Action: Save the bus -->
                <button type="submit" name="add_bus" class="button regular-button pink-background" style="margin-top: 15px;">Add Vehicle to Fleet</button>
            </form>
        </div>

        <!-- SECTION: INVENTORY - Table of all active buses -->
        <table class="crud-table">
            <thead>
                <tr>
                    <th>DB ID</th>
                    <th>Bus Identity</th>
                    <th>Plate Number</th>
                    <th>Capacity</th>
                    <th>Crew Assignment (Driver)</th>
                    <th>Operations</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // HELPER: Fetch all available Drivers to populate the dropdown selects.
                $drivers_res = $conn->query("SELECT driver_id, full_name FROM drivers ORDER BY full_name ASC");
                $drivers_list = [];
                while($d = $drivers_res->fetch_assoc()) {
                    $drivers_list[] = $d;
                }

                // QUERY: Get the list of buses and who is driving them (if anyone).
                // LEFT JOIN ensures buses appear even if no driver is assigned (driver_id is NULL).
                $sql_fleet = "SELECT b.*, d.full_name as driver_name 
                              FROM buses b 
                              LEFT JOIN drivers d ON b.driver_id = d.driver_id 
                              ORDER BY b.bus_id DESC";
                $result_fleet = $conn->query($sql_fleet);
                
                // ITERATE: Loop through every bus record found.
                while($bus_row = $result_fleet->fetch_assoc()):
                ?>
                <tr>
                    <!-- System unique ID -->
                    <td>#<?= $bus_row['bus_id'] ?></td>
                    
                    <!-- Friendly name -->
                    <td><?= htmlspecialchars($bus_row['bus_name']) ?></td>
                    
                    <!-- Official license plate -->
                    <td style="font-family: monospace; font-weight: bold;"><?= htmlspecialchars($bus_row['reg_no']) ?></td>
                    
                    <!-- Seat count -->
                    <td><?= $bus_row['max_passengers'] ?> Seats</td>
                    
                    <td>
                        <!-- ASYNC-LIKE FORM: Allows Admin to change the driver without leaving the page -->
                        <form method="POST" style="display:flex; gap:8px; align-items:center;">
                            <!-- Hidden tag to tell PHP which bus is being edited -->
                            <input type="hidden" name="bus_id" value="<?= $bus_row['bus_id'] ?>">
                            
                            <!-- Searchable-style select menu for drivers -->
                            <select name="driver_id" class="input" style="margin-top:0; height:36px; padding:0 8px; flex:1;">
                                <option value="">-- [ UNASSIGNED ] --</option>
                                <?php foreach($drivers_list as $crew): ?>
                                    <!-- Logical Toggle: Highlight the driver currently assigned to THIS bus -->
                                    <option value="<?= $crew['driver_id'] ?>" <?= ($crew['driver_id'] == $bus_row['driver_id'] ? 'selected' : '') ?>>
                                        <?= htmlspecialchars($crew['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <!-- Commit the driver change -->
                            <button type="submit" name="assign_driver" class="button regular-button pink-background" style="height:36px; margin-top:0; padding:0 15px; font-size:0.85em; border-radius:4px; box-shadow:none;">Update</button>
                        </form>
                    </td>
                    
                    <td>
                        <!-- DESTRUCTIVE ACTION: Remove record with a safety confirmation prompt -->
                        <a href="?remove_bus=<?= $bus_row['bus_id'] ?>" class="action-btn btn-delete" onclick="return confirm('CRITICAL WARNING: This will permanently delete the vehicle record. \n\nContinue?')">Delete Bus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Layout Spacing -->
    <div style="height: 100px;"></div>
    
    <!-- Site-wide Footer component -->
    <script src="js/footer.js"></script>
</body>
</html>
