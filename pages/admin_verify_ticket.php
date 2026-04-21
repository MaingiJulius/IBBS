<?php
/**
 * TICKET VERIFICATION CENTER (admin_verify_ticket.php)
 * Purpose: Staff tool for real-time boarding validation using Booking IDs.
 */

require_once 'db_connection.php';
session_start();

// Security: Only Admins and Agents can verify tickets
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'ADMIN' && $_SESSION['role'] !== 'AGENT')) {
    header("Location: login.html");
    exit();
}

$search_id = $_GET['search_id'] ?? '';
$ticket = null;
$error = '';
$success_msg = '';

// [1] Handle the 'Confirm Boarding' action when staff clicks the verification button.
if (isset($_POST['confirm_boarding'])) {
    $bid = $_POST['booking_id']; // [2] Capture the primary key of the specific booking to update.
    // [3] Prepare SQL to update the status from 'PAID' to 'CHECKED_IN'.
    $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'CHECKED_IN' WHERE booking_id = ?");
    $stmt->bind_param("i", $bid); // [4] Bind the booking ID as an integer.
    if ($stmt->execute()) {
        $success_msg = "Passenger Successfully Checked-In. Boarding Authorized."; // [5] Success feedback for staff.
    }
    $stmt->close(); // [6] Close the update statement.
}

// [7] Handle the 'Search' action when staff enters an ID/Passport number.
if ($search_id) {
    // [8] Define the lookup query: JOIN bookings with routes, buses, and users.
    $sql = "SELECT b.*, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name, u.first_name, u.last_name 
            FROM bookings b 
            JOIN routes r ON b.route_id = r.route_id 
            JOIN buses bs ON b.bus_id = bs.bus_id 
            JOIN users u ON b.user_id = u.user_id
            WHERE b.passenger_id_number = ?"; // [9] Filter results by the specific passenger Document ID.
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_id); // [10] Bind the search string securely.
    $stmt->execute();
    $ticket = $stmt->get_result()->fetch_assoc(); // [11] Retrieve the single matching record.
    $stmt->close();
    if (!$ticket) {
        $error = "No active booking found for ID Document #$search_id."; // [12] Error if no match exists.
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verification Center - Wema Travellers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .verify-container { max-width: 600px; margin: 40px auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
        .search-box { display: flex; gap: 10px; margin-bottom: 30px; }
        .search-box input { flex: 1; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1.1rem; outline: none; transition: border-color 0.3s; }
        .search-box input:focus { border-color: var(--purple); }
        .result-card { border: 2px solid #e2e8f0; border-radius: 16px; padding: 25px; margin-top: 20px; }
        .result-card.verified { border-color: #10b981; background: #f0fdf4; }
        .result-card.pending { border-color: #f59e0b; background: #fffbeb; }
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; }
        .status-PAID { background: #dcfce7; color: #166534; }
        .status-CHECKED_IN { background: #dbeafe; color: #1e40af; }
        .msg { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; text-align: center; }
        .error { background: #fee2e2; color: #991b1b; }
        .success { background: #dcfce7; color: #166534; }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <div class="verify-container">
        <h2 style="color: var(--purple); text-align: center; margin-bottom: 10px;">Boarding Verification</h2>
        <p style="text-align: center; color: #64748b; margin-bottom: 30px;">Enter the Passenger's National ID or Passport Number to validate boarding.</p>

        <?php if ($error): ?> <div class="msg error"><?= $error ?></div> <?php endif; ?>
        <?php if ($success_msg): ?> <div class="msg success"><?= $success_msg ?></div> <?php endif; ?>

        <form action="" method="GET" class="search-box" id="verifyForm" onsubmit="return validateSearch()">
            <input type="text" name="search_id" id="search_id" placeholder="National ID / Passport No." value="<?= htmlspecialchars($search_id) ?>" onmouseout="validateSearchField()">
            <button type="submit" class="button regular-button pink-background" style="margin:0; width: auto;">Search</button>
        </form>

        <script>
            // Custom JS Validation for Ticket Verification Search
            function validateSearchField() {
                var val = document.getElementById("search_id").value.trim();
                if (val.length < 5) {
                    alert("Please enter a valid National ID or Passport Number (min 5 characters).");
                    document.getElementById("search_id").focus();
                    return false;
                }
                return true;
            }

            function validateSearch() {
                return validateSearchField();
            }
        </script>


        <?php if ($ticket): ?>
            <div class="result-card <?= $ticket['booking_status'] === 'CHECKED_IN' ? 'verified' : 'pending' ?>">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin:0; color: #1e293b;"><?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?></h3>
                        <p style="color: #64748b; margin: 5px 0;">Seat: <strong><?= $ticket['seat_number'] ?></strong> | Bus: <?= htmlspecialchars($ticket['bus_name']) ?></p>
                    </div>
                    <span class="status-badge status-<?= $ticket['booking_status'] ?>"><?= $ticket['booking_status'] ?></span>
                </div>
                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 15px 0;">
                <p style="color: #475569;"><strong>Route:</strong> <?= htmlspecialchars($ticket['from_location']) ?> → <?= htmlspecialchars($ticket['to_location']) ?></p>
                <p style="color: #475569;"><strong>Departure:</strong> <?= $ticket['departure_date'] ?> at <?= $ticket['departure_time'] ?></p>
                
                <?php if ($ticket['booking_status'] === 'PAID'): ?>
                    <form action="" method="POST" style="margin-top: 20px;">
                        <input type="hidden" name="booking_id" value="<?= $ticket['booking_id'] ?>">
                        <button type="submit" name="confirm_boarding" class="button regular-button green-background" style="width: 100%; font-weight: 800; font-size: 1.1rem;">✅ Confirm Boarding</button>
                    </form>
                <?php elseif ($ticket['booking_status'] === 'CHECKED_IN'): ?>
                    <div style="text-align: center; color: #059669; font-weight: 800; margin-top: 20px;">
                        PASSENGER ALREADY BOARDED
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
