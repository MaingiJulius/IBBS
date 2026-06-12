<?php
session_start();
require_once 'db_connection.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Security Error: Access denied. Authorized Admins only.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_route'])) {
    $from   = $_POST['from_location'];
    $to     = $_POST['to_location'];
    $date   = $_POST['departure_date'];
    $time   = $_POST['departure_time'];
    $cost   = $_POST['cost'];
    $bus_id = $_POST['bus_id'];
// from injecting malicious code because the machine already knows the
    $stmt->execute();
    header("Location: admin_routes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Route - Wema Travellers</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 0; background-color: #f9f9f9; }
        .container { width: 90%; margin: 50px auto; max-width: 800px; }
        h2 { text-align: center; margin-bottom: 30px; color: #333; }
        form { background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        label { font-weight: 600; display: block; margin-bottom: 8px; color: #555; }
        input { width: 100%; padding: 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box; }
        .btn-submit { background-color: #4CAF50; border: none; color: white; padding: 14px 20px; font-size: 1.1rem; font-weight: bold; border-radius: 6px; cursor: pointer; display: block; margin: 0 auto; width: 100%; transition: background 0.2s; }
        .btn-submit:hover { background-color: #388e3c; }
        .back-btn { margin: 30px auto; display: block; padding: 10px 20px; text-align: center; background-color: #999; color: white; border-radius: 6px; text-decoration: none; width: fit-content; }
        .back-btn:hover { background-color: #777; }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'admin') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="container">
        <h2>Register New Trip Route</h2>
        <form method="POST" id="addRouteForm" onsubmit="return validateForm()">
            <div class="form-group">
                <label>From (Source City)</label>
                <input type="text" name="from_location" id="from_location" placeholder="e.g. Nairobi, Kenya" onmouseout="validateFrom()">
            </div>
            <div class="form-group">
                <label>To (Destination City)</label>
                <input type="text" name="to_location" id="to_location" placeholder="e.g. Kampala, Uganda" onmouseout="validateTo()">
            </div>
            <div class="form-group">
                <label>Departure Date</label>
                <input type="text" name="departure_date" id="departure_date" placeholder="YYYY-MM-DD" onmouseout="validateDate()">
            </div>
            <div class="form-group">
                <label>Departure Time</label>
                <input type="text" name="departure_time" id="departure_time" placeholder="HH:MM" onmouseout="validateTime()">
            </div>
            <div class="form-group">
                <label>Ticket Cost (KSH)</label>
                <input type="text" name="cost" id="cost" placeholder="99.99" onmouseout="validateCost()">
            </div>
            <div class="form-group">
                <label>Assign Bus (System ID)</label>
                <input type="text" name="bus_id" id="bus_id" placeholder="Look up IDs in Bus Fleet Management" onmouseout="validateBusId()">
            </div>
            <button type="submit" name="add_route" class="btn-submit">Save Route to Database</button>
        </form>
        <script>
            function validateFrom() {
                var val = document.getElementById("from_location").value.trim();
                if (val.length < 3) { alert("Please enter a valid source city (min 3 characters)."); document.getElementById("from_location").focus(); return false; }
                return true;
            }
            function validateTo() {
                var val = document.getElementById("to_location").value.trim();
                if (val.length < 3) { alert("Please enter a valid destination city (min 3 characters)."); document.getElementById("to_location").focus(); return false; }
                return true;
            }
            function validateDate() {
                var val = document.getElementById("departure_date").value.trim();
                var regex = /^\d{4}-\d{2}-\d{2}$/;
                if (!regex.test(val)) { alert("Please enter departure date in YYYY-MM-DD format."); document.getElementById("departure_date").focus(); return false; }
                return true;
            }
            function validateTime() {
                var val = document.getElementById("departure_time").value.trim();
                var regex = /^([01]\d|2[0-3]):([0-5]\d)$/;
                if (!regex.test(val)) { alert("Please enter departure time in HH:MM (24h) format."); document.getElementById("departure_time").focus(); return false; }
                return true;
            }
            function validateCost() {
                var val = document.getElementById("cost").value;
                if (val == "" || isNaN(val) || parseFloat(val) <= 0) { alert("Please enter a valid positive cost amount."); document.getElementById("cost").focus(); return false; }
                return true;
            }
            function validateBusId() {
                var val = document.getElementById("bus_id").value;
                if (val == "" || isNaN(val) || parseInt(val) <= 0) { alert("Please enter a valid numeric Bus System ID."); document.getElementById("bus_id").focus(); return false; }
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
        <a href="dashboard.php" class="back-btn">← Cancel & Back to Dashboard</a>
    </div>
    <script src="js/footer.js"></script>
</body>
</html>