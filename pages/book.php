<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * CORE BOOKING ENGINE (book.php)                                    // [3] Title identifying this script as the flagship reservation portal.
 * ================================================================= // [4] Visual separator for architectural documentation.
 * Purpose: This is the flagship interface of the IBBS Prototype.    // [5] Primary objective: provide a gateway for trip selection.
 * It allows Passengers to find trips and Agents to book for walk-ins. // [6] Target users: both public passengers and staff agents.
 *                                                                   // [7] White space for readability within comments.
 * Technical Highlights:                                             // [8] Executive summary of technical implementations.
 * - Real-time seat availability logic via MySQL subqueries.         // [9] Feature: dynamic vacancy tracking.
 * - Dynamic Seat Map injection using JS DOM manipulation.           // [10] Feature: interactive virtual vehicle layout.
 * - Multi-passenger data capture for group travel (Name, Age, ID).   // [11] Feature: batch processing of travel groups.
 * - AJAX-based submission (fetch API) for seamless UX without reloads. // [12] Feature: asynchronous state management.
 * ================================================================= // [13] Visual separator for architectural documentation.
 */                                                                  // [14] Close documentation block.

require_once 'db_connection.php';                                    // [15] Import database connection object ($conn) for MySQL communication.
session_start();                                                    // [16] Initialize or resume user session to identify the requester.

if (!isset($_SESSION['user_id'])) {                                  // [17] Verify user authentication to protect route inventory records.
    header("Location: login.html");                                  // [18] Redirect unauthorized visitors to the security portal.
    exit();                                                          // [19] Halt script execution to prevent unauthorized data exposure.
}                                                                    // [20] Close security validation boundary.

$user_id = $_SESSION['user_id'];                                     // [21] Map active session user ID to local scalar variable.
$role    = $_SESSION['role'];                                        // [22] Map active session role to local scalar variable.
$is_staff = ($role === 'ADMIN' || $role === 'AGENT');                // [23] Define boolean flag for elevated administrative capabilities.

/**                                                                  // [24] Documentation for trip discovery logic.
 * QUERY: AVAILABLE TRIPS                                            // [25] Section header for database analytical execution.
 */                                                                  // [26] Close documentation block.
$sql_available = "SELECT r.*, b.bus_name, b.max_passengers, (SELECT COUNT(*) FROM bookings WHERE route_id = r.route_id AND booking_status != 'CANCELLED') as booked_seats FROM routes r JOIN buses b ON r.bus_id = b.bus_id WHERE r.departure_date >= CURDATE() AND r.route_id NOT IN (SELECT route_id FROM bookings WHERE user_id = $user_id AND booking_status != 'CANCELLED') ORDER BY CASE WHEN r.from_location LIKE '%Kenya%' THEN 1 WHEN r.from_location LIKE '%Tanzania%' THEN 2 ELSE 3 END ASC, r.departure_date ASC"; // [27] High-performance SQL for live inventory.
$result_available = $conn->query($sql_available);                    // [28] Execute the inventory discovery query against the MySQL server.

$passengers = [];                                                    // [29] Initialize data array for administrative passenger lookup.
if ($is_staff) {                                                     // [30] Check if user has permission to book for third parties.
    $pass_res = $conn->query("SELECT user_id, first_name, last_name, email FROM users WHERE role = 'PASSENGER' ORDER BY first_name"); // [31] Fetch all passenger records.
    while($p = $pass_res->fetch_assoc()) {                           // [32] Iterate through user profiles to build lookup table.
        $passengers[] = $p;                                          // [33] Append user data object to the collection.
    }                                                                // [34] Close iteration.
}                                                                    // [35] Close staff-only logic block.
?>                                                                   <!-- [36] Close PHP block and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [37] Specify document type as standard HTML5. -->
<html lang="en">                                                     <!-- [38] Root element defining English as the layout language. -->
<head>                                                               <!-- [39] Metadata and styling resources header. -->
    <meta charset="UTF-8">                                           <!-- [40] Set UTF-8 encoding for character correctness. -->
    <title>Book your Journey - Wema Travellers</title>                 <!-- [41] Browser tab title identifier. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [42] Load global styling architecture and variables. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [43] Load shared component visual designs. -->
    <style>                                                          /* [44] Open internal CSS for engine-specific layout. */
        .booking-container { max-width: 1200px; margin: 40px auto; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); } /* [45] Main card. */
        .staff-panel { background: #f0f4ff; border: 2px solid var(--purple); padding: 25px; border-radius: 12px; margin-bottom: 40px; } /* [46] Agent tools area. */
        .staff-panel label { display: block; font-weight: 700; color: #1e1b4b; margin-bottom: 12px; font-size: 0.9em; text-transform: uppercase; letter-spacing: 0.05em; } /* [47] labels. */
        .passenger-select { width: 100%; max-width: 500px; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1.1em; background: white; } /* [48] dropdown. */
        .crud-table { width: 100%; border-collapse: collapse; }      /* [49] Data grid base. */
        .crud-table th, .crud-table td { padding: 18px; border-bottom: 1px solid #f1f5f9; text-align: left; } /* [50] cell style. */
        .crud-table th { background-color: #f8fafc; color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; } /* [51] headers. */
        #seat-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); overflow-y: auto; } /* [52] Modal overlay. */
        .seat-content { background-color: #ffffff; margin: 3% auto; padding: 40px; border-radius: 20px; width: 90%; max-width: 850px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); } /* [53] Modal card. */
        .bus-layout { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; background: #f1f5f9; padding: 25px; border-radius: 18px; border: 1px solid #e2e8f0; margin: 30px auto; max-width: 450px; } /* [54] Grid. */
        .seat { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 0.85em; font-weight: 700; cursor: pointer; transition: all 0.2s ease; border: 1px solid #cbd5e1; background: white; color: #475569; } /* [55] seat. */
        .seat.available:hover { border-color: #22c55e; background-color: #f0fdf4; transform: scale(1.05); } /* [56] hover. */
        .seat.occupied { background-color: #ef4444; color: white; border-color: #dc2626; cursor: not-allowed; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1); } /* [57] occupied. */
        .seat.selected { background-color: #22c55e; color: white; border-color: #15803d; box-shadow: 0 0 15px rgba(34, 197, 94, 0.4); } /* [58] selected. */
        .passenger-details-section { text-align: left; margin-top: 40px; padding-top: 30px; border-top: 1px solid #e2e8f0; display: none; } /* [59] form area. */
        .passenger-info-card { background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 20px; border-left: 6px solid var(--purple); } /* [60] data card. */
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; } /* [61] input grid. */
        .info-group label { display: block; font-size: 0.8em; font-weight: 600; color: #64748b; margin-bottom: 8px; } /* [62] label. */
        .info-group input { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: white; font-size: 0.95em; } /* [63] box. */
        .legend { display: flex; justify-content: center; gap: 25px; margin: 25px 0; font-size: 0.85em; color: #64748b; } /* [64] legend container. */
        .legend-item { display: flex; align-items: center; gap: 8px; } /* [65] legend item. */
        .box { width: 18px; height: 18px; border-radius: 4px; border: 1px solid #e2e8f0; } /* [66] color box. */
    </style>                                                         <!-- [67] Terminate internal CSS block. -->
</head>                                                              <!-- [68] Close head section. -->

<body class="<?= strtolower($_SESSION['role'] ?? 'passenger') ?>-role"> <!-- [69] Open document body for visible components. -->
<script src="js/header2.js"></script>                                <!-- [70] Inject site-wide global navigation header script. -->
<div style="height: 100px;"></div>                                   <!-- [71] Layout spacer for fixed header visibility. -->

<div class="booking-container">                                      <!-- [72] Primary system interface start. -->
    <h2 style="color: var(--purple); margin-bottom: 30px; font-weight: 800;">🎫 Trip Reservation Engine</h2> <!-- [73] title. -->

    <?php if ($is_staff): ?>                                         <!-- [74] Open staff-only controller wrapper. -->
    <div class="staff-panel">                                        <!-- [75] Administrative command hub. -->
        <label for="target_user_id">Booking Representative Control</label> <!-- [76] instructions. -->
        <select id="target_user_id" class="passenger-select">        <!-- [77] user selection target. -->
            <option value="<?= $user_id ?>">Agent Action: (<?= htmlspecialchars($_SESSION['name']) ?>)</option> <!-- [78] current agent. -->
            <optgroup label="Select Authorized Passenger">           <!-- [79] passenger database group. -->
                <?php foreach($passengers as $p): ?>                 <!-- [80] iterate through registered customers. -->
                    <option value="<?= $p['user_id'] ?>"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?> | <?= htmlspecialchars($p['email']) ?></option> <!-- [81] option. -->
                <?php endforeach; ?>                                 <!-- [82] end iteration. -->
            </optgroup>                                              <!-- [83] close group. -->
        </select>                                                    <!-- [84] close select. -->
        <p style="margin-top: 15px; font-size: 0.85em; color: #475569;">Need a new account? <a href="view_users_sorted.php" style="color:var(--purple); font-weight:700;">Create Passenger Profile →</a></p> <!-- [85] link. -->
    </div>                                                           <!-- [86] end panel. -->
    <?php else: ?>                                                   <!-- [87] handle for regular public passengers. -->
        <input type="hidden" id="target_user_id" value="<?= $user_id ?>"> <!-- [88] force ID mapping without UI presence. -->
    <?php endif; ?>                                                   <!-- [89] end conditional toolset. -->

    <div class="table-container">                                    <!-- [90] Schedule grid container. -->
        <table class="crud-table">                                   <!-- [91] Open data table. -->
            <thead>                                                  <!-- [92] Table header row. -->
                <tr><th>Destination</th><th>Departure</th><th>Vehicle</th><th>Cost (KES)</th><th>Availability</th><th>Action</th></tr> <!-- [93] headers. -->
            </thead>                                                 <!-- [94] end head. -->
            <tbody>                                                  <!-- [95] Opening record output stream. -->
                <?php $result_available->data_seek(0);               // [96] Reset database cursor to start of list.
                while ($row = $result_available->fetch_assoc()):     // [97] Iterate through discovered transport routes.
                    $remaining = $row['max_passengers'] - $row['booked_seats']; // [98] Calculate live seat availability.
                    $is_full = ($remaining <= 0);                    // [99] Boolean flag for sold-out status.
                ?>                                                   <!-- [100] Process PHP and resume HTML generation. -->
                <tr>                                                 <!-- [101] Trip record row. -->
                    <td><div style="font-weight:700; color:#1e293b;"><?= htmlspecialchars($row['from_location']) ?></div><div style="font-size:0.85em; color:#64748b;">to <?= htmlspecialchars($row['to_location']) ?></div></td> <!-- [102] Route. -->
                    <td><div style="font-weight:600;"><?= $row['departure_date'] ?></div><div style="font-size:0.85em; font-family:monospace; color:var(--purple);"><?= $row['departure_time'] ?></div></td> <!-- [103] Schedule. -->
                    <td><?= htmlspecialchars($row['bus_name']) ?></td> <!-- [104] assigned coach. -->
                    <td style="font-weight:700; color:#1e293b;"><?= number_format($row['cost'], 2) ?></td> <!-- [105] price. -->
                    <td><?= $is_full ? '<span style="background:#fee2e2; color:#b91c1c; padding:6px 12px; border-radius:99px; font-size:0.75em; font-weight:800;">BUS FULL</span>' : '<span style="background:#f0fdf4; color:#166534; padding:6px 12px; border-radius:99px; font-size:0.75em; font-weight:800;">' . $remaining . ' SEATS OPEN</span>' ?></td> <!-- [106] status. -->
                    <td><?= !$is_full ? '<button type="button" class="button regular-button pink-background" onclick="openSeatMap(' . $row['route_id'] . ',' . $row['max_passengers'] . ')">Reserve Seats</button>' : '<button disabled class="button regular-button" style="opacity:0.3; cursor:not-allowed;">Sold Out</button>' ?></td> <!-- [107] trigger. -->
                </tr>                                                <!-- [108] end row. -->
                <?php endwhile; ?>                                   <!-- [109] end trip loop. -->
            </tbody>                                                 <!-- [110] end table body. -->
        </table>                                                     <!-- [111] end table. -->
    </div>                                                           <!-- [112] end container. -->
</div>                                                               <!-- [113] end card. -->

<div id="seat-modal">                                                <!-- [114] Interactive seating deck modal system. -->
    <div class="seat-content">                                       <!-- [115] modal card. -->
        <h3 style="margin-top:0; font-size:1.5rem; color:#0f172a;">Virtual Seating Deck</h3> <!-- [116] header. -->
        <p style="color:#64748b; font-size:0.95em; margin-bottom:20px;">Please click on your preferred seats to begin the reservation.</p> <!-- [117] instructions. -->
        <div class="legend">                                         <!-- [118] Decoder for seat availability colors. -->
            <div class="legend-item"><div class="box" style="background:#fff;"></div> Vacant</div> <!-- [119] free. -->
            <div class="legend-item"><div class="box" style="background:#ef4444; border-color:#ef4444;"></div> Reserved</div> <!-- [120] taken. -->
            <div class="legend-item"><div class="box" style="background:#22c55e; border-color:#22c55e;"></div> Your Selection</div> <!-- [121] picking. -->
        </div>                                                       <!-- [122] end legend. -->
        <div id="bus-layout" class="bus-layout"></div>                <!-- [123] Mounting point for the dynamic JS seat injector. -->
        <div id="passenger-details-section" class="passenger-details-section"> <!-- [124] Personal identification module. -->
            <h3 style="font-size:1.25rem; margin-bottom:25px;">Traveller Identification</h3> <!-- [125] label. -->
            <div id="passenger-info-container"></div>                <!-- [126] Injection point for dynamic data cards. -->
        </div>                                                       <!-- [127] end data section. -->
        <div style="margin-top:40px; display:flex; gap:15px; justify-content:flex-end; position:sticky; bottom:-40px; background:#ffffff; padding:25px 0; border-top:1px solid #f1f5f9;"> <!-- [128] control toolbar. -->
            <button class="button regular-button" onclick="closeSeatMap()" style="background:#f1f5f9; color:#475569;">Cancel Action</button> <!-- [129] exit. -->
            <button id="confirm-booking-btn" class="button regular-button pink-background" disabled onclick="submitBooking()">Finalize Booking</button> <!-- [130] finalize. -->
        </div>                                                       <!-- [131] end toolbar. -->
    </div>                                                           <!-- [132] end modal card. -->
</div>                                                               <!-- [133] end overlay. -->

<div style="height: 100px;"></div>                                   <!-- [134] Scrolling buffer. -->
    <script src="js/footer.js"></script>                                 <!-- [135] site footer script. -->
    <script src="js/table_manager.js"></script>

<script>                                                             /* [136] Start high-orchestration Client-Side Application Logic. */
let currentRouteId = null;                                           // [137] Track the global state: Trip currently in-focus.
let selectedSeats = [];                                              // [138] Track the global state: Dynamic array of picked seats.

// [1] FUNCTION: openSeatMap
// Logical role: Triggers the reservation flow, fetches live occupancy, and reveals the modal.
function openSeatMap(route_id, max_passengers) {
    currentRouteId = route_id; // [2] Save the targeted trip ID for server submission.
    selectedSeats = [];        // [3] Reset selection state for a fresh booking session.
    updateBookingButton();     // [4] Refresh button state (locked until seat picked).
    document.body.style.overflow = 'hidden'; // [5] UI: Prevent page scrolling behind the modal.
    
    // [6] Polling: Fetch real-time busy seats via AJAX to prevent double-booking.
    fetch(`op_get_occupied_seats.php?route_id=${route_id}`)
        .then(res => res.json())
        .then(data => {
            const occupiedList = data.occupied || []; 
            // [7] Orchestration: Pass vehicle capacity and busy seats to the generator.
            generateLayout(max_passengers, occupiedList); 
            document.getElementById('seat-modal').style.display = 'block'; // [8] Reveal the interactive deck.
        })
        .catch(err => alert('Seat Map Error: ' + err.message));
}

// [9] FUNCTION: generateLayout
// Logical role: Builds the virtual vehicle grid and identifies 'Occupied' vs 'Available' units.
function generateLayout(total, occupied) {
    const container = document.getElementById('bus-layout');
    container.innerHTML = ''; // [10] Clear the previous trip's layout from memory.
    for (let i = 1; i <= total; i++) {
        const seatNo = `S${i}`;
        const currentGridIndex = container.children.length + 1;
        
        // [11] AISLE LOGIC: Inject a physical walkway gap every 2 seats (column 3).
        if (currentGridIndex % 5 === 3) {
            const spacer = document.createElement('div');
            spacer.className = 'aisle-spacer';
            container.appendChild(spacer);
        }

        const seatNode = document.createElement('div');
        // [12] OCCUPANCY LOGIC: If the seat ID exists in the busy list, mark it as 'Occupied'.
        if (occupied.includes(seatNo)) {
            seatNode.className = 'seat occupied'; // [13] Highlight red and disable interaction.
            seatNode.innerText = seatNo;
        } else {
            // [14] AVAILABILITY LOGIC: If seat is free, apply interaction listeners.
            seatNode.className = 'seat available';
            seatNode.innerText = seatNo;
            seatNode.onclick = () => toggleSeatSelection(seatNode, seatNo); // [15] Link click event to status toggle.
        }
        container.appendChild(seatNode);
    }
}                                                                    // [176] close function.

function toggleSeatSelection(element, seatNo) {                      // [177] Logic: State-Machine for seat selection toggling.
    if (selectedSeats.includes(seatNo)) {                            // [178] state check: is seat already in the cart?
        selectedSeats = selectedSeats.filter(s => s !== seatNo);      // [179] remove logic: filter seat out of the array.
        element.classList.replace('selected', 'available');          // [180] UI update: return to white highlight.
    } else {                                                         // [181] else: adding new seat to cart.
        selectedSeats.push(seatNo);                                  // [182] add logic: append seat ID to state array.
        element.classList.replace('available', 'selected');          // [183] UI update: switch to green highlight.
    }                                                                // [184] close state toggle.
    updatePassengerDataForms();                                      // [185] cascade: update passenger data forms based on selection.
    updateBookingButton();                                           // [186] cascade: update finalize button visibility.
}                                                                    // [187] close function.

function updatePassengerDataForms() {                                 // [188] UI orchestration: manage dynamic data entry cards for passengers.
    const container = document.getElementById('passenger-info-container'); // [189] Locate form wrapper node.
    const section = document.getElementById('passenger-details-section'); // [190] Locate section group node.
    if (selectedSeats.length === 0) {                                // [191] logic catch: if nothing is selected, hide the forms.
        section.style.display = 'none';                              // [192] toggle visibility.
        return;                                                      // [193] exit early.
    }                                                                // [194] close catch.
    section.style.display = 'block';                                 // [195] show module: user started selecting seats.
    const draftData = {};                                            // [196] memory: store temporary data to prevent type loss on refresh.
    container.querySelectorAll('.passenger-info-card').forEach(card => { // [197] capture phase: read existing inputs from the DOM.
        const s = card.dataset.seat;                                 // [198] identify card.
        draftData[s] = { name: card.querySelector('.p-name').value, age: card.querySelector('.p-age').value, id: card.querySelector('.p-id').value }; // [199] map data.
    });                                                              // [200] close capture.
    container.innerHTML = '';                                        // [201] DOM destruction: wipe existing forms for clean rebuild.
    selectedSeats.sort((a,b) => parseInt(a.substring(1)) - parseInt(b.substring(1))).forEach(seatId => { // [202] rendering phase: iterate through selections.
        const card = document.createElement('div');                  // [203] create card unit.
        card.className = 'passenger-info-card';                      // [204] apply style.
        card.dataset.seat = seatId;                                  // [205] tag metadata for identification.
        const saved = draftData[seatId] || { name: '', age: '', id: '' }; // [206] hydration: restore data from memory draft if available.
        card.innerHTML = `<h4>Reservation: Seat ${seatId}</h4><div class="info-grid"><div class="info-group"><label>Traveller Name</label><input type="text" class="p-name" value="${saved.name}" placeholder="Full Legal Name" required oninput="updateBookingButton()"></div><div class="info-group"><label>Age</label><input type="number" class="p-age" value="${saved.age}" placeholder="e.g. 25" required oninput="updateBookingButton()"></div><div class="info-group"><label>ID / Identity Number</label><input type="text" class="p-id" value="${saved.id}" placeholder="ID or Passport" required oninput="updateBookingButton()"></div></div>`; // [207] build internal HTML template.
        container.appendChild(card);                                 // [208] finalize injection: mount card into form container.
    });                                                              // [209] close rendering iteration.
}                                                                    // [210] close function.

function updateBookingButton() {                                     // [211] Function to synchronize the state of the Finalize button based on seat selection.
    const btn = document.getElementById('confirm-booking-btn');      // [212] Locate the Finalize Booking button in the DOM.
    btn.disabled = (selectedSeats.length === 0);                     // [213] Disable the button ONLY if no seats are selected; otherwise, enable it to allow validation triggers.
}                                                                    // [220] End of the state synchronization function.                                                                    // [220] close function.

function closeSeatMap() {                                            // [221] Exit: Terminate reservation flow and cleanup UI.
    document.getElementById('seat-modal').style.display = 'none';    // [222] Set the CSS display property of the seat-modal element to 'none' to remove it from the visual document flow.
    document.body.style.overflow = 'auto';                           // [223] Set the document body overflow property back to 'auto' to re-enable scrolling on the main page.
}                                                                    // [224] Close the function that handles modal closure and state cleanup.

function submitBooking() {                                           // [225] Function to submit booking data to the server via AJAX.
    const system_user_id = document.getElementById('target_user_id').value; // [226] Get the ID of the user or agent making the booking.
    const payloadArray = [];                                         // [227] Create an empty array to store passenger details.
    let validationFailed = false;                                    // [228] Flag to track if any validation errors occur.

    const cards = document.querySelectorAll('.passenger-info-card'); // [229] Select all passenger info cards currently rendered in the DOM.
    for (let i = 0; i < cards.length; i++) {                         // [229.1] Begin looping through every passenger identification card.
        const card = cards[i];                                       // [229.2] Reference the specific card instance for the current iteration.
        const nameInput = card.querySelector('.p-name');             // [229.3] Locate the traveller name input element within this card.
        const ageInput = card.querySelector('.p-age');               // [229.4] Locate the age number input element within this card.
        const idInput = card.querySelector('.p-id');                 // [229.5] Locate the ID/Identity text input element within this card.

        if (!nameInput.checkValidity()) {                            // [231] Check if the browser considers the name input valid (e.g., not empty).
            nameInput.reportValidity();                              // [231.1] Trigger the browser's native validation bubble message directly on the name input box.
            return;                                                  // [231.2] Exit the function immediately to halt submission until corrected.
        }                                                            // [231.3] End of name validity check.
        if (!ageInput.checkValidity()) {                             // [231.4] Check if the browser considers the age input valid.
            ageInput.reportValidity();                               // [231.5] Trigger the native validation bubble message directly on the age input box.
            return;                                                  // [231.6] Exit the function immediately to halt submission.
        }                                                            // [231.7] End of age validity check.
        if (!idInput.checkValidity()) {                              // [231.8] Check if the browser considers the identity input valid.
            idInput.reportValidity();                                // [231.9] Trigger the native validation bubble message directly on the ID input box.
            return;                                                  // [232.0] Exit the function immediately to halt submission.
        }                                                            // [232.1] End of identity validity check.

        payloadArray.push({ seat_id: card.dataset.seat, name: nameInput.value.trim(), age: ageInput.value.trim(), id: idInput.value.trim() }); // [232.8] Construct and append a passenger data object to the payload array.
    }                                                                // [233] Terminate the passenger card validation and serialization loop.

    if (!confirm(`Commit for ${selectedSeats.length}?`)) return;      // [235] Use the browser's native confirm dialog to ask the user if they wish to proceed with the specified number of seat bookings.
    
    // [236.1] Using the native fetch API to initiate an asynchronous HTTP POST request to the process_booking.php controller.
    // [236.2] JSON.stringify() is implemented here to serialize the JavaScript object containing booking metadata into a formatted JSON string. 
    // [236.3] This conversion is necessary because HTTP data transmissions are strictly text-based, allowing the server-side PHP to decode the payload.
    fetch('process_booking.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ route_id: currentRouteId, user_id: system_user_id, passengers: payloadArray }) }) // [236.4] Execute the network transmission by sending the stringified JSON payload to the server.
    .then(res => {                                                   // [237] Capture the initial response object returned by the server.
        if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);   // [237.1] Validate the HTTP status code; throw a new Error if the server returns a failure status (like 500 or 404).
        return res.text();                                           // [237.2] Extract the response body content as raw text for initial inspection and robustness.
    })
    .then(text => {                                                  // [237.3] Pass the raw response text into a safety-checked JSON parsing workflow.
        try {
            return JSON.parse(text);                                 // [237.4] Attempt to convert the server's text response back into a structured JavaScript object.
        } catch (e) {
            console.error('Non-JSON Payload Detected:', text);       // [237.5] Log the raw text to the console for debugging if it is not valid JSON.
            throw new Error('System outputted an invalid response format. Content: ' + text.substring(0, 100)); // [237.6] Fire a detailed exception containing a snippet of the malformed response.
        }
    })
    .then(result => {                                                // [238] Final processing stage for the successfully parsed server outcome.
        if (result.success) { alert(result.message); window.location.href = result.redirect || 'view_user_history.php'; } // [239] If the operation succeeded, display the success message and redirect the browser to the passenger's history portal.
        else { alert('Reservation Failed: ' + result.message); }    // [240] If the operation failed server-side, display a detailed error alert explaining why.
    })                                                               // [241] Terminate the successful promise resolution chain.
    .catch(error => { console.error('Flow Crash:', error); alert('System Error: ' + error.message); }); // [242] Universal error boundary to catch and alert any coding failures or network errors during transmission.
}                                                                    // [243] Close the submitBooking function logic block.
</script>                                                            <!-- [244] End the internal JavaScript logic definition block. -->
</body>                                                              <!-- [245] Close the visible document body section of the HTML. -->
</html>                                                              <!-- [246] Formal termination of the HTML document structure. -->
