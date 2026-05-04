<?php
/**
 * PASSENGER MANIFESTS (admin_manifests.php)
 * Purpose: Cross-border compliance reporting.
 * Features: Grouping by country, filtering, and professional PDF-ready printing.
 */

require_once 'db_connection.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.html");
    exit();
}

$filter_country = isset($_GET['country']) ? $_GET['country'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

// Fetch distinct countries from to_location for the filter dropdown
$countries_sql = "SELECT DISTINCT SUBSTRING_INDEX(to_location, ', ', -1) as country FROM routes ORDER BY country ASC";
$countries_res = $conn->query($countries_sql);

// Main query to fetch routes/trips with booking counts
$sql = "SELECT r.*, b.bus_name, b.reg_no, d.full_name as driver_name, d.national_id as driver_id,
        SUBSTRING_INDEX(r.to_location, ', ', -1) as dest_country,
        (SELECT COUNT(*) FROM bookings WHERE route_id = r.route_id AND booking_status != 'CANCELLED') as passenger_count
        FROM routes r
        JOIN buses b ON r.bus_id = b.bus_id
        LEFT JOIN drivers d ON b.driver_id = d.driver_id
        WHERE 1=1";

if ($filter_country) {
    $sql .= " AND r.to_location LIKE '%" . $conn->real_escape_string($filter_country) . "%'";
}
if ($filter_date) {
    $sql .= " AND r.departure_date = '" . $conn->real_escape_string($filter_date) . "'";
}

$sql .= " ORDER BY dest_country ASC, r.departure_date ASC";
$trips_res = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passenger Manifests - IBBS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .manifest-container { max-width: 1200px; margin: 30px auto; padding: 20px; }
        .filter-section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; gap: 20px; align-items: flex-end; }
        .filter-group { flex: 1; }
        .trip-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; }
        .trip-card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #edf2f7; position: relative; overflow: hidden; }
        .trip-card::before { content: ''; position: absolute; top: 0; left: 0; width: 6px; height: 100%; background: var(--purple); }
        .country-badge { background: #ebf8ff; color: #2c5282; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; display: inline-block; }
        .trip-info { margin: 15px 0; font-size: 0.9rem; color: #4a5568; }
        .trip-info strong { color: #2d3748; }
        .manifest-btn { background: var(--purple); color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: bold; cursor: pointer; transition: all 0.2s; margin-top: 15px; }
        .manifest-btn:hover { background: #7e3e7e; transform: translateY(-2px); }
        .no-results { grid-column: 1 / -1; text-align: center; padding: 60px; background: white; border-radius: 12px; color: #a0aec0; }
        
        /* Print Staging */
        #manifest-print-staging { display: none; }
    </style>
</head>
<body class="admin-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <div class="manifest-container">
        <h2 style="color: var(--purple);">📋 Cross-Border Passenger Manifests</h2>
        <p style="color: #718096; margin-bottom: 30px;">Select a scheduled trip to generate the official passenger manifest for border compliance.</p>

        <!-- Filter Form -->
        <form class="filter-section" method="GET">
            <div class="filter-group">
                <label>Destination Country</label>
                <select name="country" class="input">
                    <option value="">All Countries</option>
                    <?php while($c = $countries_res->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($c['country']) ?>" <?= $filter_country == $c['country'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['country']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Departure Date</label>
                <input type="date" name="date" class="input" value="<?= htmlspecialchars($filter_date) ?>">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="button regular-button pink-background" style="width: auto; padding: 12px 25px;">Filter Results</button>
                <a href="admin_manifests.php" class="button regular-button" style="width: auto; padding: 12px 25px; background: #e2e8f0; color: #4a5568;">Reset</a>
            </div>
        </form>

        <!-- Trip Results Grid -->
        <div class="trip-grid">
            <?php if ($trips_res->num_rows > 0): ?>
                <?php while($trip = $trips_res->fetch_assoc()): ?>
                    <div class="trip-card">
                        <span class="country-badge">🌍 <?= htmlspecialchars($trip['dest_country']) ?></span>
                        <h3 style="margin: 5px 0; color: var(--purple);"><?= htmlspecialchars($trip['from_location']) ?> → <?= htmlspecialchars($trip['to_location']) ?></h3>
                        
                        <div class="trip-info">
                            <p>📅 <strong>Date:</strong> <?= $trip['departure_date'] ?? '--' ?> at <?= $trip['departure_time'] ?? '--' ?></p>
                            <p>🚌 <strong>Bus:</strong> <?= htmlspecialchars($trip['bus_name'] ?? 'N/A') ?> (<?= htmlspecialchars($trip['reg_no'] ?? 'N/A') ?>)</p>
                            <p>👨‍✈️ <strong>Driver:</strong> <?= htmlspecialchars($trip['driver_name'] ?? 'Not Assigned') ?></p>
                            <p>👥 <strong>Passengers:</strong> <?= $trip['passenger_count'] ?> Booked</p>
                        </div>

                        <button class="manifest-btn" onclick="generateManifest(<?= $trip['route_id'] ?>)">
                            🖨️ Generate Official Manifest
                        </button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <h3>No scheduled trips found matching your criteria.</h3>
                    <p>Try adjusting your filters or check the Route Management section.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- AJAX Staging for Print -->
    <div id="manifest-print-staging"></div>

    <script>
        function generateManifest(routeId) {
            // Fetch passenger data via AJAX (we'll use a hidden iframe or simple fetch)
            fetch(`get_manifest_data.php?route_id=${routeId}`)
                .then(response => response.text())
                .then(html => {
                    const printWindow = window.open('', '_blank', 'width=1000,height=800');
                    printWindow.document.write(html);
                    printWindow.document.close();
                })
                .catch(err => alert('Error generating manifest: ' + err));
        }
    </script>

    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
