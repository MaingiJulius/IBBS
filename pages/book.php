<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];
$is_staff = ($role === 'ADMIN' || $role === 'AGENT');
$sql_available = "SELECT r.*, b.bus_name, b.max_passengers,
    (SELECT COUNT(*) FROM bookings WHERE route_id = r.route_id AND booking_status != 'CANCELLED') as booked_seats
    FROM routes r JOIN buses b ON r.bus_id = b.bus_id
    WHERE r.departure_date >= CURDATE()
    AND r.route_id NOT IN (SELECT route_id FROM bookings WHERE user_id = $user_id AND booking_status != 'CANCELLED')
    ORDER BY r.departure_date ASC";
$result_available = mysqli_query($conn, $sql_available);
$passengers = [];
if ($is_staff) {
    $pass_res = mysqli_query($conn, "SELECT user_id, first_name, last_name, email FROM users WHERE role = 'PASSENGER' ORDER BY first_name");
    while($p = mysqli_fetch_assoc($pass_res)) {
        $passengers[] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Journey - Wema Travellers</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .booking-container { max-width: 1200px; margin: 40px auto; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .staff-panel { background: #f0f4ff; border: 2px solid var(--purple); padding: 25px; border-radius: 12px; margin-bottom: 40px; }
        .passenger-select { width: 100%; max-width: 500px; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1.1em; background: white; }
        .crud-table { width: 100%; border-collapse: collapse; }
        .crud-table th, .crud-table td { padding: 18px; border-bottom: 1px solid #f1f5f9; text-align: left; }
        .crud-table th { background-color: #f8fafc; color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; }
        #seat-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; overflow-y: auto; }
        .seat-content { background-color: #ffffff; margin: 3% auto; padding: 40px; border-radius: 20px; width: 90%; max-width: 850px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .bus-layout { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; background: #f1f5f9; padding: 25px; border-radius: 18px; border: 1px solid #e2e8f0; margin: 30px auto; max-width: 450px; }
        .seat { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 0.85em; font-weight: 700; cursor: pointer; transition: all 0.2s ease; border: 1px solid #cbd5e1; background: white; color: #475569; }
        .seat.available:hover { border-color: #22c55e; background-color: #f0fdf4; transform: scale(1.05); }
        .seat.occupied { background-color: #ef4444; color: white; border-color: #dc2626; cursor: not-allowed; }
        .seat.selected { background-color: #22c55e; color: white; border-color: #15803d; box-shadow: 0 0 15px rgba(34, 197, 94, 0.4); }
        .passenger-details-section { text-align: left; margin-top: 40px; padding-top: 30px; border-top: 1px solid #e2e8f0; display: none; }
        .passenger-info-card { background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 20px; border-left: 6px solid var(--purple); }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .info-group label { display: block; font-size: 0.8em; font-weight: 600; color: #64748b; margin-bottom: 8px; }
        .info-group input { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: white; font-size: 0.95em; }
        .legend { display: flex; justify-content: center; gap: 25px; margin: 25px 0; font-size: 0.85em; color: #64748b; }
        .legend-item { display: flex; align-items: center; gap: 8px; }
        .box { width: 18px; height: 18px; border-radius: 4px; border: 1px solid #e2e8f0; }
        .aisle-spacer { grid-column: span 1; visibility: hidden; }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'passenger') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="booking-container">
        <h2 style="color: var(--purple); margin-bottom: 30px; font-weight: 800;">🎫 Trip Reservation Engine</h2>
        <?php if ($is_staff): ?>
        <div class="staff-panel">
            <label for="target_user_id">Representative Control (Agent/Admin)</label>
            <select id="target_user_id" class="passenger-select">
                <option value="<?= $user_id ?>">Agent Action: (<?= htmlspecialchars($_SESSION['name']) ?>)</option>
                <optgroup label="Select Authorized Passenger">
                    <?php foreach($passengers as $p): ?>
                        <option value="<?= $p['user_id'] ?>"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?> | <?= htmlspecialchars($p['email']) ?></option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
            <p style="margin-top: 15px; font-size: 0.85em; color: #475569;">Need a profile? <a href="view_users_sorted.php" style="color:var(--purple); font-weight:700;">Create One →</a></p>
        </div>
        <?php else: ?>
            <input type="hidden" id="target_user_id" value="<?= $user_id ?>">
        <?php endif; ?>
        <div class="table-container">
            <table class="crud-table">
                <thead>
                    <tr><th>Destination</th><th>Departure</th><th>Vehicle</th><th>Cost (KES)</th><th>Availability</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php
                    mysqli_data_seek($result_available, 0);
                    while ($row = mysqli_fetch_assoc($result_available)):
                        $remaining = $row['max_passengers'] - $row['booked_seats'];
                        $is_full = ($remaining <= 0);
                    ?>
                    <tr>
                        <td><div style="font-weight:700; color:#1e293b;"><?= htmlspecialchars($row['from_location']) ?></div><div style="font-size:0.85em; color:#64748b;">to <?= htmlspecialchars($row['to_location']) ?></div></td>
                        <td><div style="font-weight:600;"><?= $row['departure_date'] ?></div><div style="font-size:0.85em; font-family:monospace; color:var(--purple);"><?= $row['departure_time'] ?></div></td>
                        <td><?= htmlspecialchars($row['bus_name']) ?></td>
                        <td style="font-weight:700; color:#1e293b;"><?= number_format($row['cost'], 2) ?></td>
                        <td><?= $is_full ? '<span style="color:#ef4444; font-weight:800;">FULL</span>' : '<span style="color:#10b981; font-weight:800;">' . $remaining . ' OPEN</span>' ?></td>
                        <td><?= !$is_full ? '<button type="button" class="button regular-button pink-background" onclick="openSeatMap(' . $row['route_id'] . ',' . $row['max_passengers'] . ')">Reserve</button>' : '<button disabled class="button regular-button" style="opacity:0.3;">Sold Out</button>' ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="seat-modal">
        <div class="seat-content">
            <h3 style="margin-top:0;">Virtual Seating Deck</h3>
            <div class="legend">
                <div class="legend-item"><div class="box" style="background:#fff;"></div> Vacant</div>
                <div class="legend-item"><div class="box" style="background:#ef4444;"></div> Reserved</div>
                <div class="legend-item"><div class="box" style="background:#22c55e;"></div> Your Selection</div>
            </div>
            <div id="bus-layout" class="bus-layout"></div>
            <div id="passenger-details-section" class="passenger-details-section">
                <h3 style="font-size:1.25rem; margin-bottom:25px;">Traveller Identification</h3>
                <div id="passenger-info-container"></div>
            </div>
            <div style="margin-top:40px; display:flex; gap:15px; justify-content:flex-end;">
                <button class="button regular-button" onclick="closeSeatMap()" style="background:#f1f5f9; color:#475569;">Cancel</button>
                <button id="confirm-booking-btn" class="button regular-button pink-background" disabled onclick="submitBooking()">Finalize Booking</button>
            </div>
        </div>
    </div>
    <script src="js/footer.js"></script>
    <script>
    function validatePaxName(input) {
        if (input.value.trim().length == 0) {
            alert("Passenger Name is required");
            return false;
        }
        return true;
    }
    function validatePaxAge(input) {
        var age = input.value.trim();
        if (age.length == 0 || isNaN(age) || parseInt(age) < 0 || parseInt(age) > 120) {
            alert("Please enter a valid age (0-120)");
            return false;
        }
        return true;
    }
    function validatePaxId(input) {
        if (input.value.trim().length == 0) {
            alert("ID/Passport/Birth Cert Number is required");
            return false;
        }
        return true;
    }
    let currentRouteId = null;
    let selectedSeats = [];
    function openSeatMap(route_id, max_passengers) {
        currentRouteId = route_id;
        selectedSeats = [];
        updateBookingButton();
        document.body.style.overflow = 'hidden';
        fetch(`op_get_occupied_seats.php?route_id=${route_id}`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                const occupiedList = data.occupied || [];
                const totalSeats = parseInt(max_passengers) || 0;
                generateLayout(totalSeats, occupiedList);
                document.getElementById('seat-modal').style.display = 'block';
            })
            .catch(err => {
                console.error('Booking Error:', err);
                alert('Critical Error: Unable to fetch seating data. ' + err.message);
                document.body.style.overflow = 'auto';
            });
    }
    function generateLayout(total, occupied) {
        const container = document.getElementById('bus-layout');
        container.innerHTML = '';
        for (let i = 1; i <= total; i++) {
            const seatNo = `S${i}`;
            const currentGridIndex = container.children.length + 1;
            if (currentGridIndex % 5 === 3) {
                const spacer = document.createElement('div');
                spacer.className = 'aisle-spacer';
                container.appendChild(spacer);
            }
            const seatNode = document.createElement('div');
            if (occupied.includes(seatNo)) {
                seatNode.className = 'seat occupied';
                seatNode.innerText = seatNo;
            } else {
                seatNode.className = 'seat available';
                seatNode.innerText = seatNo;
                seatNode.onclick = () => toggleSeatSelection(seatNode, seatNo);
            }
            container.appendChild(seatNode);
        }
    }
    function toggleSeatSelection(element, seatNo) {
        if (selectedSeats.includes(seatNo)) {
            selectedSeats = selectedSeats.filter(s => s !== seatNo);
            element.classList.replace('selected', 'available');
        } else {
            selectedSeats.push(seatNo);
            element.classList.replace('available', 'selected');
        }
        updatePassengerDataForms();
        updateBookingButton();
    }
    function updatePassengerDataForms() {
        const container = document.getElementById('passenger-info-container');
        const section = document.getElementById('passenger-details-section');
        if (selectedSeats.length === 0) {
            section.style.display = 'none';
            return;
        }
        section.style.display = 'block';
        const draftData = {};
        container.querySelectorAll('.passenger-info-card').forEach(card => {
            const s = card.dataset.seat;
            draftData[s] = { name: card.querySelector('.p-name').value, age: card.querySelector('.p-age').value, id: card.querySelector('.p-id').value };
        });
        container.innerHTML = '';
        selectedSeats.forEach(seatId => {
            const card = document.createElement('div');
            card.className = 'passenger-info-card';
            card.dataset.seat = seatId;
            const saved = draftData[seatId] || { name: '', age: '', id: '' };
            card.innerHTML = `
                <h4>Seat ${seatId} Details</h4>
                <div class="info-grid">
                    <div class="info-group">
                        <label>Traveller Name</label>
                        <input type="text" class="p-name" value="${saved.name}" placeholder="Enter name...">
                    </div>
                    <div class="info-group">
                        <label>Age</label>
                        <input type="text" class="p-age" value="${saved.age}" placeholder="e.g. 25">
                    </div>
                    <div class="info-group">
                        <label>ID Number</label>
                        <input type="text" class="p-id" value="${saved.id}" placeholder="ID/Passport">
                    </div>
                </div>`;
            container.appendChild(card);
        });
    }
    function updateBookingButton() {
        const btn = document.getElementById('confirm-booking-btn');
        btn.disabled = (selectedSeats.length === 0);
    }
    function closeSeatMap() {
        document.getElementById('seat-modal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    function submitBooking() {
        const system_user_id = document.getElementById('target_user_id').value;
        const payloadArray = [];
        const cards = document.querySelectorAll('.passenger-info-card');
        for (let i = 0; i < cards.length; i++) {
            const card = cards[i];
            const nameInput = card.querySelector('.p-name');
            const ageInput = card.querySelector('.p-age');
            const idInput = card.querySelector('.p-id');
            if (!validatePaxName(nameInput)) return;
            if (!validatePaxAge(ageInput)) return;
            if (!validatePaxId(idInput)) return;
            payloadArray.push({ seat_id: card.dataset.seat, name: nameInput.value.trim(), age: ageInput.value.trim(), id: idInput.value.trim() });
        }
        if (!confirm(`Confirm booking for ${selectedSeats.length} seats?`)) return;
        fetch('process_booking.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ route_id: currentRouteId, user_id: system_user_id, passengers: payloadArray }) })
        .then(res => res.json())
        .then(result => {
            if (result.success) { alert(result.message); window.location.href = result.redirect || 'view_user_history.php'; }
            else { alert('Error: ' + result.message); }
        })
        .catch(err => alert('System Error: ' + err.message));
    }
    </script>
</body>
</html>
<?php
mysqli_close($conn);
?>