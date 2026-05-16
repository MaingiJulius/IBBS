<?php
// [1] <?php (opening tag) starts the PHP engine.
// [1.1] < (less than) ? (question mark) php (p h p) tells the server to begin interpreting the server-side code.

/**
 * =================================================================
 * ARCHIVE REPORT: PREVIOUS MONTH SALES (report_last_month.php)
 * [2] /** (star star) opens a multi-line documentation block for architectural metadata.
 * =================================================================
 */
// [3] */ (star slash) closes the documentation block.

require_once 'db_connection.php';
// [4] require_once imports the database bridge. ; (semicolon).
// [4.1] require_once is a mandatory directive that pulls in the 'db_connection.php' file; it ensures the connection variable $conn is available. ; (semicolon) terminates the instruction.

session_start();
// [5] session_start activates the user memory system. ; (semicolon).
// [5.1] session_start is a function that initializes the global $_SESSION array to track who is logged in. ( ) executes the function. ; (semicolon) ends the line.

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
// [6] if (i f) starts the security check. ( contains logic. ! (not). isset checks if role exists. || means OR. !== means NOT IDENTICAL. 'ADMIN' is the target text. ) ends check. { starts block.
// [6.1] if (i f) starts a conditional logical check. ( (bracket) contains the comparison. ! (exclamation) means NOT. isset verifies if the key exists. || (double pipe) means OR. !== means NOT EQUAL to. 'ADMIN' (quote) is the required role string. ) (bracket) ends check. { (curly bracket) starts the logic to run if the check fails.

    header("Location: login.html");
    // [7] header redirects the user. ; (semicolon).
    // [7.1] header is a function that sends a raw redirection instruction to the browser. "Location: login.html" (quote) is the target address. ; (semicolon) ends the instruction.

    exit();
    // [8] exit stops the machine. ; (semicolon).
    // [8.1] exit is a command that immediately kills the script execution for security. ; (semicolon) ends it.
}
// [9] } ends the security block.

$sql = "SELECT b.booking_id, b.booking_time, CONCAT(u.first_name, ' ', u.last_name) AS passenger_name, r.from_location, r.to_location, b.seat_number, b.booking_status, r.cost 
        FROM bookings b 
        JOIN users u ON b.user_id = u.user_id 
        JOIN routes r ON b.route_id = r.route_id 
        WHERE PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM CURDATE()), EXTRACT(YEAR_MONTH FROM b.booking_time)) = 1 
        ORDER BY b.booking_time DESC";
// [10] $sql (variable). = (assign). "SELECT..." (SQL command). ; (semicolon).
// [10.1] $ (dollar) variable indicator. sql is the name. = (equals) is the assignment symbol. "SELECT..." (quote) is a complex SQL query using PERIOD_DIFF() and EXTRACT() to find the gap between months. ; (semicolon) terminates the line.

$result = mysqli_query($conn, $sql);
// [11] $result (variable). = (assign). mysqli_query runs the command. ; (semicolon).
// [11.1] $result (dollar r e s u l t) is a handle for the database output. = (assigns) the value. mysqli_query is the function that talks to MySQL. $conn is the bridge. $sql is the command. ; (semicolon) stops the line.

$total_revenue = 0;
// [12] $total_revenue (variable). = (assign). 0 (number). ; (semicolon).
// [12.1] $total_revenue is a numeric container for summing up the costs. = (assigns). 0 (zero) is the start value. ; (semicolon).
?>
<!-- [13] [?] stops PHP and starts HTML. -->

<!DOCTYPE html>
<!-- [14] <!DOCTYPE html> defines a modern HTML5 web document. -->

<html lang="en">
<!-- [15] <html lang="en"> starts the English web page. -->

<head>
<!-- [16] <head> starts the metadata section. -->

    <meta charset="UTF-8">
    <!-- [17] <meta charset="UTF-8"> ensures characters display correctly. -->

    <title>Retrospective Audit: Previous Month - Wema Travellers</title>
    <!-- [18] <title> sets the browser tab title. -->

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- [19] <meta> name="viewport" makes the page display well on mobile phones. -->

    <link rel="stylesheet" href="css/style.css">
    <!-- [20] <link> connects to the 'style.css' design file. -->

    <link rel="stylesheet" href="css/main.css">
    <!-- [21] <link> connects to the 'main.css' layout file. -->

    <style>
    /* [22] <style> starts internal CSS design rules. */

        .report-container { width: 95%; max-width: 1200px; margin: 40px auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.04); }
        /* [23] .report-container card style. centers the box and adds a premium shadow. */

        .data-table { width: 100%; border-collapse: collapse; margin-top: 35px; }
        /* [24] .data-table grid style for clean columns. */

        .data-table th, .data-table td { border-bottom: 1px solid #f1f5f9; padding: 18px; text-align: left; }
        /* [25] th and td cell style. adds lines between rows and spacing. */

        .data-table th { background-color: var(--purple); color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; }
        /* [26] th header style. uses the purple brand color and makes text all caps. */

        .data-table tr:nth-child(even) { background-color: #fbfcfe; }
        /* [27] tr:nth-child(even) adds zebra striping to the report rows. */

        @media print { .no-print { display: none; } }
        /* [28] @media print hides buttons when printing to paper. */

    </style>
    <!-- [29] </style> ends internal CSS. -->

</head>
<!-- [30] </head> ends metadata. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">
<!-- [31] <body> starts the visible page. class="[echo] ... [?]" [echo] adds the user role to the page class. -->

    <script src="js/header2.js"></script>
    <!-- [32] <script> pulls in the navigation header logic. -->

    <div style="height: 100px;"></div>
    <!-- [33] <div> is a spacer to prevent the header from covering content. -->

    <div class="report-container">
    <!-- [34] <div class="report-container"> is the white card containing the report. -->

        <div class="no-print" style="margin-bottom: 30px;">
        <!-- [35] <div class="no-print"> contains buttons that won't show on paper. -->

            <a href="admin_insights.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; display: inline-block; width: auto; padding: 12px 35px; font-weight: 700;">← Back to Insights Hub</a>
            <!-- [36] <a> link button to return to the admin dashboard. -->

            <button onclick="window.print()" class="button regular-button" style="text-decoration:none; background-color: #64748b; color: white; border-radius: 50px; border: none; padding: 12px 35px; margin-left: 10px; font-weight: 700; cursor: pointer;">Print Report</button>
            <!-- [37] <button> that triggers the browser print tool via JavaScript. -->

        </div>
        <!-- [38] </div> ends the button box. -->

        <h2 style="color: var(--purple); margin-bottom: 5px;">📉 Historical Performance Review</h2>
        <!-- [39] <h2> heading with an emoji icon 📉. -->

        <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 10px;">Analyzing every passenger transaction recorded during the previous complete calendar cycle.</p>
        <!-- [40] <p> paragraph providing report context. -->

        <table class="data-table">
        <!-- [41] <table> starts the audit data grid. -->

            <thead><tr><th>Ref ID</th><th>Stamp</th><th>Full Name</th><th>Segment</th><th>Seat Map</th><th>Price</th><th>Status Record</th></tr></thead>
            <!-- [42] <thead> table head with column labels. -->

            <tbody>
            <!-- [43] <tbody> starts the dynamic data section. -->

                <?php if (mysqli_num_rows($result) > 0): ?>
                <!-- [44] [php] if check. mysqli_num_rows (m y s q l i underscore n u m underscore r o w s) counts results. > 0 checks if data exists. : starts block. [?] -->

                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <!-- [45] while (loop). $row variable holds one record. mysqli_fetch_assoc retrieves the next row as an array. : starts block. [?] -->

                        <tr>
                        <!-- [46] <tr> starts a new table row. -->

                            <td><strong style="color: #94a3b8;"><?= $row['booking_id'] ?></strong></td>
                            <!-- [47] <td> cell for ID. [echo] [echo] prints the booking_id value. -->

                            <td style="font-family: 'Courier New', monospace; font-size: 0.85rem; color: #475569;"><?= $row['booking_time'] ?></td>
                            <!-- [48] <td> cell for time. Courier New font makes it look like a timestamp. -->

                            <td style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($row['passenger_name']) ?></td>
                            <!-- [49] <td> cell for Name. htmlspecialchars ensures text is shown safely. -->

                            <td style="color: #64748b;"><?= htmlspecialchars($row['from_location'] . ' → ' . $row['to_location']) ?></td>
                            <!-- [50] <td> cell for journey. . (dot) connects strings. -->

                            <td><span style="background: #f8fafc; color: #475569; padding: 5px 10px; border-radius: 6px; font-weight: 800; border: 1px solid #e2e8f0;"><?= $row['seat_number'] ?></span></td>
                            <!-- [51] <td> cell for seat number inside a styled <span> box. -->

                            <td style="font-weight: 700; color: #0f172a;">$<?= number_format($row['cost'], 2) ?></td>
                            <!-- [52] <td> cell for price. number_format ensures 2 decimals. -->

                            <td style="color: <?= ($row['booking_status'] == 'CANCELLED') ? '#ef4444' : '#10b981' ?>; font-weight: 900; text-transform: uppercase; font-size: 0.85rem;"><?= $row['booking_status'] ?></td>
                            <!-- [53] <td> cell for status. ? (ternary) choice makes CANCELLED red (#ef4444) and others green (#10b981). -->

                        </tr>
                        <!-- [54] </tr> ends the row. -->

                        <?php if($row['booking_status'] !== 'CANCELLED') { $total_revenue += $row['cost']; } ?>
                        <!-- [55] [php] if check. if status is NOT (!) CANCELLED. $total_revenue is the sum variable. += adds the cost. ; ends. [?] -->

                    <?php endwhile; ?>
                    <!-- [56] [php] endwhile; [?] ends the loop. -->

                    <tr style="background-color: #f8fafc; font-size: 1.1rem; border-top: 2px solid var(--purple);">
                    <!-- [57] <tr> starts the summary row for total revenue. -->

                        <td colspan="5" style="text-align: right; font-weight: 800; color: var(--purple);">TOTAL REVENUE:</td>
                        <!-- [58] <td> label for total. colspan="5" merges 5 cells. -->

                        <td colspan="2" style="font-weight: 900; color: #10b981; font-size: 1.3rem;">$<?= number_format($total_revenue, 2) ?></td>
                        <!-- [59] <td> final sum. prints the calculated total in green bold text. -->

                    </tr>
                    <!-- [60] </tr> ends summary. -->

                <?php else: ?>
                <!-- [61] [php] else: [?] runs if zero records found. -->

                    <tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 100px;"><div style="font-size: 1.25rem; font-style: italic;">No historical records found for the previous month instance.</div></td></tr>
                    <!-- [62] <tr> centered empty state message. -->

                <?php endif; ?>
                <!-- [63] [php] endif; [?] ends the logic. -->

            </tbody>
            <!-- [64] </tbody> ends data section. -->

        </table>
        <!-- [65] </table> ends grid. -->

    </div>
    <!-- [66] </div> ends card. -->

    <div style="height: 120px;"></div>
    <!-- [67] <div> spacer box. -->

    <script src="js/footer.js"></script>
    <!-- [68] <script> footer logic. -->

</body>
<!-- [69] </body> ends visible page. -->

</html>
<!-- [70] </html> ends document structure. -->

<?php mysqli_close($conn); ?>
<!-- [71] [php] starts PHP. mysqli_close (m y s q l i underscore c l o s e) shuts down the database bridge $conn to save resources. ; terminates. [?] ends PHP. -->
re. -->nt. -->
