<?php
/**
 * MANIFEST DATA GENERATOR (get_manifest_data.php)
 * Purpose: This script generates a clean, professional manifest for printing.
 * It combines trip details, bus info, and the list of passengers into one document.
 */

// [1] Include the database connection so we can talk to MySQL.
require_once 'db_connection.php';

// [2] Start the session so we can check who is logged in.
session_start();

// [3] Security Check: Only the ADMIN is allowed to see the official manifest.
// If someone else tries to open this, we stop them immediately.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    die("Access Denied: You do not have permission to view this manifest.");
}

// [4] Check if we have a Route ID. We need this to know which trip to show.
if (!isset($_GET['route_id'])) {
    die("Error: Route ID is missing. Cannot generate manifest.");
}

// [5] Save the Route ID from the URL into a variable.
$route_id = $_GET['route_id'];

/**
 * DATABASE TASK 1: Fetch Trip and Vehicle Metadata
 * We need to know where the bus is going, who is driving, and the bus registration number.
 */
$trip_sql = "SELECT r.*, b.bus_name, b.reg_no, d.full_name as driver_name, d.national_id as driver_id, d.phone as driver_phone
             FROM routes r
             JOIN buses b ON r.bus_id = b.bus_id
             LEFT JOIN drivers d ON b.driver_id = d.driver_id
             WHERE r.route_id = ?";

// [6] Prepare the SQL statement to prevent SQL injection.
$stmt = $conn->prepare($trip_sql);
// [7] Bind the ID as an integer ('i').
$stmt->bind_param("i", $route_id);
// [8] Run the query.
$stmt->execute();
// [9] Get the result and save it as an associative array ($trip).
$trip = $stmt->get_result()->fetch_assoc();
$stmt->close();

// [10] If no trip was found with that ID, show an error.
if (!$trip) {
    die("Error: The requested trip was not found in the system.");
}

/**
 * DATABASE TASK 2: Fetch the Passenger List
 * We need to see everyone who has booked a seat and hasn't cancelled.
 */
$pass_sql = "SELECT passenger_name, passenger_id_number, passenger_age, seat_number, booking_status 
             FROM bookings 
             WHERE route_id = ? AND booking_status != 'CANCELLED'
             ORDER BY CAST(seat_number AS UNSIGNED) ASC";

// [11] Prepare the second query.
$stmt = $conn->prepare($pass_sql);
$stmt->bind_param("i", $route_id);
$stmt->execute();
// [12] Store the list of passengers in the $passengers variable.
$passengers = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Passenger Manifest - Wema Travellers</title>
    <!-- [13] Styling the Manifest for a professional 'Paper' look -->
    <style>
        /* Base page styling */
        body { font-family: 'Segoe UI', Arial, sans-serif; padding: 40px; color: #333; line-height: 1.5; background: #fff; }
        
        /* The header section with the company logo/name */
        .manifest-header { border-bottom: 3px solid #000; padding-bottom: 20px; margin-bottom: 30px; text-align: center; }
        .company-name { font-size: 32px; font-weight: 800; text-transform: uppercase; margin: 0; color: #000; }
        .document-title { font-size: 18px; color: #666; margin: 5px 0 0; letter-spacing: 3px; font-weight: bold; }
        
        /* Grid layout for trip info (Origin, Destination, Bus Details) */
        .trip-details { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f8fafc; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 30px; }
        .detail-item { font-size: 14px; }
        .detail-item strong { color: #000; text-transform: uppercase; font-size: 11px; display: block; margin-bottom: 4px; border-bottom: 1px solid #eee; }
        
        /* Styling the table where passengers are listed */
        .passenger-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; background: #fff; }
        .passenger-table th { background: #f1f5f9; border: 1px solid #000; padding: 12px; text-align: left; font-size: 12px; text-transform: uppercase; color: #000; }
        .passenger-table td { border: 1px solid #000; padding: 12px; font-size: 14px; }
        
        /* Signature boxes at the bottom for verification */
        .footer-signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; margin-top: 60px; }
        .signature-box { border-top: 2px solid #000; padding-top: 10px; text-align: center; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        
        /* Instructions for the printer: don't show the background colors, just the text */
        @media print {
            body { padding: 0; margin: 1cm; }
            .trip-details { background: none !important; border: 1px solid #000; }
            .passenger-table th { background: #eee !important; }
        }
    </style>
</head>
<body>
    <!-- [14] MANIFEST HEADER: Displays company branding -->
    <div class="manifest-header">
        <h1 class="company-name">Wema Travellers Limited</h1>
        <p class="document-title">OFFICIAL PASSENGER MANIFEST</p>
    </div>

    <!-- [15] TRIP SUMMARY: Shows where we are going and who is driving -->
    <div class="trip-details">
        <div class="detail-item">
            <strong>1. Journey Path (From - To)</strong>
            <?= htmlspecialchars($trip['from_location'] ?? '') ?> to <?= htmlspecialchars($trip['to_location'] ?? '') ?>
        </div>
        <div class="detail-item">
            <strong>2. Schedule Information</strong>
            Date of Travel: <?= $trip['departure_date'] ?? '--' ?> | Time: <?= $trip['departure_time'] ?? '--' ?>
        </div>
        <div class="detail-item">
            <strong>3. Transport Asset (Vehicle)</strong>
            Bus Name: <?= htmlspecialchars($trip['bus_name'] ?? 'N/A') ?> | Registration: <?= htmlspecialchars($trip['reg_no'] ?? 'N/A') ?>
        </div>
        <div class="detail-item">
            <strong>4. Command & Crew (Driver)</strong>
            Driver: <?= htmlspecialchars($trip['driver_name'] ?? 'NOT ASSIGNED') ?> (ID: <?= htmlspecialchars($trip['driver_id'] ?? '--') ?>)
        </div>
    </div>

    <!-- [16] PASSENGER LIST: The core data table -->
    <table class="passenger-table">
        <thead>
            <tr>
                <th style="width: 40px;">No.</th>
                <th>Full Passenger Name</th>
                <th>National ID / Passport No.</th>
                <th style="width: 50px;">Age</th>
                <th style="width: 70px;">Seat</th>
                <th>Status / Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $count = 1; // Start counting from 1
            while($p = $passengers->fetch_assoc()): 
            ?>
                <tr>
                    <!-- [17] Row Number -->
                    <td><?= $count++ ?></td>
                    <!-- [18] Passenger Name -->
                    <td><strong><?= htmlspecialchars($p['passenger_name'] ?? '') ?></strong></td>
                    <!-- [19] ID Number (Shows blank lines if empty so they can write by hand) -->
                    <td><?= htmlspecialchars($p['passenger_id_number'] ?? '____________________') ?></td>
                    <!-- [20] Age -->
                    <td><?= $p['passenger_age'] ?? '--' ?></td>
                    <!-- [21] Seat Number -->
                    <td style="text-align: center; font-weight: bold;"><?= $p['seat_number'] ?? '--' ?></td>
                    <!-- [22] Remarks: Shows 'Checked In' if they are verified -->
                    <td style="font-weight: bold; color: #059669;">
                        <?= ($p['booking_status'] === 'CHECKED_IN') ? 'VERIFIED: CHECKED IN' : 'RESERVED' ?>
                    </td>
                </tr>
            <?php endwhile; ?>

            <!-- [23] Empty State: If no one has booked, show a message -->
            <?php if ($passengers->num_rows === 0): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 50px; color: #999; font-style: italic;">
                        Notice: No active passengers are registered for this trip yet.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- [24] SIGNATURE SECTION: For accountability -->
    <div class="footer-signatures">
        <div class="signature-box">Main Driver's Signature & Date</div>
        <div class="signature-box">Station Manager Approval & Stamp</div>
    </div>

    <!-- [25] AUTOMATIC PRINTING: This script triggers the print dialog as soon as the page opens -->
    <script>
        window.onload = function() {
            // This command tells the browser to open the 'Print to PDF' or 'Printer' window.
            window.print();
            
            // Note: You can uncomment the line below if you want the window to close after printing.
            // window.close(); 
        };
    </script>
</body>
</html>

