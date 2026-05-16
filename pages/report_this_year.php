<?php
// [1] <?php (opening tag) tells the machine to activate the PHP interpreter for server-side logic.
// [1.1] < (less than) ? (question mark) php (p h p) is the opening signal that all following code is in the PHP language.

/**
 * =================================================================
 * BUSINESS INTELLIGENCE: ANNUAL BOOKING LEDGER (report_this_year.php)
 * [2] /** (star star) opens a professional multi-line documentation block for explaining script intent.
 * =================================================================
 */
// [3] */ (star slash) closes the documentation block.

require_once 'db_connection.php';
// [4] require_once imports the database bridge. ; (semicolon).
// [4.1] require_once is a directive that pulls in the 'db_connection.php' file; it ensures the database link is established once to prevent errors. ; (semicolon) terminates the line.

session_start();
// [5] session_start initializes the user memory system. ; (semicolon).
// [5.1] session_start is a built-in function that tells the server to retrieve the user's persistent data (like roles). ( ) executes it. ; (semicolon) stops the line.

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
// [6] if (i f) starts the security check. ( contains logic. ! (not). isset checks if the key exists. || (or). !== (not identical). 'ADMIN' (text). ) ends check. { starts block.
// [6.1] if (i f) starts a conditional choice. ( (bracket) contains the condition. ! (exclamation) means NOT. isset verifies if the memory key exists. $_SESSION is the memory array. || (double pipe) means OR. !== means NOT EQUAL to. 'ADMIN' (quote) is the required role. ) (bracket) ends check. { (curly bracket) starts the logic to run if the user is not an admin.

    header("Location: login.html");
    // [7] header redirects the user. ; (semicolon).
    // [7.1] header is a function that sends a raw HTTP command to the browser to navigate to a new page. "Location: login.html" (quote) is the target. ; (semicolon) ends the command.

    exit();
    // [8] exit stops the machine. ; (semicolon).
    // [8.1] exit is a terminal command that immediately stops the script execution to prevent unauthorized access. ( ) executes it. ; (semicolon) ends it.
}
// [9] } ends the security block.

$sql = "SELECT b.booking_id, b.booking_time, CONCAT(u.first_name, ' ', u.last_name) AS passenger_name, r.from_location, r.to_location, b.seat_number, b.booking_status, r.cost 
        FROM bookings b 
        JOIN users u ON b.user_id = u.user_id 
        JOIN routes r ON b.route_id = r.route_id 
        WHERE YEAR(b.booking_time) = YEAR(CURDATE()) 
        ORDER BY b.booking_time DESC";
// [10] $sql (variable). = (assign). "SELECT..." (SQL command). ; (semicolon).
// [10.1] $ (dollar) variable indicator. sql is the name of the memory box. = (equals) is the assignment operator. "SELECT..." (quote) is a complex SQL command that filters for the current year using the YEAR() database tool. ; (semicolon) terminates the assignment.

$result = mysqli_query($conn, $sql);
// [11] $result (variable). = (assign). mysqli_query runs the command. ; (semicolon).
// [11.1] $result (dollar r e s u l t) is a variable handle that holds the outcome of the query. = (assigns) the value. mysqli_query is the function that talks to the database. $conn is the bridge variable. $sql is the command string. ; (semicolon) stops the line.

$total_revenue = 0;
// [12] $total_revenue (variable). = (assign). 0 (number). ; (semicolon).
// [12.1] $total_revenue is a numeric variable for calculating the sum. = (assigns). 0 (zero) is the start value. ; (semicolon).
?>
<!-- [13] [?] stops PHP and starts HTML. -->

<!DOCTYPE html>
<!-- [14] <!DOCTYPE html> defines a modern HTML5 web document for the browser. -->

<html lang="en">
<!-- [15] <html lang="en"> starts the English web page structure. -->

<head>
<!-- [16] <head> starts the metadata section. -->

    <meta charset="UTF-8">
    <!-- [17] <meta charset="UTF-8"> ensures characters display correctly. -->

    <title>Annual Intelligence Report: Total Year Sales - Wema Travellers</title>
    <!-- [18] <title> sets the browser tab label. -->

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- [19] <meta> name="viewport" ensures the report scales correctly on mobile phones. -->

    <link rel="stylesheet" href="css/style.css">
    <!-- [20] <link> connects to the 'style.css' branding file. -->

    <link rel="stylesheet" href="css/main.css">
    <!-- [21] <link> connects to the 'main.css' layout file. -->

    <style>
    /* [22] <style> starts internal CSS design rules. */

        .report-container { width: 95%; max-width: 1200px; margin: 40px auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.04); }
        /* [23] .report-container design. centers the box and adds a premium shadow effect. */

        .data-table { width: 100%; border-collapse: collapse; margin-top: 35px; }
        /* [24] .data-table grid design for clean row display. */

        .data-table th, .data-table td { border-bottom: 1px solid #f1f5f9; padding: 18px; text-align: left; }
        /* [25] th and td cell design. adds lines between rows and creates space. */

        .data-table th { background-color: var(--purple); color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; }
        /* [26] th header style. uses the purple theme variable and makes text all caps. */

        .data-table tr:nth-child(even) { background-color: #fbfcfe; }
        /* [27] tr:nth-child(even) adds zebra striping for easier reading of data. */

        .back-link { display: inline-block; text-decoration: none; color: white; background-color: var(--purple); padding: 12px 35px; border-radius: 50px; font-weight: 700; transition: opacity 0.2s; }
        /* [28] .back-link design for the return button. transition makes it smooth on hover. */

        .back-link:hover { opacity: 0.9; }
        /* [29] .back-link:hover makes the button slightly fade when touched. */

        @media print { .no-print { display: none; } }
        /* [30] @media print hides buttons when the report is sent to the printer. */

    </style>
    <!-- [31] </style> ends internal CSS. -->

</head>
<!-- [32] </head> ends metadata. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">
<!-- [33] <body> starts the visible page. class="[echo] ... [?]" [echo] adds the user role to the page class. -->

    <script src="js/header2.js"></script>
    <!-- [34] <script> pulls in the navigation header logic. -->

    <div style="height: 100px;"></div>
    <!-- [35] <div> is a spacer box to push the content down. -->

    <div class="report-container">
    <!-- [36] <div class="report-container"> is the white card holding the report content. -->

        <div class="no-print" style="margin-bottom: 30px;">
            <a href="admin_insights.php" class="back-link">← Back to Insights Hub</a>
            <!-- (less than sign) ! (exclamation mark) - (dash) - (dash) starts an HTML comment explaining the print logic.
                 window (w i n d o w) is the browser object.
                 . (dot) access operator.
                 print (p r i n t) is the command to open the print dialog.
                 ( (bracket) ) (bracket) executes the command.
                 - (dash) - (dash) (greater than sign) ends the logic explanation. -->
            <button onclick="window.print()" class="button regular-button" style="text-decoration:none; background-color: #64748b; color: white; border-radius: 50px; border: none; padding: 12px 35px; margin-left: 10px; font-weight: 700; cursor: pointer;">Print Report</button>
        </div>
        <!-- [40] </div> ends the button box. -->

        <h2 style="color: var(--purple); margin-bottom: 5px;">📅 Annual Performance Audit</h2>
        <!-- [41] <h2> heading with an emoji icon 📅. -->

        <p style="color: #64748b; margin-bottom: 10px; font-size: 1.05rem;">Aggregated ticketing activity recorded since Jan 1st of the current calendar year.</p>
        <!-- [42] <p> paragraph providing report description. -->

        <table class="data-table">
        <!-- [43] <table> starts the sales data grid. -->

            <thead><tr><th>Ref ID</th><th>Stamp</th><th>Full Name</th><th>Travel Segment</th><th>Seat Map</th><th>Price</th><th>Log Status</th></tr></thead>
            <!-- [44] <thead> table head containing column titles. -->

            <tbody>
            <!-- [45] <tbody> starts the dynamic data section. -->

                <?php if (mysqli_num_rows($result) > 0): ?>
                <!-- [46] [php] if check. mysqli_num_rows (m y s q l i underscore n u m underscore r o w s) counts results. > 0 checks if not empty. : starts block. [?] -->

                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <!-- [47] while (loop). $row variable holds one record. mysqli_fetch_assoc retrieves the next row. : starts block. [?] -->

                        <tr>
                        <!-- [48] <tr> starts a new table row. -->

                            <td><strong style="color: #94a3b8;"><?= $row['booking_id'] ?></strong></td>
                            <!-- [49] <td> cell for ID. [echo] [echo] prints the booking_id. -->

                            <td style="font-family: 'Courier New', monospace; font-size: 0.85rem; color: #475569;"><?= $row['booking_time'] ?></td>
                            <!-- [50] <td> cell for time. Courier New font makes it look like a system timestamp. -->

                            <td style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($row['passenger_name']) ?></td>
                            <!-- [51] <td> cell for Name. htmlspecialchars ensures names are shown safely. -->

                            <td style="color: #64748b;"><?= htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) ?></td>
                            <!-- [52] <td> cell for journey details. . (dot) is the string connector. -->

                            <td><span style="background: #f8fafc; color: #475569; padding: 5px 10px; border-radius: 6px; font-weight: 800; border: 1px solid #e2e8f0;"><?= $row['seat_number'] ?></span></td>
                            <!-- [53] <td> cell for seat number inside a styled <span> box. -->

                            <td style="font-weight: 700; color: #0f172a;">$<?= number_format($row['cost'], 2) ?></td>
                            <!-- [54] <td> cell for price. number_format ensures 2 decimals. -->

                            <td style="color: <?= ($row['booking_status'] == 'CANCELLED') ? '#ef4444' : '#10b981' ?>; font-weight: 900; text-transform: uppercase; font-size: 0.85rem;"><?= $row['booking_status'] ?></td>
                            <!-- [55] <td> cell for status. ? (ternary) choice makes CANCELLED red (#ef4444) and others green (#10b981). -->

                        </tr>
                        <!-- [56] </tr> ends the row. -->

                        <?php if($row['booking_status'] !== 'CANCELLED') { $total_revenue += $row['cost']; } ?>
                        <!-- [57] [php] if check. if status is NOT (!) CANCELLED. $total_revenue is the sum variable. += adds the cost. ; ends. [?] -->

                    <?php endwhile; ?>
                    <!-- [58] [php] endwhile; [?] ends the loop. -->

                    <tr style="background-color: #f8fafc; font-size: 1.1rem; border-top: 2px solid var(--purple);">
                    <!-- [59] <tr> starts the summary row for total revenue. -->

                        <td colspan="5" style="text-align: right; font-weight: 800; color: var(--purple);">TOTAL REVENUE:</td>
                        <!-- [60] <td> label for total. colspan="5" merges 5 cells. -->

                        <td colspan="2" style="font-weight: 900; color: #10b981; font-size: 1.3rem;">$<?= number_format($total_revenue, 2) ?></td>
                        <!-- [61] <td> final sum. prints the calculated total in green bold text. -->

                    </tr>
                    <!-- [62] </tr> ends summary. -->

                <?php else: ?>
                <!-- [63] [php] else: [?] runs if zero records found. -->

                    <tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 100px;"><div style="font-size: 3rem; margin-bottom: 20px;">📉</div><div style="font-size: 1.25rem; font-style: italic;">No booking records detected for the current calendar year.</div></td></tr>
                    <!-- [64] <tr> centered empty state message with a chart emoji. -->

                <?php endif; ?>
                <!-- [65] [php] endif; [?] ends the logic. -->

            </tbody>
            <!-- [66] <tbody> ends data section. -->

        </table>
        <!-- [67] </table> ends data grid. -->

    </div>
    <!-- [68] </div> ends report card. -->

    <div style="height: 120px;"></div>
    <!-- [69] <div> spacer box. -->

    <script src="js/footer.js"></script>
    <!-- [70] <script> footer logic. -->

</body>
<!-- [71] </body> ends visible content. -->

</html>
<!-- [72] </html> ends document structure. -->

<?php mysqli_close($conn); ?>
<!-- [73] [php] starts PHP. mysqli_close (m y s q l i underscore c l o s e) shuts down the database bridge $conn to save resources. ; terminates. [?] ends PHP. -->
