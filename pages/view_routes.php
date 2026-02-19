<?php
// =================================================================
// ROUTES MANAGEMENT PAGE (view_routes.php)
// =================================================================
// This page allows Staff (Admins & Agents) to manage bus travel schedules.
// It lists all available routes, grouped by country, and allows adding new trips.
// =================================================================

// 1. Include the database connection.
require_once 'db_connection.php';
// 2. Start the session to verify user access.
session_start();

// --- SECURITY CHECK ---
// Ensure the user is an 'ADMIN' or 'AGENT'.
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    header("Location: login.html");
    exit();
}

// --- DELETE ROUTE LOGIC ---
// If the 'delete_route' parameter is present in the URL...
if (isset($_GET['delete_route'])) {
    $route_id = $_GET['delete_route'];
    // Prepare a DELETE SQL statement.
    $stmt = $conn->prepare("DELETE FROM routes WHERE route_id = ?");
    $stmt->bind_param("i", $route_id);
    $stmt->execute();
    $stmt->close();
    // Redirect back to the list with a success message.
    header("Location: view_routes.php?msg=Route deleted");
    exit();
}

// --- ADD ROUTE LOGIC ---
// If the 'add_route' form is submitted via POST...
if (isset($_POST['add_route'])) {
    // Collect all input values.
    $from = $_POST['from_location'];
    $to = $_POST['to_location'];
    $date = $_POST['departure_date'];
    $time = $_POST['departure_time'];
    $cost = $_POST['cost'];
    $bus_id = $_POST['bus_id'];

    // Prepare an INSERT statement for the 'routes' table.
    $stmt = $conn->prepare("INSERT INTO routes (from_location, to_location, departure_date, departure_time, cost, bus_id) VALUES (?, ?, ?, ?, ?, ?)");
    // Bind parameters: 4 strings (s), 1 double/decimal (d), 1 integer (i).
    $stmt->bind_param("ssssdi", $from, $to, $date, $time, $cost, $bus_id);
    $stmt->execute();
    $stmt->close();
    // Redirect with success message.
    header("Location: view_routes.php?msg=New route added successfully!");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Routes - Wema Travellers</title>
    <!-- Import Global Styles -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* --- PAGE SPECIFIC STYLES --- */
        .view-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .back-btn-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Table Design */
        .crud-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .crud-table th, .crud-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .crud-table th {
            background-color: var(--purple);
            color: white;
        }

        /* Button Design */
        .action-btn {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 0.9em;
        }
        .btn-delete { background-color: #ff4d4d; }
        
        /* Add Route Form Design */
        .add-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #eee;
        }

        .add-form h3 { margin-top: 0; }
        .form-row { display: flex; gap: 10px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 150px; }
        .form-group label { display: block; font-size: 0.8em; margin-bottom: 5px;}
    </style>
</head>
<body>
    <!-- Inject Header -->
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <!-- Back Button -->
    <div class="back-btn-container">
        <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Back to Dashboard</a>
    </div>

    <!-- Main Content -->
    <div class="view-container">
        <!-- Title Banner -->
        <div class="welcome-banner" style="background: linear-gradient(135deg, var(--purple), var(--pink)); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <h2 style="margin:0;">Manage Bus Routes</h2>
            <p style="margin:5px 0 0; opacity: 0.9;">Staff Member: <?= htmlspecialchars($_SESSION['name']) ?></p>
        </div>
        
        <!-- Feedback Messages -->
        <?php if(isset($_GET['msg'])): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($_GET['msg']) ?></p>
        <?php endif; ?>

        <!-- SECTION: ADD NEW ROUTE FORM -->
        <div class="add-form">
            <h3>Add New Route</h3>
            <form method="POST">
                <div class="form-row">
                    <!-- Starting Point -->
                    <div class="form-group">
                        <label>Starting Point</label>
                        <input type="text" name="from_location" class="input" required placeholder="e.g. Nairobi">
                    </div>
                    <!-- Endpoint -->
                    <div class="form-group">
                        <label>Destination</label>
                        <input type="text" name="to_location" class="input" required placeholder="e.g. Kampala">
                    </div>
                    <!-- Date -->
                    <div class="form-group">
                        <label>Departure Date</label>
                        <input type="date" name="departure_date" class="input" required>
                    </div>
                    <!-- Time -->
                    <div class="form-group">
                        <label>Departure Time</label>
                        <input type="time" name="departure_time" class="input" required>
                    </div>
                    <!-- Price -->
                    <div class="form-group">
                        <label>Ticket Cost (KES)</label>
                        <input type="number" name="cost" class="input" required step="0.01" placeholder="0.00">
                    </div>
                    <!-- Bus Selector -->
                    <div class="form-group">
                        <label>Select Bus for this Route</label>
                        <select name="bus_id" class="input" required>
                            <?php
                            // Fetch all buses from the database to populate the dropdown.
                            $buses = $conn->query("SELECT bus_id, bus_name FROM buses");
                            while($b = $buses->fetch_assoc()) {
                                echo "<option value='{$b['bus_id']}'>{$b['bus_name']} (ID: {$b['bus_id']})</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <!-- Submit -->
                <button type="submit" name="add_route" class="button regular-button pink-background" style="margin-top: 15px;">Create Travel Schedule</button>
            </form>
        </div>

        <!-- SECTION: ROUTES LIST TABLE -->
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Travel Date</th>
                    <th>Time</th>
                    <th>Bus Assigned</th>
                    <th>Price (KES)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // FETCH ROUTES
                // We use 'SUBSTRING_INDEX' to extract the country from "City, Country".
                // We join 'routes' with 'buses' to get the bus name.
                // We order primarily by the extracted Country name.
                $sql = "SELECT r.*, b.bus_name, 
                        SUBSTRING_INDEX(r.from_location, ', ', -1) as country 
                        FROM routes r 
                        JOIN buses b ON r.bus_id = b.bus_id 
                        ORDER BY country ASC, r.from_location ASC, r.departure_date ASC";
                $result = $conn->query($sql);
                
                $current_country = ""; // Variable to keep track of grouping headers.

                // Loop through results
                while($row = $result->fetch_assoc()):
                    // Check if the country changed from the previous row.
                    if ($row['country'] != $current_country) {
                        $current_country = $row['country'];
                        // Print a special full-width row as a section header.
                        echo "<tr><td colspan='8' style='background:#f0f0f0; font-weight:bold; color:#333; text-transform:uppercase;'>Starting from: " . htmlspecialchars($current_country) . "</td></tr>";
                    }
                ?>
                <tr>
                    <!-- Display Route Details -->
                    <td><?= $row['route_id'] ?></td>
                    <td><?= htmlspecialchars($row['from_location']) ?></td>
                    <td><?= htmlspecialchars($row['to_location']) ?></td>
                    <td><?= $row['departure_date'] ?></td>
                    <td><?= $row['departure_time'] ?></td>
                    <td><?= htmlspecialchars($row['bus_name']) ?></td>
                    <td><?= number_format($row['cost'], 2) ?></td>
                    
                    <!-- Action Buttons -->
                    <td>
                        <!-- EDIT Button -->
                         <a href="edit_route.php?id=<?= $row['route_id'] ?>" class="action-btn" style="background-color: #4CAF50; margin-right: 5px;">Edit</a>

                        <!-- DELETE Button with confirm dialog -->
                        <a href="?delete_route=<?= $row['route_id'] ?>" class="action-btn btn-delete" onclick="return confirm('WARNING: Are you sure you want to delete this route?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer Space -->
    <div style="height: 100px;"></div>
    <!-- Inject Footer -->
    <script src="js/footer.js"></script>
</body>
</html>
