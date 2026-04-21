<?php
/**
 * ADD_ROUTE.PHP
 * Purpose: This is an internal administrative page used by Staff to manually create new bus routes.
 * A "Route" defines where a bus starts, where it ends, when it leaves, and the ticket price.
 * NOTE: This is a legacy file. Most route management is now handled in 'view_routes.php'.
 */

// --- STEP 1: SECURITY & SESSION ---
// session_start() allows the script to read who is currently logged in.
session_start();

// Include the database connection tool.
require_once 'db_connection.php';

/**
 * --- STEP 2: ACCESS CONTROL (Security Check) ---
 * We only want users with the 'admin' (Administrator) role to touch the route system.
 * If the user_id is missing from the session, or the role is NOT 'admin', we kick them out.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // die() stops the script and shows a message.
    die("Security Error: Access denied. Authorized Admins only.");
}

/**
 * --- STEP 3: DATA SUBMISSION (POST Request) ---
 * This block only runs when the "Add Route" button is clicked in the HTML form below.
 * $_SERVER['REQUEST_METHOD'] === 'POST' ensures the data comes from a submitted form.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_route'])) {
    
    // --- STEP 4: COLLECT FORM INPUTS ---
    // We capture all the boxes filled in by the Admin.
    $from   = $_POST['from_location']; // Departure city
    $to     = $_POST['to_location'];   // Destination city
    $date   = $_POST['departure_date']; // Date of travel (Y-m-d)
    $time   = $_POST['departure_time']; // Time of travel (H:i)
    $cost   = $_POST['cost'];           // Ticket price
    $bus_id = $_POST['bus_id'];         // The ID of the bus assigned to this trip

    /**
     * --- STEP 5: STORE DATA (SQL Stored Procedure) ---
     * This file uses a "Stored Procedure" called 'AddRoute'.
     * A Stored Procedure is a pre-written mini-script saved INSIDE the database.
     * It's faster and more secure than writing long SQL strings here.
     * We use prepare() and bind_param() to prevent SQL Injection hackers.
     */
    $stmt = $conn->prepare("CALL AddRoute(?, ?, ?, ?, ?, ?)");
    
    /**
     * bind_param explanation:
     * "ssssdi" means:
     * s = String (from)
     * s = String (to)
     * s = String (date)
     * s = String (time)
     * d = Double/Decimal (cost)
     * i = Integer (bus_id)
     */
    $stmt->bind_param("ssssdi", $from, $to, $date, $time, $cost, $bus_id);
    
    // Execute the database command.
    $stmt->execute();
    
    // Close the statement to free up memory.
    $stmt->close();

    // --- STEP 6: FEEDBACK & REDIRECT ---
    // After success, send the Admin back to the main routes management list.
    header("Location: admin_routes.php");
    exit(); // Always exit after a header redirect.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Route - Wema Travellers</title>
    
    <!-- Include basic site styles -->
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* CSS for the Route Entry page */
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .container {
            width: 90%;
            margin: 50px auto;
            max-width: 800px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        /* Styling the form card */
        form {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            border-radius: 6px;
            border: 1px solid #ddd;
            box-sizing: border-box; /* Ensures padding doesn't break width */
        }

        /* Green theme for the submit button */
        .btn-submit {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 14px 20px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
            width: 100%;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background-color: #388e3c;
        }

        /* Back to dashboard utility link */
        .back-btn {
            margin: 30px auto;
            display: block;
            padding: 10px 20px;
            text-align: center;
            background-color: #999;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            width: fit-content;
        }

        .back-btn:hover {
            background-color: #777;
        }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'admin') ?>-role">

    <!-- Load the dynamic Javascript Navbar -->
    <script src="js/header2.js"></script>
    
    <!-- Spacer for the fixed header -->
    <div style="height: 100px;"></div>

    <div class="container">
        <h2>Register New Trip Route</h2>
        
        <!-- FORM start: Sending to 'add_route.php' via POST -->
        <form method="POST" id="addRouteForm" onsubmit="return validateForm()">
            <!-- Box 1: Start Location -->
            <div class="form-group">
                <label>From (Source City)</label>
                <input type="text" name="from_location" id="from_location" placeholder="e.g. Nairobi, Kenya" onmouseout="validateFrom()">
            </div>
            
            <!-- Box 2: Destination -->
            <div class="form-group">
                <label>To (Destination City)</label>
                <input type="text" name="to_location" id="to_location" placeholder="e.g. Kampala, Uganda" onmouseout="validateTo()">
            </div>
            
            <!-- Box 3: Traveling Date -->
            <div class="form-group">
                <label>Departure Date</label>
                <input type="text" name="departure_date" id="departure_date" placeholder="YYYY-MM-DD" onmouseout="validateDate()">
            </div>
            
            <!-- Box 4: Traveling Time -->
            <div class="form-group">
                <label>Departure Time</label>
                <input type="text" name="departure_time" id="departure_time" placeholder="HH:MM" onmouseout="validateTime()">
            </div>
            
            <!-- Box 5: Seat Price -->
            <div class="form-group">
                <label>Ticket Cost (KSH)</label>
                <!-- type="text" to avoid browser's built-in number/step validation -->
                <input type="text" name="cost" id="cost" placeholder="99.99" onmouseout="validateCost()">
            </div>
            
            <!-- Box 6: Bus ID -->
            <div class="form-group">
                <label>Assign Bus (System ID)</label>
                <input type="text" name="bus_id" id="bus_id" placeholder="Look up IDs in Bus Fleet Management" onmouseout="validateBusId()">
            </div>
            
            <!-- Action Button -->
            <button type="submit" name="add_route" class="btn-submit">Save Route to Database</button>
        </form>

        <script>
            // Custom JavaScript validation for Admin Add Route Form
            function validateFrom() {
                var val = document.getElementById("from_location").value.trim();
                if (val.length < 3) {
                    alert("Please enter a valid source city (min 3 characters).");
                    document.getElementById("from_location").focus();
                    return false;
                }
                return true;
            }

            function validateTo() {
                var val = document.getElementById("to_location").value.trim();
                if (val.length < 3) {
                    alert("Please enter a valid destination city (min 3 characters).");
                    document.getElementById("to_location").focus();
                    return false;
                }
                return true;
            }

            function validateDate() {
                var val = document.getElementById("departure_date").value.trim();
                var regex = /^\d{4}-\d{2}-\d{2}$/;
                if (!regex.test(val)) {
                    alert("Please enter departure date in YYYY-MM-DD format.");
                    document.getElementById("departure_date").focus();
                    return false;
                }
                return true;
            }

            function validateTime() {
                var val = document.getElementById("departure_time").value.trim();
                var regex = /^([01]\d|2[0-3]):([0-5]\d)$/;
                if (!regex.test(val)) {
                    alert("Please enter departure time in HH:MM (24h) format.");
                    document.getElementById("departure_time").focus();
                    return false;
                }
                return true;
            }


            function validateCost() {
                var val = document.getElementById("cost").value;
                if (val == "" || isNaN(val) || parseFloat(val) <= 0) {
                    alert("Please enter a valid positive cost amount.");
                    document.getElementById("cost").focus();
                    return false;
                }
                return true;
            }

            function validateBusId() {
                var val = document.getElementById("bus_id").value;
                if (val == "" || isNaN(val) || parseInt(val) <= 0) {
                    alert("Please enter a valid numeric Bus System ID.");
                    document.getElementById("bus_id").focus();
                    return false;
                }
                return true;
            }

            function validateForm() {
                if (!validateFrom()) return false;
                if (!validateTo()) return false;
                if (!validateDate()) return false;
                if (!validateTime()) return false;
                if (!validateCost()) return false;
                if (!validateBusId()) return false;
                return true;
            }
        </script>


        <!-- Navigation Button -->
        <a href="dashboard.php" class="back-btn">← Cancel & Back to Dashboard</a>
    </div>

    <!-- Load the dynamic Footer -->
    <script src="js/footer.js"></script>
</body>
</html>
