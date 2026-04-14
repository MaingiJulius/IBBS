<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * PASSENGER PORTAL: MY DIGITAL TICKETS (view_tickets.php)           // [4] Title identifying this script as the personal ticket wallet.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script serves as a personal dashboard for passengers // [6] Primary goal: provide travelers with a digital boarding pass vault.
 * to view, manage, and present their valid travel tickets.         // [7] Functionality: retrieval and display of individual bookings.
 * Key Features: Secure Token Display, Real-time status, and Cancellation. // [8] Features: QR-token validation and voiding logic.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume user session to identify the passenger.

if (!isset($_SESSION['user_id'])) {                                  // [13] Verify user is authenticated before exposing travel records.
    header("Location: login.html");                                  // [14] Redirect unauthorized visitors to the security gateway.
    exit();                                                          // [15] Halt further code execution to protect user data.
}                                                                    // [16] Close authentication check.

$user_id = $_SESSION['user_id'];                                     // [17] Map the active session identifier to a local variable for queries.

/**                                                                  // [18] Documentation for multi-table analytical query.
 * SQL ARCHITECTURE:                                                 // [19] Logic header for data retrieval.
 */                                                                  // [20] Close documentation block.
/* --- [DATABASE FETCHING: PASSENGER RECORDS] --- */
// [1] Define the target Query: We perform a 3-table JOIN (bookings + routes + buses).
// We fetch 'FROM/TO' locations and 'DEPARTURE' times from the routes table,
// and 'BUS NAME' from the buses table, filtered specifically for the logged-in user.
$sql = "SELECT b.*, r.from_location, r.to_location, r.departure_date, r.departure_time, bs.bus_name 
        FROM bookings b 
        JOIN routes r ON b.route_id = r.route_id 
        JOIN buses bs ON b.bus_id = bs.bus_id 
        WHERE b.user_id = ? AND b.booking_status = 'PAID' 
        ORDER BY r.departure_date DESC"; 

$stmt = $conn->prepare($sql);                                        // Prepare template.
$stmt->bind_param("i", $user_id);                                    // Bind user ID securely.
$stmt->execute();                                                    // [24] Execute query to fetch finalized travel tickets.
$result = $stmt->get_result();                                       // [25] Capture the resultant rows into a traversable data object.
?>                                                                   <!-- [26] Close PHP logic and prepare for document rendering. -->
                                                                     <!-- [L41] Blank line for structural separation. -->

<!DOCTYPE html>                                                         <!-- [27] Declare document as standard HTML5. -->
<html lang="en">                                                     <!-- [28] Define language for accessibility. -->
<head>                                                               <!-- [29] Metadata and resource head. -->
    <meta charset="UTF-8">                                           <!-- [30] Set character encoding for universal text support. -->
    <title>My Boarding Passes - Wema Travellers</title>                 <!-- [31] Website title in the tab. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [32] Responsive viewport settings. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [33] Import core styling tokens. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [34] Global theme and layout variables. -->
    <style>                                                          /* [35] Page-specific CSS block. */
        .ticket-container { max-width: 800px; margin: 50px auto; padding: 20px; } /* [36] Wallet wrapper. */
        .ticket-card { background: #ffffff; border-radius: 16px; padding: 30px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-left: 12px solid var(--purple); transition: transform 0.3s ease; position: relative; overflow: hidden; } /* [37] Card style. */
        .ticket-card:hover { transform: translateY(-3px); }          /* [38] Hover interaction. */
        .ticket-info h3 { margin: 0 0 10px 0; color: var(--purple); font-size: 1.4rem; } /* [39] Destination title. */
        .ticket-info p { margin: 5px 0; color: #4a5568; line-height: 1.5; } /* [40] Meta-data rows. */
        .ticket-qr-section { display: flex; flex-direction: column; align-items: center; gap: 10px; min-width: 160px; } /* [41] Align action buttons vertically. */
        .ticket-qr-container { 
            padding: 10px;                                           /* [42] Internal spacing around content. */
            background: white;                                       /* [43] Contrast background color. */
            border: 1px solid #e2e8f0;                               /* [44] Subtle border for definition. */
            border-radius: 8px;                                      /* [45] Slightly rounded corners for modern look. */
            width: 120px;                                            /* [46] Fixed width for container consistency. */
            height: 120px;                                           /* [47] Fixed height for container consistency. */
            display: flex;                                           /* [48] Enable flexbox for center alignment. */
            align-items: center;                                     /* [49] Center children vertically. */
            justify-content: center;                                 /* [50] Center children horizontally. */
        }
        .booking-id-badge {                                          /* [51] Style for the ID reference tag. */
            background: var(--purple);                               /* [52] Use theme color for branding. */
            color: white;                                            /* [53] High-contrast text color. */
            padding: 4px 12px;                                       /* [54] Cushioned inner spacing. */
            border-radius: 20px;                                     /* [55] Capsule-shaped aesthetics. */
            font-size: 0.8rem;                                       /* [56] Compact font size. */
            font-weight: 700;                                        /* [57] Bold font weight for visibility. */
            margin-bottom: 5px;                                      /* [58] Gap below the badge. */
            display: inline-block;                                   /* [59] Allow padding/width control while inline. */
        }
        
        /* Print Styles - Logic for traditional browser-based printing */
        @media print {                                               /* [60] Definitions for physical paper printing. */
            body * { visibility: hidden; }                           /* [61] Hide everything by default for a clean state. */
            #print-area, #print-area * { visibility: visible; }      /* [62] Reveal ONLY the designated print container. */
            #print-area {                                            /* [63] Positioning for the print-only area. */
                position: absolute;                                  /* [64] Move to true document origin. */
                left: 0;                                             /* [65] Align to left paper edge. */
                top: 0;                                              /* [66] Align to top paper edge. */
                width: 100%;                                         /* [67] Span full width of the page. */
                margin: 0;                                           /* [68] Reset margins for precision. */
                padding: 20px;                                       /* [69] Internal padding for border gap. */
                background: white;                                   /* [70] Ensure white background on paper. */
            }
            .ticket-card {                                           /* [71] Adjust card styling for ink efficiency. */
                border: 2px solid #edf2f7;                           /* [72] Swap shadow for a thin line border. */
                box-shadow: none;                                    /* [73] Disable shadows to prevent dark ink blobs. */
                margin: 0;                                           /* [74] Reset external margins. */
                padding: 30px;                                       /* [75] Maintain internal cushioning. */
                border-radius: 16px;                                 /* [76] Retain modern rounded aesthetics. */
                border-left: 15px solid #9a4d9a;                      /* [77] Thick left border for color branding. */
                display: flex !important;                            /* [78] Force flex layout on paper. */
                flex-direction: row !important;                      /* [79] Ensure horizontal info/action split. */
                justify-content: space-between;                      /* [80] Push content to paper edges. */
                align-items: center;                                 /* [81] Vertically center align text. */
            }
            .button, .back-btn, .print-btn, #banner, #nav-links { display: none !important; } /* [82] Hide UI interactions. */
            .ticket-qr-container { display: block !important; border: 1px solid #e2e8f0; } /* [83] Ensure content container is visible. */
        }
    </style>                                                         
    <script>
        /**
         * FUNCTION: printSelectedTicket
         * Logic: Extracts ticket data from the DOM, creates a temporary HTML window, 
         * and triggers the browser's native print manager.
         */
        function printSelectedTicket(bookingId) {
            // [1] Locate the source ticket container by its dynamic ID.
            const ticketCard = document.getElementById('ticket-' + bookingId);
            if (!ticketCard) {
                alert('System Error: Ticket source not found.');
                return;
            }
            
            // [2] Create a memory-only clone of the ticket to prevent damaging the live UI.
            const clonedCard = ticketCard.cloneNode(true);
            
            // [3] Strip out UI buttons (Print/Cancel) and scripts from the cloned content.
            clonedCard.querySelectorAll('button, .button, a, script').forEach(el => el.remove());

            // [4] Open a new browser window context for the printing stage.
            const printWindow = window.open('', '_blank', 'width=850,height=900');
            
            // [5] Construct the print-optimized HTML document.
            printWindow.document.write('<!DOCTYPE html><html><head><title>Boarding Pass #' + bookingId + '</title>');
            
            // [CSS] Print Styling: Hides browser headers/footers and centers the content.
            printWindow.document.write('<style>@page { size: auto; margin: 0; } body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #1a202c; background: #fff; margin: 1cm; } .ticket-card { border: 4px double #9a4d9a; border-radius: 12px; padding: 40px; max-width: 600px; margin: auto; } .company-name { text-align: center; color: #9a4d9a; margin-bottom: 25px; font-size: 30px; font-weight: 800; text-transform: uppercase; border-bottom: 3px solid #f7fafc; padding-bottom: 15px; } .ticket-info h3 { color: #2d3748; font-size: 24px; margin: 0 0 15px 0; } .ticket-info p { margin: 12px 0; font-size: 18px; color: #4a5568; line-height: 1.6; } .booking-id-badge { background: #9a4d9a; color: white; padding: 6px 18px; border-radius: 50px; font-weight: bold; margin-bottom: 20px; display: inline-block; font-size: 1rem; } .footer-note { margin-top: 30px; font-style: italic; color: #718096; font-size: 0.9rem; text-align: center; border-top: 1px solid #edf2f7; padding-top: 20px; }</style></head><body>');
            
            // [Content] Inject the centered Company Name and the cleaned cloned ticket info.
            printWindow.document.write('<div class="ticket-card"><div class="company-name">WEMA TRAVELLERS</div><div class="ticket-info">' + clonedCard.querySelector('.ticket-info').innerHTML + '</div><div class="footer-note">Verification: Please present your National ID / Passport for boarding.</div></div>');
            
            // [Action] Trigger the print dialog specifically once the window content finishes loading.
            printWindow.document.write('<script>window.onload = function() { window.print(); window.close(); };<\/script></body></html>');
            printWindow.document.close();                                    // [L142] Standardize the stream end for the browser window.
        }                                                            // [L143] Close the printSelectedTicket function block.
    </script>                                                        <!-- [L144] Terminate the JavaScript logic section. -->
</head>                                                              <!-- [44] Close the document head metadata section. -->
                                                                     <!-- [L146] Blank line for readability. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">               <!-- [L147] Open body with a dynamic class based on user role (e.g. passenger). -->
<script src="js/header2.js"></script>                                <!-- [46] Inject site-wide navigation bar and logo component. -->
<div style="height: 100px;"></div>                                   <!-- [47] Fixed header offset to prevent content overlap. -->
                                                                     <!-- [L150] Structural spacing gap. -->
<div class="ticket-container">                                         <!-- [48] Open the primary UI wallet container. -->
    <h2 style="text-align:center; color: var(--purple); margin-bottom: 40px; font-weight: 800;">🎫 My Digital Travel Wallet</h2> <!-- [49] Main page heading. -->
    <?php if ($result->num_rows > 0): ?>                             <!-- [50] Conditional check: Iterate only if tickets exist in DB. -->
        <?php while($ticket = $result->fetch_assoc()): ?>            <!-- [L154] Start row loop to fetch individual ticket details. -->
            <div class="ticket-card" id="ticket-<?= $ticket['booking_id'] ?>"> <!-- [L155] Unique card container with dynamic ID. -->                <div class="ticket-info">                            <!-- [53] detail section. -->
                    <div class="booking-id-badge">ID: #<?= $ticket['booking_id'] ?></div>
                    <h3><?= htmlspecialchars($ticket['from_location']) ?> → <?= htmlspecialchars($ticket['to_location']) ?></h3> 
                    <p><strong>📅 Departure:</strong> <?= $ticket['departure_date'] ?> | <strong>⏰ Time:</strong> <?= $ticket['departure_time'] ?></p> 
                    <p><strong>💺 Seat:</strong> <span style="color:var(--purple); font-weight:bold;"><?= $ticket['seat_number'] ?></span> | <strong>🚌 Coach:</strong> <?= htmlspecialchars($ticket['bus_name']) ?></p> 
                    <p><strong>👤 Traveler:</strong> <?= htmlspecialchars($ticket['passenger_name'] ?: $_SESSION['name']) ?></p> <!-- [L161] Display passenger name or account default. -->
                    <p>                                              <!-- [L162] Open paragraph for identity verification details. -->
                        <strong>🆔 ID/Passport:</strong>             <!-- [L163] Field label for identity document. -->                        <!-- [RETROACTIVE UPDATE LOGIC] -->
                        <!-- If passenger ID is missing, provide an inline form to capture data. -->
                        <?php if (empty($ticket['passenger_id_number'])): ?>
                            <form action="op_update_passenger_details.php" method="POST" style="display:inline-block; margin:0; vertical-align:middle;">
                                <input type="hidden" name="booking_id" value="<?= $ticket['booking_id'] ?>">
                                <input type="hidden" name="redirect_to" value="view_tickets.php">
                                <!-- ID Field -->
                                <input type="text" name="passenger_id_number" placeholder="Enter ID..." required style="width:100px; padding:2px 5px; border:1px solid #cbd5e0; border-radius:4px; font-size:0.8rem;">
                                | <strong>🎂 Age:</strong>
                                <!-- Age Field -->
                                <input type="number" name="passenger_age" placeholder="Age" required style="width:50px; padding:2px 5px; border:1px solid #cbd5e0; border-radius:4px; font-size:0.8rem;">
                                <!-- Save Button: Transmits to op_update_passenger_details.php -->
                                <button type="submit" style="background:#48bb78; color:white; border:none; padding:2px 8px; border-radius:4px; cursor:pointer; font-size:0.75rem; margin-left:5px; font-weight:bold;">Save Details</button>
                            </form>
                        <?php else: ?>
                            <!-- Display verified saved details -->
                            <?= htmlspecialchars($ticket['passenger_id_number']) ?> | <strong>🎂 Age:</strong> <?= htmlspecialchars($ticket['passenger_age']) ?>
                        <?php endif; ?>
                    </p>                                             <!-- [L182] Close the passenger data paragraph. -->
                </div>                                               <!-- [183] Close the ticket information text block. -->
                <div class="ticket-qr-section">                              <!-- [L184] Open the secondary action/QR section. -->
                    <div style="margin-top: 10px; display: flex; flex-direction: column; gap: 8px; width: 100%;"> <!-- [L185] Action button wrapper. -->
                        <button class="print-btn" onclick="printSelectedTicket(<?= $ticket['booking_id'] ?>)" style="background: #9a4d9a; color: white; border: none; padding: 10px; border-radius: 50px; cursor: pointer; font-size: 0.85rem; font-weight: 700; box-shadow: 2px 2px 5px rgba(0,0,0,0.2);">🖨️ Print Ticket</button> <!-- [L186] Print trigger button. -->
                        <a href="user_cancel_ticket.php?booking_id=<?= $ticket['booking_id'] ?>" class="button" style="background-color: #f59e0b; color: white; padding: 10px; text-decoration: none; border-radius: 50px; font-size: 0.85rem; font-weight: 700; text-align: center; box-shadow: 2px 2px 5px rgba(0,0,0,0.2);" onclick="return confirm('CANCEL: Proceed irreversibly?')"> ❌ Cancel </a> <!-- [L187] Ticket cancellation link. -->
                    </div>                                           <!-- [L188] Close action button wrapper. -->
                </div>                                               <!-- [L189] Close the QR/action section div. -->
            </div>                                                   <!-- [L190] Close the entire ticket card container. -->        <?php endwhile; ?>                                           <!-- [67] Terminate the while loop that iterates through and renders the user's active ticket result set. -->
    <?php else: ?>                                                   <!-- [68] Execute this alternative block if the database returned zero active booking results for the current logged-in user. -->
        <div class="empty-state">                                    <!-- [69] Open a styled div container wrapper to display the empty state placeholder content. -->
            <p style="color: #64748b; font-size: 1.1rem;">You haven't reserved any active tickets yet.</p> <!-- [70] Output a descriptive paragraph message informing the user that no active reservations were found in their account. -->
            <a href="book.php" class="button regular-button pink-background" style="text-decoration:none; display:inline-block; margin-top:20px;">Book Your First Trip →</a> <!-- [71] Render a stylized hyperlink button pointing to the book.php engine to encourage the user to make their first reservation. -->
        </div>                                                       <!-- [72] Close the empty state placeholder div container. -->
    <?php endif; ?>                                                  <!-- [73] Close the PHP conditional if/else structure for the ticket rendering logic. -->
</div>                                                               <!-- [74] Close the primary main-content div container that encapsulates the entire passenger ticket history interface. -->

<div style="height: 100px;"></div>                                   <!-- [75] Insert a structural spacer div with a height of 100 pixels to provide visual padding at the bottom of the content area. -->
<script src="js/footer.js"></script>                                 <!-- [76] Inject the external site-wide footer JavaScript component into the HTML document. -->
<a href="dashboard.php" style="display:block; text-align:center; margin-top:40px; color:var(--purple); font-weight:bold; text-decoration:none; font-size: 1.1rem;"> ← Return to Personal Dashboard </a> <!-- [77] Render a horizontal navigation link allowing the user to return to their personal account dashboard. -->
</body>                                                              <!-- [78] Close the visible body content section of the HTML document. -->
</html>                                                              <!-- [79] Formal termination and closure of the entire HTML document structure. -->
<?php                                                                // [80] Re-open PHP for final resource cleanup and session closure logic.
$stmt->close();                                                      // [81] Explicitly release the memory allocated to the prepared statement object on the database server.
$conn->close();                                                      // [82] Terminate the active connection bridge to the MySQL database server.
?>                                                                   <!-- [83] Final termination of the PHP script execution. -->
