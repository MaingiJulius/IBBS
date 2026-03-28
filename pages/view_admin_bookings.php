<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * ADMINISTRATION: BOOKING COMMAND CENTER (view_admin_bookings.php)  // [4] Title identifying this script as the master booking dashboard.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This page provides administrative staff with a real-time // [6] Description of the script's primary role for the office staff.
 * view of every passenger ticket in the system.                     // [7] Scope of data visibility: entire system registry.
 * Features: Multi-table reporting, ticket cancellation, and deletion. // [8] Summary of the core features and management actions.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import the database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume the user session to identify the requester.

/* --- [3] ACCESS WALL: STAFF ONLY area --- */                       // [13] Marker for the primary security access control check.
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'ADMIN' && $_SESSION['role'] != 'AGENT')) { // [14] Verify staff role exists.
    die("Access Denied: High-Level Personnel Authentication Required for Ledger Access."); // [15] Halt execution if unauthorized.
}                                                                    // [16] Close security validation boundary.

/* --- [4] TRANSACTIONAL LOGIC: Ticket Cancellation --- */           // [17] Marker for processing the 'VOID' action via URL parameter.
if (isset($_GET['cancel_booking'])) {                                // [18] Detect if the 'cancel_booking' instruction exists in the query string.
    $bid = $_GET['cancel_booking'];                                  // [19] Capture the target booking ID from the GET global array.
    $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'CANCELLED' WHERE booking_id = ?"); // [20] Prepare SQL command.
    $stmt->bind_param("i", $bid);                                    // [21] Safely bind the integer ID to prevent SQL injection.
    $stmt->execute();                                                // [22] Execute the status update on the database server.
    $stmt->close();                                                  // [23] Release the statement resource to save server memory.
    header("Location: view_admin_bookings.php?msg=Ticket sequence has been successfully CANCELLED."); // [24] Redirect with success message.
    exit();                                                          // [25] Halt further execution to ensure redirect behavior.
}                                                                    // [26] Close the cancellation logic block.

/* --- [5] DESTRUCTIVE LOGIC: Booking Record Delete --- */           // [27] Marker for hard deletion of records via URL parameter.
if (isset($_GET['delete_booking'])) {                                // [28] Detect if the 'delete_booking' instruction exists in the query string.
    $bid = $_GET['delete_booking'];                                  // [29] Capture the target booking ID for hard destruction.
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?"); // [30] Prepare the destructive SQL DELETE command.
    $stmt->bind_param("i", $bid);                                    // [31] Safely bind the integer ID to the prepared statement.
    $stmt->execute();                                                // [32] Execute the deletion on the database server.
    $stmt->close();                                                  // [33] Reach completion and close the statement resource.
    header("Location: view_admin_bookings.php?msg=Historical record permanently DELETED."); // [34] Redirect with success alert.
    exit();                                                          // [35] Halt execution to ensure the browser follows the redirect.
}                                                                    // [36] Close the deletion logic block.
?>                                                                   <!-- [37] Close PHP processing and prepare for HTML rendering. -->

<!DOCTYPE html>                                                         <!-- [38] Define the document type as standard HTML5. -->
<html lang="en">                                                     <!-- [39] Root element defining the content language as English. -->
<head>                                                               <!-- [40] Head section containing non-visible document metadata. -->
    <meta charset="UTF-8">                                           <!-- [41] Declare UTF-8 character encoding for global data support. -->
    <title>Master Booking Ledger - Wema Travellers Hub</title>         <!-- [42] Website title displayed in the browser tab. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [43] Ensure responsive scaling on mobile devices. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [44] Load shared CSS component tokens. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [45] Load global branding layout variables. -->
    <style>                                                          /* [46] Open internal CSS block for page-specific styling. */
        .view-container {                                            /* [47] Style for the main white content card. */
            max-width: 1200px;                                       /* [48] Limit width for optimized tabular reading. */
            margin: 20px auto;                                       /* [49] Vertical gap and horizontal centering. */
            padding: 30px;                                           /* [50] Internal cushioning around the data grid. */
            background: #ffffff;                                     /* [51] High-contrast white background. */
            border-radius: 12px;                                     /* [52] Modern soft-rounded aesthetics. */
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);                /* [53] Soft elevation shadow for visual depth. */
        }                                                            /* [54] Close container style. */
        .back-btn-container { padding: 20px; max-width: 1200px; margin: 0 auto; } /* [55] layout for the return link. */
        .crud-table { width: 100%; border-collapse: collapse; margin-top: 25px; } /* [56] primary data table structure. */
        .crud-table th, .crud-table td { padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: left; } /* [57] cell layout. */
        .crud-table th { background-color: var(--purple); color: #ffffff; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; } /* [58] header branding. */
        .action-btn { padding: 8px 14px; border-radius: 6px; text-decoration: none; color: #ffffff; font-size: 0.85rem; margin-right: 8px; font-weight: 700; display: inline-block; } /* [59] button base. */
        .btn-delete { background-color: #ef4444; }                    /* [60] red for delete. */
        .btn-cancel { background-color: #f59e0b; }                    /* [61] orange for cancel. */
    </style>                                                         <!-- [62] Terminate internal CSS block. -->
</head>                                                              <!-- [63] Close head section. -->
<body>                                                               <!-- [64] Begin visible body. -->
<script src="js/header2.js"></script>                                <!-- [65] Inject site-wide navigation header. -->
<div style="height: 100px;"></div>                                   <!-- [66] Neutralize fixed header offset. -->
<div class="back-btn-container">                                      <!-- [67] breadcrumb wrapper. -->
    <a href="dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); border-radius: 50px; color: white; padding: 12px 30px; font-weight: 700;">← Back to Command Center</a> <!-- [68] nav link. -->
</div>                                                               <!-- [69] close breadcrumb. -->

<div class="view-container">                                         <!-- [70] content card. -->
    <h2 style="color: var(--purple); margin-bottom: 15px;">🎟️ Booking Transaction Ledger</h2> <!-- [71] title. -->
    <?php if(isset($_GET['msg'])): ?>                                <!-- [72] check for session-linked feedback alerts. -->
        <p style="background: #f0fff4; color: #22543d; padding: 15px; border-radius: 8px; border-left: 5px solid #38a169; font-weight: 600;"> ✅ <?= htmlspecialchars($_GET['msg']) ?> </p> <!-- [73] show alert. -->
    <?php endif; ?>                                                   <!-- [74] close alert check. -->

    <table class="crud-table">                                       <!-- [75] Open data grid. -->
        <thead>                                                      <!-- [76] Table head labels. -->
            <tr>                                                     <!-- [77] row. -->
                <th>Ref ID</th><th>Processed</th><th>Passenger Dossier</th><th>Travel Path</th><th>Seat ID</th><th>Financial State</th><th>Control Deck</th> <!-- [78] cells. -->
            </tr>                                                    <!-- [79] end row. -->
        </thead>                                                     <!-- [80] close head. -->
        <tbody>                                                      <!-- [81] Begin data body. -->
            <?php                                                     // [82] Re-open PHP for record iteration.
            $sql = "SELECT b.booking_id, b.booking_time, u.first_name as booker_first, u.last_name as booker_last, b.passenger_name, b.passenger_age, b.passenger_dob, b.passenger_id_number, r.from_location, r.to_location, b.seat_number, b.booking_status FROM bookings b JOIN users u ON b.user_id = u.user_id JOIN routes r ON b.route_id = r.route_id ORDER BY b.booking_id DESC"; // [83] Main query.
            $result = $conn->query($sql);                            // [84] Execute query.
            while($row = $result->fetch_assoc()):                    // [85] Start row loop.
            ?>                                                        <!-- [86] Close PHP for HTML row output. -->
            <tr>                                                     <!-- [87] Data row. -->
                <td><strong><?= $row['booking_id'] ?></strong></td>  <!-- [88] ID cell. -->
                <td style="color: #64748b; font-size: 0.9em;"><?= $row['booking_time'] ?></td> <!-- [89] Time cell. -->
                <td>                                                  <!-- [90] Passenger detail complex cell. -->
                    <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($row['passenger_name']) ?></div> <!-- [91] name. -->
                    <?php if ($row['passenger_age'] > 0 && !empty($row['passenger_id_number'])): ?>         <!-- [92] dossiers match. -->
                        <div style="font-size: 0.8em; color: #64748b;">Age: <?= $row['passenger_age'] ?> <?php if($row['passenger_dob']): ?>(DOB: <?= $row['passenger_dob'] ?>)<?php endif; ?> | ID: <?= htmlspecialchars($row['passenger_id_number']) ?></div> <!-- [93] bio info. -->
                    <?php else: ?>                                   <!-- [94] incomplete dossier. -->
                        <div style="color:#e53e3e; font-size:0.8em; font-weight:800; background:#fff5f5; padding:5px; border-radius:4px; margin-top:5px;">⚠️ DATA ANOMALY: MISSING PII (DOB/ID)</div> <!-- [95] error alert. -->
                        <form action="update_passenger_dob.php" method="POST" style="margin-top:8px; display: flex; flex-direction: column; gap: 5px;"> <!-- [96] repair form. -->
                            <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                            <div style="display: flex; gap: 5px;">
                                <input type="date" name="dob" placeholder="DOB" style="border:1px solid #cbd5e0; padding:4px; font-size:0.7em; border-radius:4px; flex: 1;">
                                <input type="text" name="national_id" placeholder="National ID" style="border:1px solid #cbd5e0; padding:4px; font-size:0.7em; border-radius:4px; flex: 1;">
                                <button type="submit" style="font-size:0.7em; background:var(--purple); color:white; border:none; padding:4px 8px; border-radius:4px; cursor:pointer;">UPDATE</button> <!-- [97] action. -->
                            </div>
                        </form>                                      <!-- [98] close repair. -->
                    <?php endif; ?>                                  <!-- [99] end anomaly check. -->
                    <div style="color: #94a3b8; font-size: 0.75em; font-style: italic; margin-top: 5px;">Authored by: <?= htmlspecialchars($row['booker_first'] . ' ' . $row['booker_last']) ?></div> <!-- [100] attribution. -->
                </td>                                                <!-- [101] end dossier. -->
                <td><?= htmlspecialchars($row['from_location'] . ' → ' . $row['to_location']) ?></td> <!-- [102] route. -->
                <td><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 4px; font-weight: 800; font-size: 1rem;"><?= $row['seat_number'] ?></span></td> <!-- [103] seat. -->
                <td><span style="color: <?= ($row['booking_status'] == 'CANCELLED') ? '#ef4444' : '#10b981' ?>; font-weight:900; font-size: 0.85em; text-transform: uppercase;"><?= $row['booking_status'] ?></span></td> <!-- [104] status cell. -->
                <td>                                                  <!-- [105] action cell. -->
                    <?php if($row['booking_status'] != 'CANCELLED'): ?> <!-- [106] void logic. -->
                        <a href="?cancel_booking=<?= $row['booking_id'] ?>" class="action-btn btn-cancel" onclick="return confirm('CANCEL TICKET: Confirm cancellation?')">CANCEL</a> <!-- [107] cancel link. -->
                    <?php endif; ?>                                  <!-- [108] end void. -->
                    <a href="?delete_booking=<?= $row['booking_id'] ?>" class="action-btn btn-delete" onclick="return confirm('DELETE WARNING: Irreversible. Proceed?')">DELETE</a> <!-- [109] delete link. -->
                </td>                                                <!-- [110] end actions. -->
            </tr>                                                    <!-- [111] end data row. -->
            <?php endwhile; ?>                                       <!-- [112] End iteration. -->
        </tbody>                                                     <!-- [113] end body. -->
    </table>                                                         <!-- [114] end table. -->
</div>                                                               <!-- [115] end card. -->
<div style="height: 120px;"></div>                                   <!-- [116] buffer. -->
<script src="js/footer.js"></script>                                 <!-- [117] footer injection. -->
</body>                                                              <!-- [118] end body. -->
</html>                                                              <!-- [119] end HTML document. -->


