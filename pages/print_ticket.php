<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'PASSENGER';
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
if ($booking_id <= 0) {
    die("Error: Invalid Booking ID.");
}
$sql = "SELECT b.*, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name
        FROM bookings b
        JOIN routes r ON b.route_id = r.route_id
        JOIN buses bs ON b.bus_id = bs.bus_id
        WHERE b.booking_id = $booking_id";
$result = mysqli_query($conn, $sql);
$ticket = mysqli_fetch_assoc($result);
if (!$ticket) {
    die("Error: Ticket not found.");
}
if ($role === 'PASSENGER' && $ticket['user_id'] != $user_id) {
    die("Access Denied: You do not own this ticket.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wema Travellers - Boarding Pass</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f6ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
            margin: 0;
        }
        .no-print {
            background: #fff;
            border: 2px dashed #8e44ad;
            padding: 15px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            max-width: 620px;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            color: #2d3748;
            font-weight: bold;
        }
        .back-link {
            display: inline-block;
            margin-top: 10px;
            color: #8e44ad;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .wrapper {
            border: 4px solid #8e44ad;
            border-radius: 16px;
            max-width: 620px;
            width: 100%;
            background: white;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .t-header {
            background: #8e44ad;
            color: white;
            padding: 20px 30px;
            text-align: center;
        }
        .t-header h1 {
            margin: 0;
            font-size: 1.8rem;
            letter-spacing: 2px;
        }
        .t-header p {
            margin: 4px 0 0;
            font-size: 0.85rem;
            opacity: 0.85;
            letter-spacing: 1px;
        }
        .t-body {
            padding: 25px 30px;
        }
        .t-body p {
            margin: 8px 0;
            font-size: 0.95rem;
            color: #2d3748;
            line-height: 1.5;
        }
        .t-body strong {
            color: #1a202c;
        }
        .booking-id-badge {
            background: #8e44ad;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }
        .ticket-details-print {
            margin-top: 15px;
            background: #fcfaff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2d9f3;
        }
        hr {
            border: 0;
            border-top: 2px dashed #e2d9f3;
            margin: 15px 0;
        }
        .t-footer {
            background: #f3f0fa;
            padding: 12px 30px;
            font-size: 0.78rem;
            color: #718096;
            text-align: center;
            border-top: 1px solid #e2d9f3;
        }
        @media print {
               @media print: CSS conditional print media query selector.
            @page {
                margin: 0;
            }
            body {
                margin: 1.5cm;
                background: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .wrapper {
                box-shadow: none;
                border: 4px solid #8e44ad;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="background:#8e44ad; color:white; border:none; padding:12px 30px; border-radius:8px; font-size:1rem; font-weight:bold; cursor:pointer; margin-bottom:10px;">🖨️ Print This Boarding Pass</button>
        <br>
        <a href="view_tickets.php" class="back-link">← Return to Wallet</a>
    </div>
    <div class="wrapper">
        <div class="t-header">
            <h1>&#x1F6A6; WEMA TRAVELLERS</h1>
            <p>OFFICIAL BOARDING PASS</p>
        </div>
        <div class="t-body">
            <div class="booking-id-badge">Booking ID: #<?php echo $ticket['booking_id']; ?></div>
            <p><strong>Route:</strong> <?php echo htmlspecialchars($ticket['from_location']); ?> → <?php echo htmlspecialchars($ticket['to_location']); ?></p>
            <p><strong>📅 Date:</strong> <?php echo $ticket['departure_date']; ?> | <strong>⏰ Time:</strong> <?php echo $ticket['departure_time']; ?></p>
            <p><strong>💺 Seat:</strong> <?php echo $ticket['seat_number']; ?> | <strong>🚌 Bus:</strong> <?php echo htmlspecialchars($ticket['bus_name']); ?></p>
            <hr>
            <div class="ticket-details-print">
                <p><strong>👤 Traveler Name:</strong> <?php echo htmlspecialchars($ticket['passenger_name'] ?: '—'); ?></p>
                <p><strong>🎂 Age:</strong> <?php echo htmlspecialchars($ticket['passenger_age'] > 0 ? $ticket['passenger_age'] . ' years' : '—'); ?></p>
                <p><strong>🆔 ID/Passport:</strong> <?php echo htmlspecialchars($ticket['passenger_id_number'] ?: '—'); ?></p>
            </div>
        </div>
        <div class="t-footer">
            Please arrive 30 mins before departure &bull; Valid Government-Issued ID Required &bull; Non-Transferable
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>