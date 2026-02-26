<?php
/**
 * CORE BOOKING ENGINE (book.php)
 * =================================================================
 * Purpose: This is the flagship interface of the IBBS Prototype. 
 * It allows Passengers to find trips and Agents to book for walk-ins.
 * 
 * Technical Highlights:
 * - Real-time seat availability logic.
 * - Dynamic Seat Map injection.
 * - Multi-passenger data capture (Name, Age, ID).
 * - AJAX-based submission to prevent page reloads.
 * =================================================================
 */

// 1. DATA BRIDGE: Connect to the MySQL database by including the header file.
require_once 'db_connection.php';

// 2. IDENTITY: Start the PHP session mechanism to track the current logged-in user's state.
session_start();

/**
 * --- SECURITY ACCESS CONTROL ---
 * Booking is a privileged action. We check if the user is logged in.
 * If the 'user_id' index is missing from the $_SESSION array, they are blocked.
 */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Force redirect to the login portal.
    exit(); // Prevent any further HTML from loading.
}

/**
 * --- USER CONTEXT ---
 * Retrieve the specific identity and role of the person accessing the page.
 * We use this to toggle between "Passenger Mode" and "Staff/Agent Mode".
 */
$user_id = $_SESSION['user_id']; // The unique database ID of the user.
$role    = $_SESSION['role'];    // The designation (PASSENGER, AGENT, or ADMIN).
// $is_staff: A boolean (True/False) that checks if the role matches internal staff types.
$is_staff = ($role === 'ADMIN' || $role === 'AGENT');


/**
 * --- QUERY: AVAILABLE TRIPS ---
 * We write a complex SQL query to find all valid journeys.
 * Logic:
 * - Select all columns (*) from the 'routes' table (r).
 * - JOIN with 'buses' (b) to retrieve seat capacity (max_passengers).
 * - Use a correlated subquery to dynamically COUNT active bookings for each route.
 * - FILTER: Only show trips that haven't happened yet (>= CURDATE()).
 * - FILTER: Hide trips that this specific user has already booked to avoid double-booking.
 * - SORT: Kenya routes first, then Tanzania, then others, then by date.
 */
$sql_available = "
    SELECT r.*, b.bus_name, b.max_passengers, 
           (SELECT COUNT(*) FROM bookings WHERE route_id = r.route_id AND booking_status != 'CANCELLED') as booked_seats
    FROM routes r
    JOIN buses b ON r.bus_id = b.bus_id
    WHERE r.departure_date >= CURDATE()
    AND r.route_id NOT IN (
        SELECT route_id FROM bookings WHERE user_id = $user_id AND booking_status != 'CANCELLED'
    )
    ORDER BY 
        CASE 
            WHEN r.from_location LIKE '%Kenya%' THEN 1 
            WHEN r.from_location LIKE '%Tanzania%' THEN 2 
            ELSE 3 
        END ASC,
        r.departure_date ASC
";
// $result_available: Executes the query and stores the rows in memory.
$result_available = $conn->query($sql_available);


/**
 * --- DATA: PASSENGER REGISTRY (Staff Only) ---
 * If an AGENT is booking, they need to select WHICH customer they are booking for.
 * We fetch all users who have the 'PASSENGER' role.
 */
$passengers = []; // Initialize an empty container.
if ($is_staff) {
    // Run a query to get names and emails for the dropdown.
    $pass_res = $conn->query("SELECT user_id, first_name, last_name, email FROM users WHERE role = 'PASSENGER' ORDER BY first_name");
    // Loop through results and store them in the $passengers array.
    while($p = $pass_res->fetch_assoc()) $passengers[] = $p;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book your Journey - Wema Travellers</title>
    
    <!-- Branding & Design System -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main.css">
    
    <style>
        /* UI DESIGN: Core layout for the booking engine */
        .booking-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        /* Staff Controls: Blue highlight for the Admin-only section */
        .staff-panel {
            background: #f0f4ff;
            border: 2px solid var(--purple);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 40px;
        }

        .staff-panel label {
            display: block;
            font-weight: 700;
            color: #1e1b4b;
            margin-bottom: 12px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .passenger-select {
            width: 100%;
            max-width: 500px;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1.1em;
            background: white;
        }

        /* Grid Table for Route Listing */
        .crud-table {
            width: 100%;
            border-collapse: collapse;
        }
        .crud-table th, .crud-table td {
            padding: 18px;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
        }
        .crud-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        
        /* -------------------------------------------------------------
           SEAT MAP MODAL: The Interactive Seating UI
           ------------------------------------------------------------- */
        
        /* Overlay: Darkens the background */
        #seat-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(8px);
            overflow-y: auto;
        }

        /* Modal Body */
        .seat-content {
            background-color: #ffffff;
            margin: 3% auto;
            padding: 40px;
            border-radius: 20px;
            width: 90%;
            max-width: 850px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* The Virtual Bus: CSS Grid of 5 columns (Standard Bus Layout) */
        .bus-layout {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            background: #f1f5f9;
            padding: 25px;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            margin: 30px auto;
            max-width: 450px;
        }

        /* Individual Seat Interaction */
        .seat {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 0.85em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #cbd5e1;
            background: white;
            color: #475569;
        }

        /* Seat States */
        /* Available: Clean White */
        .seat.available:hover { 
            border-color: #22c55e; 
            background-color: #f0fdf4; 
            transform: scale(1.05); 
        }
        /* Occupied: Solid Red (Blocked) */
        .seat.occupied { 
            background-color: #ef4444; 
            color: white; 
            border-color: #dc2626; 
            cursor: not-allowed; 
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        /* Selected: Vibrant Green (Active) */
        .seat.selected { 
            background-color: #22c55e; 
            color: white; 
            border-color: #15803d;
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.4);
        }
        
        /* PASSENGER DATA FORMS: Generated dynamically based on selected seats */
        .passenger-details-section {
            text-align: left;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
            display: none;
        }

        .passenger-info-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 6px solid var(--purple);
        }
        .passenger-info-card h4 { margin: 0 0 15px 0; color: #1e293b; font-size: 1em; }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .info-group label { display: block; font-size: 0.8em; font-weight: 600; color: #64748b; margin-bottom: 8px; }
        .info-group input { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #cbd5e1; 
            border-radius: 6px; 
            background: white;
            font-size: 0.95em;
        }

        /* UI Legend: Helping users understand colors */
        .legend {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin: 25px 0;
            font-size: 0.85em;
            color: #64748b;
        }
        .legend-item { display: flex; align-items: center; gap: 8px; }
        .box { width: 18px; height: 18px; border-radius: 4px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <!-- Standard Navigation -->
    <script src="js/header2.js"></script>
    
    <!-- Buffer for the sticky header -->
    <div style="height: 100px;"></div>

    <div class="booking-container"> <!-- Main visual wrapper for the booking software. -->
        <h2 style="color: var(--purple); margin-bottom: 30px; font-weight: 800;">🎫 Trip Reservation Engine</h2> <!-- Main Page Title. -->

        <!-- STAFF CONTROLS (Only visible if the logged-in user is an ADMIN or AGENT) -->
        <?php if ($is_staff): ?>
        <div class="staff-panel"> <!-- Styles this area with a light blue background. -->
            <label for="target_user_id">Booking Representative Control</label> <!-- Label for instructions. -->
            <select id="target_user_id" class="passenger-select"> <!-- Dropdown to pick the customer. -->
                <!-- Default option: The Agent books for themselves. -->
                <option value="<?= $user_id ?>">Agent Action: (<?= htmlspecialchars($_SESSION['name']) ?>)</option>
                <!-- Grouped list of all registered passengers in the system. -->
                <optgroup label="Select Authorized Passenger">
                    <?php foreach($passengers as $p): ?> <!-- Loop through the $passengers array created in PHP above. -->
                        <option value="<?= $p['user_id'] ?>">
                            <?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?> | <?= htmlspecialchars($p['email']) ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
            <p style="margin-top: 15px; font-size: 0.85em; color: #475569;">
                <!-- Link to manual registration if the customer isn't in the list yet. -->
                Need a new account? <a href="view_users_sorted.php" style="color:var(--purple); font-weight:700;">Create Passenger Profile →</a>
            </p>
        </div>
        <?php else: ?>
            <!-- Regular Passenger Logic: We hide the selection and force the user_id to be their own. -->
            <input type="hidden" id="target_user_id" value="<?= $user_id ?>">
        <?php endif; ?>

        <!-- ACTIVE ROUTES: The visual table schedule -->
        <div class="table-container">
            <table class="crud-table"> <!-- Uses the standardized project table styles. -->
                <thead>
                    <tr>
                        <th>Destination</th> <!-- Origin and Destination cities. -->
                        <th>Departure Schedule</th> <!-- Date and Time. -->
                        <th>Assigned Vehicle</th> <!-- The bus name. -->
                        <th>Cost (KES)</th> <!-- Ticket price. -->
                        <th>Availability</th> <!-- Seats free. -->
                        <th>Action</th> <!-- The 'Reserve' button. -->
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Render each active route row from the $result_available database object.
                    $result_available->data_seek(0); // Reset pointer to the start of the results.
                    while ($row = $result_available->fetch_assoc()): // Fetch one row at a time.
                        // Calculate remaining seats by subtracting bookings from max capacity.
                        $remaining = $row['max_passengers'] - $row['booked_seats'];
                        // $is_full: Boolean check to see if the bus is sold out.
                        $is_full = ($remaining <= 0);
                    ?>
                    <tr>
                        <!-- Location Pair -->
                        <td>
                            <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($row['from_location']) ?></div> <!-- Origin city. -->
                            <div style="font-size: 0.85em; color: #64748b;">to <?= htmlspecialchars($row['to_location']) ?></div> <!-- Destination city. -->
                        </td>
                        <!-- Time Tracking -->
                        <td>
                            <div style="font-weight: 600;"><?= $row['departure_date'] ?></div> <!-- Calendar date. -->
                            <div style="font-size: 0.85em; font-family: monospace; color: var(--purple);"><?= $row['departure_time'] ?></div> <!-- Clock time. -->
                        </td>
                        <!-- Bus Identity -->
                        <td><?= htmlspecialchars($row['bus_name']) ?></td> <!-- Name of the bus vehicle. -->
                        <!-- Fare Calculation -->
                        <td style="font-weight: 700; color: #1e293b;"><?= number_format($row['cost'], 2) ?></td> <!-- Price formatted for currency. -->
                        <!-- Stock Status -->
                        <td>
                            <?php if ($is_full): ?>
                                <!-- Red pill showing the bus is full. -->
                                <span style="background: #fee2e2; color: #b91c1c; padding: 6px 12px; border-radius: 99px; font-size: 0.75em; font-weight: 800;">BUS FULL</span>
                            <?php else: ?>
                                <!-- Green pill showing available seats count. -->
                                <span style="background: #f0fdf4; color: #166534; padding: 6px 12px; border-radius: 99px; font-size: 0.75em; font-weight: 800;"><?= $remaining ?> SEATS OPEN</span>
                            <?php endif; ?>
                        </td>
                        <!-- Entry Button -->
                        <td>
                            <?php if (!$is_full): ?>
                                <!-- Active button that triggers the JS Seat Map logic. -->
                                <button type="button" class="button regular-button pink-background" onclick="openSeatMap(<?= $row['route_id'] ?>, <?= $row['max_passengers'] ?>)">Reserve Seats</button>
                            <?php else: ?>
                                <!-- Faded button if no seats are available. -->
                                <button disabled class="button regular-button" style="opacity:0.3; cursor:not-allowed;">Sold Out</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?> <!-- End of trip loop. -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- -----------------------------------------------------------------
         INTERFACE: INTERACTIVE SEATING MODAL
         ----------------------------------------------------------------- -->
    <div id="seat-modal">
        <div class="seat-content">
            <h3 style="margin-top:0; font-size: 1.5rem; color: #0f172a;">Virtual Seating Deck</h3>
            <p style="color:#64748b; font-size:0.95em; margin-bottom: 20px;">Please click on your preferred seats to begin the reservation.</p>
            
            <!-- GUIDANCE: Color coding legend -->
            <div class="legend">
                <div class="legend-item"><div class="box" style="background:#fff;"></div> Vacant</div>
                <div class="legend-item"><div class="box" style="background:#ef4444; border-color: #ef4444;"></div> Reserved</div>
                <div class="legend-item"><div class="box" style="background:#22c55e; border-color: #22c55e;"></div> Your Selection</div>
            </div>

            <!-- THE BUS: This is where JS will draw the seats -->
            <div id="bus-layout" class="bus-layout">
                <!-- Dynamically Generated -->
            </div>

            <!-- PASSENGER IDENTITY: This appears as soon as 1 seat is clicked -->
            <div id="passenger-details-section" class="passenger-details-section">
                <h3 style="font-size: 1.25rem; margin-bottom: 25px;">Traveller Identification</h3>
                <div id="passenger-info-container">
                    <!-- Cards appear here -->
                </div>
            </div>

            <!-- MODAL CONTROL PANEL: Action buttons at the bottom -->
            <div style="margin-top: 40px; display: flex; gap: 15px; justify-content: flex-end; position: sticky; bottom: -40px; background: #ffffff; padding: 25px 0; border-top: 1px solid #f1f5f9;">
                <button class="button regular-button" onclick="closeSeatMap()" style="background:#f1f5f9; color: #475569;">Cancel Action</button>
                <button id="confirm-booking-btn" class="button regular-button pink-background" disabled onclick="submitBooking()">Finalize Booking</button>
            </div>
        </div>
    </div>

    <div style="height: 100px;"></div>
    
    <!-- Link the central script for the footer -->
    <script src="js/footer.js"></script>


    <!-- =================================================================
         LOGIC: ENGINE JAVASCRIPT
         ================================================================= -->
    <script>
        // CLIENT STATE
        let currentRouteId = null;  // The Trip we are currently looking at
        let selectedSeats = [];     // List of seats currently highlighted green

        /**
         * ACTION: OPEN SEAT ENGINE
         * Loads fresh data for the specific trip and resets the modal.
         */
        function openSeatMap(route_id, max_passengers) {
            currentRouteId = route_id;
            selectedSeats = []; // Wipe selections from previous modal open
            
            // UI RESET
            updateBookingButton();
            document.body.style.overflow = 'hidden'; // Lock background scroll
            
            /**
             * FETCH OCCUPANCY:
             * We call a background PHP script (op_get_occupied_seats.php)
             * to find out which seats are already taken in the database.
             */
            fetch(`op_get_occupied_seats.php?route_id=${route_id}`)
                .then(res => res.json())
                .then(data => {
                    const occupiedList = data.occupied || [];
                    // TRIGGER DRAW: Build the visual grid
                    generateLayout(max_passengers, occupiedList);
                    
                    // SHOW: Reveal the modal
                    document.getElementById('seat-modal').style.display = 'block';
                    document.getElementById('passenger-details-section').style.display = 'none';
                    document.getElementById('passenger-info-container').innerHTML = '';
                });
        }

        /**
         * LOGIC: GRID GENERATOR
         * Creates a clickable squares for every seat in the bus fleet.
         */
        function generateLayout(total, occupied) {
            const container = document.getElementById('bus-layout');
            container.innerHTML = ''; // Clean slate
            
            for (let i = 1; i <= total; i++) {
                const seatNo = `S${i}`; // Standardized ID (e.g., S24)
                const seatNode = document.createElement('div');
                
                // If the seat is in our 'occupied' list from the server
                if (occupied.includes(seatNo)) {
                    seatNode.className = 'seat occupied';
                    seatNode.innerText = seatNo;
                } else {
                    // Seat is available
                    seatNode.className = 'seat available';
                    seatNode.innerText = seatNo;
                    // Connect the interactive logic
                    seatNode.onclick = () => toggleSeatSelection(seatNode, seatNo);
                }
                container.appendChild(seatNode);
            }
        }

        /**
         * ACTION: TOGGLE SELECTION
         * Logic to add or remove a seat from the user's "shopping cart".
         */
        function toggleSeatSelection(element, seatNo) {
            if (selectedSeats.includes(seatNo)) {
                // Remove if already there
                selectedSeats = selectedSeats.filter(s => s !== seatNo);
                element.classList.replace('selected', 'available');
            } else {
                // Add to selection
                selectedSeats.push(seatNo);
                element.classList.replace('available', 'selected');
            }
            
            // SIDE EFFECTS:
            updatePassengerDataForms(); // Update the Name/ID inputs below
            updateBookingButton();      // Enable/Disable the confirm button
        }

        /**
         * UI: DYNAMIC PASSENGER FORMS
         * Generates one data card (Name, Age, ID) for every green seat.
         * Crucially: It saves what the user already typed before rebuilding.
         */
        function updatePassengerDataForms() {
            const container = document.getElementById('passenger-info-container');
            const section = document.getElementById('passenger-details-section');
            
            // Clean up if nothing is selected
            if (selectedSeats.length === 0) {
                section.style.display = 'none';
                return;
            }
            
            section.style.display = 'block';
            
            // 1. MEMORY: Save existing text input values before we wipe the container
            const draftData = {};
            container.querySelectorAll('.passenger-info-card').forEach(card => {
                const s = card.dataset.seat;
                draftData[s] = {
                    name: card.querySelector('.p-name').value,
                    age: card.querySelector('.p-age').value,
                    id: card.querySelector('.p-id').value
                };
            });
            
            // 2. CLEAR: Wipe current inputs
            container.innerHTML = '';

            // 3. REBUILD: Create a fresh form for every selected seat
            // We sort selections (S1, S2...) for a clean layout
            selectedSeats.sort((a,b) => parseInt(a.substring(1)) - parseInt(b.substring(1))).forEach(seatKey => {
                const card = document.createElement('div');
                card.className = 'passenger-info-card';
                card.dataset.seat = seatKey;
                
                // Restore values if we saved them in Step 1
                const v = draftData[seatKey] || { name: '', age: '', id: '' }; 
                
                card.innerHTML = `
                    <h4>Reservation: Seat ${seatKey}</h4>
                    <div class="info-grid">
                        <div class="info-group">
                            <label>Traveller Name</label>
                            <input type="text" class="p-name" value="${v.name}" placeholder="Full Legal Name" required>
                        </div>
                        <div class="info-group">
                            <label>Age</label>
                            <input type="number" class="p-age" value="${v.age}" placeholder="e.g. 25" required>
                        </div>
                        <div class="info-group">
                            <label>ID / Identity Number</label>
                            <input type="text" class="p-id" value="${v.id}" placeholder="ID or Passport" required>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        // CONTROL: Only allow "Confirm" if seats are picked
        function updateBookingButton() {
            document.getElementById('confirm-booking-btn').disabled = (selectedSeats.length === 0);
        }

        // ACTION: Close the overlay and restore system scroll
        function closeSeatMap() {
            document.getElementById('seat-modal').style.display = 'none';
            document.body.style.overflow = 'auto'; 
        }

        /**
         * THE BIG ACTION: SUBMIT BOOKING
         * Collects all seat/passenger data and sends it to 'process_booking.php' 
         * using a JSON POST request.
         */
        function submitBooking() {
            const system_user_id = document.getElementById('target_user_id').value;
            const payloadArray = [];
            
            let allFieldsValid = true;
            
            // Loop through all generated forms to validate and capture
            document.querySelectorAll('.passenger-info-card').forEach(card => {
                const p_name = card.querySelector('.p-name').value.trim();
                const p_age  = card.querySelector('.p-age').value.trim();
                const p_id   = card.querySelector('.p-id').value.trim();
                
                if (!p_name || !p_age || !p_id) allFieldsValid = false;
                
                payloadArray.push({
                    seat_number: card.dataset.seat,
                    passenger_name: p_name,
                    passenger_age: p_age,
                    passenger_id_number: p_id
                });
            });
            
            // Check for empty fields
            if (!allFieldsValid) {
                alert('Incomplete Data: Please provide details for all selected seats.');
                return;
            }

            // User Confirmation
            if (!confirm(`Commit reservation for ${selectedSeats.length} passenger(s)?`)) return;

            /**
             * EXECUTE AJAX:
             * This allows us to book the ticket WITHOUT the page refreshing.
             */
            fetch('process_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    route_id: currentRouteId,
                    target_user_id: system_user_id,
                    bookings: payloadArray
                })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    // SUCCESS: Route the user to their tickets
                    alert(result.message);
                    window.location.href = result.redirect || 'view_user_history.php';
                } else {
                    // CONFLICT: e.g. Someone else just took the same seat
                    alert('Reservation Failed: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Network/Server Crash:', error);
                alert('System Malfunction: Could not communicate with booking server.');
            });
        }
    </script>
</body>
</html>
