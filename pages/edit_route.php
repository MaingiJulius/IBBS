<?php
// This page is for Administrators to EDIT an existing bus route.
// It allows changing things like where the bus starts, ends, and how much it costs.

// Start the session to see who is logged in.
session_start();
// Include the database connection.
require_once 'db_connection.php';

// SECURITY CHECK:
// We only allow users with the 'admin' role to reach this page.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Authorized Admins only.");
}

// We identify which specific route we are editing by the 'id' in the URL.
$route_id = $_GET['id'] ?? null;

if (!$route_id) {
    die("Error: No route ID was provided for editing.");
}

// ACTION: This part runs when the Admin clicks the "Update Route" button (Form Submission).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_route'])) {
    // Collect the updated information from the input boxes.
    $from = $_POST['from_location'];
    $to = $_POST['to_location'];
    $date = $_POST['departure_date'];
    $time = $_POST['departure_time'];
    $cost = $_POST['cost'];
    $bus_id = $_POST['bus_id'];

    // We use a standard SQL UPDATE command to fix the route details.
    // UPDATED: Replaced missing stored procedure with direct SQL.
    $sql = "UPDATE routes SET from_location=?, to_location=?, departure_date=?, departure_time=?, cost=?, bus_id=? WHERE route_id=?";
    $stmt = $conn->prepare($sql);
    
    // "ssssdii" = string, string, string, string, double, int, int
    $stmt->bind_param("ssssdii", $from, $to, $date, $time, $cost, $bus_id, $route_id);
    $stmt->execute();
    $stmt->close();

    // After updating, send the user back to the routes management page.
    header("Location: admin_routes.php");
    exit();
}

// --- PRE-FILL DATA ---
// We fetch the current information for this route so it shows up in the form boxes.
$stmt = $conn->prepare("SELECT * FROM routes WHERE route_id = ?");
$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();
$route = $result->fetch_assoc();
$stmt->close();

if (!$route) {
    die("Route not found in our records.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Route</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 50px auto;
            max-width: 800px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            background: #f4f4f4;
            padding: 30px;
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn-submit {
            background-color: #2196F3;
            border: none;
            color: white;
            padding: 12px 20px;
            font-size: 1rem;
            border-radius: 6px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
        }

        .btn-submit:hover {
            background-color: #1565C0;
        }

        .back-btn {
            margin: 30px auto;
            display: block;
            padding: 10px 20px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            width: fit-content;
        }

        .back-btn:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>

<script src="js/header2.js"></script>

<div class="container">
    <h2>Edit Route</h2>
    <form method="POST">
        <div class="form-group">
            <label>From Location</label>
            <input type="text" name="from_location" value="<?= htmlspecialchars($route['from_location']) ?>" required>
        </div>
        <div class="form-group">
            <label>To Location</label>
            <input type="text" name="to_location" value="<?= htmlspecialchars($route['to_location']) ?>" required>
        </div>
        <div class="form-group">
            <label>Departure Date</label>
            <input type="date" name="departure_date" value="<?= $route['departure_date'] ?>" required>
        </div>
        <div class="form-group">
            <label>Departure Time</label>
            <input type="time" name="departure_time" value="<?= $route['time'] ?>" required>
        </div>
        <div class="form-group">
            <label>Cost</label>
            <input type="number" step="0.01" name="cost" value="<?= $route['cost'] ?>" required>
        </div>
        <div class="form-group">
            <label>Bus ID</label>
            <input type="number" name="bus_id" value="<?= $route['bus_id'] ?>" required>
        </div>
        <button type="submit" name="edit_route" class="btn-submit">Update Route</button>
    </form>

    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

<script src="js/footer.js"></script>
</body>
</html>
