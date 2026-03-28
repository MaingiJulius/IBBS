<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * PASSENGER PORTAL: TRAVEL HISTORY LEDGER (user_history.php)         // [4] Title identifying this script as the personal history record.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: Provides passengers with a historical audit of their      // [6] Description: bridge for users to see past and upcoming trips.
 * finalized travel transactions.                                  // [7] Goal: transparency for the traveler regarding past spend.
 * Source: Pulls data from the 'user_booking_history' database view. // [8] Data source: abstraction layer for complex joins.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume user session to identify the traveler.

$user_id = $_SESSION['user_id'] ?? null;                            // [13] Attempt to retrieve the unique identity of the logged-in user.

if (!$user_id) {                                                     // [14] Security Check: Intercept anonymous visitors.
    echo "Please log in to view your booking history.";              // [15] Inform user of authentication requirement.
    exit();                                                          // [16] Halt script execution to prevent data leakage.
}                                                                    // [17] Close guest exclusion block.

$sql = "SELECT * FROM user_booking_history WHERE user_id = ?";       // [18] Prepare the query to fetch user-specific historical records.
$stmt = $conn->prepare($sql);                                        // [19] Compile SQL template on server for secure execution.
$stmt->bind_param("i", $user_id);                                    // [20] Inject the session ID safely into the query placeholder.
$stmt->execute();                                                    // [21] Commit the data retrieval request to the database.
$result = $stmt->get_result();                                       // [22] Capture the resultant rows into a traversable data object.

if ($result->num_rows > 0) {                                         // [23] Check if user has a non-empty travel ledger.
    $booking_history = $result->fetch_all(MYSQLI_ASSOC);             // [24] Pull all records into a local associative array for loop.
} else {                                                             // [25] Logic Path: User has no recorded travel history.
    $booking_history = [];                                           // [26] Initialize as empty to prevent front-end loop errors.
}                                                                    // [27] Close history check.

$stmt->close();                                                      // [28] Explicitly release the statement resource memory.
$conn->close();                                                      // [29] Close database transition handle.
?>                                                                   <!-- [30] Close PHP processing and prepare for document rendering. -->

<!DOCTYPE html>                                                         <!-- [31] Define document as standard HTML5. -->
<html lang="en">                                                     <!-- [32] Root element identifying English as content language. -->
<head>                                                               <!-- [33] Document metadata head section. -->
    <meta charset="UTF-8">                                           <!-- [34] Declare UTF-8 for character set compatibility. -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">            <!-- [35] Compatibility flag for Internet Explorer rendering. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [36] Responsive scaling for mobile devices. -->
    <title>Your Booking History</title>                                 <!-- [37] Web page title label. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [38] Load global layout tokens. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [39] Load shared visual components. -->
    <link rel="stylesheet" href="css/entry-page.css">                <!-- [40] Load page-specific typography. -->
    <style>                                                          /* [41] Visual styles for the history dashboard. */
        .container { width: 80%; margin: 50px auto; padding: 20px; } /* [42] Main wrapper. */
        .header { text-align: center; margin-bottom: 20px; }         /* [43] Heading. */
        .table-container { overflow-y: scroll; max-height: 500px; }  /* [44] Scroll wrapper. */
        table { width: 100%; border-collapse: collapse; text-align: center; margin-bottom: 20px; } /* [45] Table grid. */
        th, td { padding: 12px; border: 1px solid #ddd; }            /* [46] cell padding. */
        th { background-color: #f2f2f2; font-weight: bold; }         /* [47] header style. */
        td { background-color: #fff; }                               /* [48] data row style. */
        .button { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; } /* [49] cmd button. */
        .button:hover { background-color: #45a049; }                 /* [50] hover action. */
        .back-button { padding: 10px 20px; background-color: #f44336; color: white; border: none; cursor: pointer; margin-top: 20px; text-decoration: none; display: inline-block; border-radius: 5px; } /* [51] alert button. */
        .back-button:hover { background-color: #da190b; }            /* [52] hover alert. */
        .no-bookings { text-align: center; margin-top: 20px; font-size: 1.2rem; color: #555; } /* [53] empty state. */
    </style>                                                         <!-- [54] End style block. -->
</head>                                                              <!-- [55] End head. -->

<body>                                                               <!-- [56] Begin visible body. -->
    <script src="js/header.js"></script>                                 <!-- [57] Inject primary navigation header. -->
    <div class="container">                                              <!-- [58] UI wrap. -->
        <h1 class="header">Your Booking History</h1>                      <!-- [59] Title. -->
        <?php if (!empty($booking_history)): ?>                        <!-- [60] check if records exist. -->
            <div class="table-container">                                <!-- [61] grid wrap. -->
                <table>                                                  <!-- [62] start table. -->
                    <thead><tr><th>Route</th><th>From Location</th><th>To Location</th><th>Departure Date</th><th>Time</th><th>Cost</th><th>Booking Time</th></tr></thead> <!-- [63] headers. -->
                    <tbody>                                              <!-- [64] data start. -->
                        <?php foreach ($booking_history as $row): ?>   <!-- [65] iterate through history array. -->
                            <tr>                                         <!-- [66] record row. -->
                                <td><?= htmlspecialchars($row['route_id']); ?></td> <!-- [67] ID cell. -->
                                <td><?= htmlspecialchars($row['from_location']); ?></td> <!-- [68] origin. -->
                                <td><?= htmlspecialchars($row['to_location']); ?></td> <!-- [69] destination. -->
                                <td><?= htmlspecialchars($row['departure_date']); ?></td> <!-- [70] date. -->
                                <td><?= htmlspecialchars($row['time']); ?></td> <!-- [71] departure time. -->
                                <td>KES <?= number_format($row['cost'], 2); ?></td> <!-- [72] monetarized cost. -->
                                <td><?= htmlspecialchars($row['booking_time']); ?></td> <!-- [73] registration time. -->
                            </tr>                                        <!-- [74] end row. -->
                        <?php endforeach; ?>                             <!-- [75] end loop. -->
                    </tbody>                                             <!-- [76] end data body. -->
                </table>                                                 <!-- [77] end table. -->
            </div>                                                       <!-- [78] end container. -->
        <?php else: ?>                                                   <!-- [79] empty handle. -->
            <p class="no-bookings">You have no booking history.</p>       <!-- [80] message. -->
        <?php endif; ?>                                                   <!-- [81] end check. -->
        <div style="text-align: center; margin-top: 40px;">              <!-- [82] btn row. -->
            <a href="book.php" class="back-button">Back to Bookings</a>     <!-- [83] navigator. -->
        </div>                                                           <!-- [84] end row. -->
    </div>                                                               <!-- [85] end wrapper. -->
    <script src="js/footer.js"></script>                                 <!-- [86] inject footer. -->
</body>                                                              <!-- [87] end body. -->
</html>                                                              <!-- [88] end document. -->
