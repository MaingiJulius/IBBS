<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.html");
    exit();
}
$filter_country = isset($_GET['country']) ? $_GET['country'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$sql_c = "SELECT DISTINCT SUBSTRING_INDEX(to_location, ', ', -1) as country FROM routes ORDER BY country ASC";
$res_c = mysqli_query($conn, $sql_c);
$sql_t = "SELECT r.*, b.bus_name, b.reg_no, d.full_name as driver_name, (SELECT COUNT(*) FROM bookings WHERE route_id = r.route_id AND booking_status != 'CANCELLED') as passenger_count FROM routes r JOIN buses b ON r.bus_id = b.bus_id LEFT JOIN drivers d ON b.driver_id = d.driver_id WHERE 1=1";
if ($filter_country) { $sql_t .= " AND r.to_location LIKE '%" . mysqli_real_escape_string($conn, $filter_country) . "%'"; }
if ($filter_date) { $sql_t .= " AND r.departure_date = '" . mysqli_real_escape_string($conn, $filter_date) . "'"; }
$sql_t .= " ORDER BY r.departure_date ASC";
$res_t = mysqli_query($conn, $sql_t);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passenger Manifests - IBBS</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .manifest-container { max-width: 1200px; margin: 30px auto; }
        .filter-section { background: white; padding: 20px; border-radius: 10px; display: flex; gap: 10px; align-items: flex-end; }
        .trip-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px; }
        .trip-card { background: white; border-radius: 10px; padding: 20px; border-left: 5px solid var(--purple); }
    </style>
</head>
<body class="admin-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="manifest-container">
        <h2 style="color: var(--purple);">📋 Passenger Manifests</h2>
        <form class="filter-section" method="GET">
            <select name="country" class="input">
                <option value="">All Countries</option>
                <?php while($c = mysqli_fetch_assoc($res_c)): ?>
                    <option value="<?= $c['country'] ?>" <?= $filter_country == $c['country'] ? 'selected' : '' ?>><?= $c['country'] ?></option>
                <?php endwhile; ?>
            </select>
            <input type="date" name="date" class="input" value="<?= $filter_date ?>">
            <button type="submit" class="button pink-background">Filter Trips</button>
        </form>
        <div class="trip-grid">
            <?php if (mysqli_num_rows($res_t) > 0): ?>
                <?php while($trip = mysqli_fetch_assoc($res_t)): ?>
                    <div class="trip-card">
                        <h3><?= htmlspecialchars($trip['from_location']) ?> → <?= htmlspecialchars($trip['to_location']) ?></h3>
                        <p>Date: <?= $trip['departure_date'] ?> | Bus: <?= htmlspecialchars($trip['bus_name']) ?></p>
                        <p>Passengers: <?= $trip['passenger_count'] ?></p>
                        <button class="button" style="background:var(--purple); color:white;" onclick="window.open('get_manifest_data.php?route_id=<?= $trip['route_id'] ?>', '_blank')">View & Print Manifest</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No trips found matching filters.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="js/footer.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>