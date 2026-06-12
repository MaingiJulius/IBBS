<?php
session_start();
require_once 'db_connection.php';
// --- [A] SECURITY GATEWAY: ROLE VALIDATION ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    die("Access denied. Authorized Admins only.");
}
/* --- [B] DATA ACQUISITION: FETCH TARGET IDENTIFIER --- */         // [17] Marker for identifying which record is being modified.
$route_id = $_GET['id'] ?? null;                                     // [18] Retrieve the 'id' from the URL query string using Null Coalescing.
if (!$route_id) {
    die("Error: No route ID was provided for editing.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_route'])) {
    $from = $_POST['from_location'];
    $to = $_POST['to_location'];
    $date = $_POST['departure_date'];
    $time = $_POST['departure_time'];
    $cost = $_POST['cost'];
    $bus_id = $_POST['bus_id'];
    $sql = "UPDATE routes SET from_location=?, to_location=?, departure_date=?, departure_time=?, cost=?, bus_id=? WHERE route_id=?";
// "ssssdii" = 4 Strings, 1 Double/Decimal, 2 Integers.          // [35] Explaining the type definition string for bind_param.
    mysqli_query($conn,$sql);
    header("Location: admin_routes.php");
    exit();                                                          // [40] Halt further script execution to ensure redirect behavior.
}
$result = mysqli_query($conn, "SELECT * FROM routes WHERE route_id = $route_id");
$route = mysqli_fetch_assoc($result);
if (!$route) {                                                       // [49] Check if the database actually returned a valid record.
    die("Route not found in our records.");                          // [50] Terminate if the provided ID doesn't exist in the table.
}
?>
<!DOCTYPE html>                                                         <!-- [53] Define the document type as standard HTML5. -->
<html lang="en">                                                     <!-- [54] Root element defining the content language as English. -->
<head>                                                               <!-- [55] Head section containing non-visible document metadata. -->
    <meta charset="UTF-8">                                           <!-- [56] Declare UTF-8 character encoding for global data support. -->
    <title>Edit Route</title>                                        <!-- [57] Website title displayed in the browser tab. -->
    <link rel="stylesheet" href="css/style.css">
    <style>
    </style>                                                         <!-- [125] Terminate internal CSS block. -->
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'admin') ?>-role">
<script src="js/header2.js"></script>
<div style="height: 100px;"></div>
<div class="container">
    <h2>Edit Route</h2>
    <form method="POST" id="editRouteForm" onsubmit="return validateForm()">
        <div class="form-group">
            <label>From Location</label>
            <input type="text" name="from_location" id="from_location" value="<?= htmlspecialchars($route['from_location']) ?>" onmouseout="validateFrom()">
        </div>
        <div class="form-group">                                     <!-- [136] Group container for the 'Destination' input. -->
            <label>To Location</label>                               <!-- [137] Textual label identifying the arrival city field. -->
            <input type="text" name="to_location" id="to_location" value="<?= htmlspecialchars($route['to_location']) ?>" onmouseout="validateTo()"> <!-- [138] Text input pre-filled with existing arrival data (XSS protected). -->
        </div>
        <div class="form-group">                                     <!-- [140] Group container for the 'Departure Date' input. -->
            <label>Departure Date</label>
            <input type="text" name="departure_date" id="departure_date" value="<?= $route['departure_date'] ?>" placeholder="YYYY-MM-DD" onmouseout="validateDate()"> <!-- [142] Date input. -->
        </div>
        <div class="form-group">                                     <!-- [144] Group container for the 'Departure Time' input. -->
            <label>Departure Time</label>                            <!-- [145] Textual label for the clock-time scheduling field. -->
            <input type="text" name="departure_time" id="departure_time" value="<?= $route['departure_time'] ?>" placeholder="HH:MM" onmouseout="validateTime()"> <!-- [146] Time input. -->
        </div>
        <div class="form-group">
            <label>Cost</label>                                      <!-- [149] Textual label for the pricing adjustment field. -->
            <input type="text" name="cost" id="cost" value="<?= $route['cost'] ?>" onmouseout="validateCost()"> <!-- [150] Text input for fare value to avoid browser validation. -->
        </div>
        <div class="form-group">
            <label>Bus ID</label>                                    <!-- [153] Textual label for the vehicle association field. -->
            <input type="text" name="bus_id" id="bus_id" value="<?= $route['bus_id'] ?>" onmouseout="validateBusId()"> <!-- [154] Text input for the specific vehicle ID. -->
        </div>
        <button type="submit" name="edit_route" class="btn-submit">Update Route</button> <!-- [156] Finalize submission button to trigger the POST action. -->
    </form>
    <script>
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
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
<script src="js/footer.js"></script>
</body>
</html>                                                              <!-- [162] Terminate the HTML document. -->