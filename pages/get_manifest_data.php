<?php
require_once 'db_connection.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    die("Unauthorized");
}

if (!isset($_GET['route_id'])) {
    die("Route ID required");
}

$route_id = intval($_GET['route_id']);

// 1. Fetch Trip Metadata
$trip_sql = "SELECT r.*, b.bus_name, b.reg_no, d.full_name as driver_name, d.national_id as driver_id, d.phone as driver_phone
             FROM routes r
             JOIN buses b ON r.bus_id = b.bus_id
             LEFT JOIN drivers d ON b.driver_id = d.driver_id
             WHERE r.route_id = $route_id";
$res_t = mysqli_query($conn, $trip_sql);
$trip = mysqli_fetch_assoc($res_t);

if (!$trip) {
    die("Trip not found");
}

// 2. Fetch Passengers
$pass_sql = "SELECT passenger_name, passenger_id_number, passenger_age, seat_number, booking_status
             FROM bookings
             WHERE route_id = $route_id AND booking_status != 'CANCELLED'
             ORDER BY seat_number ASC";
$passengers = mysqli_query($conn, $pass_sql) or die(mysqli_error($conn));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Official Passenger Manifest - IBBS</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; padding: 40px; color: #333; line-height: 1.5; }
        .manifest-header { border-bottom: 3px solid #000; padding-bottom: 20px; margin-bottom: 30px; text-align: center; }
        .company-name { font-size: 28px; font-weight: 800; text-transform: uppercase; margin: 0; }
        .document-title { font-size: 18px; color: #666; margin: 5px 0 0; letter-spacing: 2px; }

        .trip-details { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f8fafc; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 30px; }
        .detail-item { font-size: 14px; }
        .detail-item strong { color: #000; text-transform: uppercase; font-size: 12px; display: block; margin-bottom: 2px; }

        .passenger-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .passenger-table th { background: #f1f5f9; border: 1px solid #cbd5e0; padding: 12px; text-align: left; font-size: 12px; text-transform: uppercase; }
        .passenger-table td { border: 1px solid #cbd5e0; padding: 12px; font-size: 14px; }
        .passenger-table tr:nth-child(even) { background: #fdfdfd; }

        .footer-signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; margin-top: 50px; }
        .signature-box { border-top: 1px solid #000; padding-top: 10px; text-align: center; font-size: 12px; font-weight: bold; text-transform: uppercase; }

        @media print {
            body { padding: 0; margin: 1cm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="manifest-header">
        <h1 class="company-name">Wema Travellers Limited</h1>
        <p class="document-title">OFFICIAL CROSS-BORDER PASSENGER MANIFEST</p>
    </div>

    <div class="trip-details">
        <div class="detail-item">
            <strong>Origin - Destination</strong>
            <?= htmlspecialchars($trip['from_location'] ?? '') ?> to <?= htmlspecialchars($trip['to_location'] ?? '') ?>
        </div>
        <div class="detail-item">
            <strong>Schedule Info</strong>
            Date: <?= $trip['departure_date'] ?? '--' ?> | Time: <?= $trip['departure_time'] ?? '--' ?>
        </div>
        <div class="detail-item">
            <strong>Vehicle Details</strong>
            Bus: <?= htmlspecialchars($trip['bus_name'] ?? 'N/A') ?> | Reg No: <?= htmlspecialchars($trip['reg_no'] ?? 'N/A') ?>
        </div>
        <div class="detail-item">
            <strong>Crew Assigned</strong>
            Driver: <?= htmlspecialchars($trip['driver_name'] ?? 'Not Assigned') ?> (ID: <?= htmlspecialchars($trip['driver_id'] ?? '--') ?>)
        </div>
    </div>

    <table class="passenger-table">
        <thead>
            <tr>
                <th style="width: 50px;">No.</th>
                <th>Passenger Full Name</th>
                <th>National ID / Passport No.</th>
                <th style="width: 60px;">Age</th>
                <th style="width: 80px;">Seat No.</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php $count = 1; while($p = mysqli_fetch_assoc($passengers)): ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><strong><?= htmlspecialchars($p['passenger_name'] ?? '') ?></strong></td>
                    <td><?= htmlspecialchars($p['passenger_id_number'] ?? '____________________') ?></td>
                    <td><?= $p['passenger_age'] ?? '--' ?></td>
                    <td style="text-align: center; font-weight: bold;"><?= $p['seat_number'] ?? '--' ?></td>
                    <td style="font-weight: bold; color: #059669;"><?= ($p['booking_status'] === 'CHECKED_IN') ? 'Checked In' : '' ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($passengers) === 0): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #999;">No passengers found for this trip.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer-signatures">
        <div class="signature-box">Driver's Signature &amp; Date</div>
        <div class="signature-box">Station Manager / Border Official</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>