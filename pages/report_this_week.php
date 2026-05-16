<?php
// [1] <?php (opening tag) tells the machine to activate the PHP engine to process server-side logic.
/**
 * BUSINESS INTELLIGENCE: WEEKLY PERFORMANCE (report_this_week.php)
 * [2] /** (star star) opens a professional multi-line documentation block for explaining script intent and architecture.
 */
// [3] */ (star slash) closes the professional documentation block.

require_once 'db_connection.php';
// [4] require_once is a mandatory directive that imports the 'db_connection.php' file; it ensures the database bridge is established only once to prevent redundant connection errors. 'db_connection.php' is the file containing the MySQL login credentials. ; (semicolon) terminates the instruction.

session_start();
// [5] session_start is a built-in PHP function that initializes the global session environment, allowing the server to retrieve user data like roles and names from persistent memory. ( ) executes the tool. ; (semicolon) stops the line.

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
// [6] if (i f) starts a conditional logical check. ( (bracket) contains the comparison. ! (exclamation) means NOT. isset checks if a specific memory key exists. $_SESSION is the global memory container. [ 'role' ] is the specific box we are looking for. || (double pipe) means OR. !== means NOT IDENTICAL. 'ADMIN' is the text value to compare against. ) (bracket) ends the check. { (curly bracket) starts the logic to run if access is unauthorized.

    header("Location: login.html");
    // [7] header is a function that sends a raw HTTP redirection command to the browser. "Location: login.html" (quote) tells the browser to immediately navigate to the login page for security. ; (semicolon) ends the command.

    exit();
    // [8] exit is a terminal command that immediately kills the script's execution to prevent any further data from being sent to an unauthorized user. ( ) executes it. ; (semicolon) ends it.
}
// [9] } (curly bracket) closes the security check logic block.

// --- SQL QUERY (Procedural Style) ---
// [10] // (double slash) starts a single-line comment for human developers to read.

$sql = "SELECT b.booking_id, b.booking_time, CONCAT(u.first_name, ' ', u.last_name) AS passenger_name, r.from_location, r.to_location, b.seat_number, b.booking_status, r.cost 
        FROM bookings b 
        JOIN users u ON b.user_id = u.user_id 
        JOIN routes r ON b.route_id = r.route_id 
        WHERE YEARWEEK(b.booking_time, 1) = YEARWEEK(CURDATE(), 1) 
        ORDER BY b.booking_time DESC";
// [11] $ (dollar) indicates a variable container. sql is the name of the memory box. = (equals) is the assignment operator. "SELECT..." (quote) is a complex SQL language string that tells the database to join multiple tables (bookings, users, routes) to create a detailed report for the current week. ; (semicolon) terminates the string assignment.

$result = mysqli_query($conn, $sql);
// [12] $result is a variable that serves as a handle or container for the outcome of the database query. = (equals) assigns the value. mysqli_query is the built-in function that transmits the command to the MySQL server. $conn is the database bridge variable. $sql is the command string. ; (semicolon) stops the line.

$total_revenue = 0;
// [13] $total_revenue is a numeric variable used to calculate the sum of all ticket sales. = (assigns). 0 (zero) is the starting point for the calculation. ; (semicolon) terminates the line.
?>
<!-- [14] [?] (closing tag) tells the machine to stop the PHP processor and start rendering the web page's visual HTML structure. -->

<!DOCTYPE html>
<!-- [15] <!DOCTYPE html> is a declaration that informs the web browser to use the latest HTML5 standards for rendering the page layout. -->

<html lang="en">
<!-- [16] <html (h t m l) is the root tag of the entire web document. lang="en" (l a n g equals quote e n quote) sets the document language to English for accessibility. > (greater than). -->

<head>
<!-- [17] <head (h e a d) > (greater than) starts the configuration section where we define hidden metadata, page titles, and links to external design files. -->

    <meta charset="UTF-8">
    <!-- [18] <meta (m e t a) charset="UTF-8" > ensures the browser can display all symbols, emojis, and special characters correctly by using a universal text encoding. -->

    <title>Weekly Performance Analysis - Wema Travellers</title>
    <!-- [19] <title (t i t l e) > (greater than) defines the text that appears on the browser tab or in search engine results. </title> (slash t i t l e) ends it. -->

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- [20] <meta> name="viewport" is a configuration that tells mobile browsers how to scale the website to fit small phone screens. -->

    <link rel="stylesheet" href="css/style.css">
    <!-- [21] <link (l i n k) rel="stylesheet" connects the page to an external CSS design file named 'style.css' which contains branding and color rules. -->

    <link rel="stylesheet" href="css/main.css">
    <!-- [22] <link> connects to 'main.css' for global layout and UI component styling. -->

    <style>
    /* [23] <style (s t y l e) > (greater than) starts a block of internal CSS design code specifically for this report. */

        .report-container { width: 95%; max-width: 1200px; margin: 40px auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.04); }
        /* [24] . (dot) is a class selector. report-container is the name. width and max-width set the size. margin centers the box. padding creates space inside. box-shadow adds a soft shadow for a premium look. */

        .data-table { width: 100%; border-collapse: collapse; margin-top: 35px; }
        /* [25] .data-table design for the sales grid. border-collapse merges lines into a clean table structure. */

        .data-table th, .data-table td { border-bottom: 1px solid #f1f5f9; padding: 18px; text-align: left; }
        /* [26] th (table header) and td (table data) design. border-bottom adds subtle lines between rows. padding creates space for readability. */

        .data-table th { background-color: var(--purple); color: #ffffff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; }
        /* [27] th header design. background-color uses the global purple variable. text-transform makes the labels all capital letters. */

        .data-table tr:nth-child(even) { background-color: #fbfcfe; }
        /* [28] nth-child(even) is a rule that applies a light blue-grey background to every second row to create a "zebra stripe" effect for easier reading. */

        @media print { .no-print { display: none; } }
        /* [29] @media print is a special rule that runs only when the user tries to print the page. .no-print { display: none; } hides buttons like "Print" or "Back" on the physical paper. */

    </style>
    <!-- [30] </style> (slash s t y l e) ends the internal design block. -->

</head>
<!-- [31] </head> (slash h e a d) ends the configuration section. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">
<!-- [32] <body (b o d y) > (greater than) starts the visible part of the website. class="[echo] ... [?]" [echo] injects the user's role into the design for specific styling. -->

    <script src="js/header2.js"></script>
    <!-- [33] <script (s c r i p t) src="js/header2.js" > (greater than) executes a JavaScript file that builds the navigation header at the top of the page. -->

    <div style="height: 100px;"></div>
    <!-- [34] <div (d i v) > (greater than) is a spacer box. style="height: 100px;" (quote) creates a blank space to prevent the header from covering the content. -->

    <div class="report-container">
    <!-- [35] <div class="report-container"> is the white card box that contains the entire analytical report. -->

        <div class="no-print" style="margin-bottom: 30px;">
            <a href="admin_insights.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; display: inline-block; width: auto; padding: 12px 35px; font-weight: 700;">← Back to Insights Hub</a>
            <!-- (less than sign) ! (exclamation mark) - (dash) - (dash) starts an HTML comment explaining the print logic.
                 window (w i n d o w) is the browser object.
                 . (dot) access operator.
                 print (p r i n t) is the command to open the print dialog.
                 ( (bracket) ) (bracket) executes the command.
                 - (dash) - (dash) (greater than sign) ends the logic explanation. -->
            <button onclick="window.print()" class="button regular-button" style="text-decoration:none; background-color: #64748b; color: white; border-radius: 50px; border: none; padding: 12px 35px; margin-left: 10px; font-weight: 700; cursor: pointer;">Print Report</button>
        </div>
        <!-- [39] </div> ends the button container. -->

        <h2 style="color: var(--purple); margin-bottom: 5px;">📅 Weekly Sales Audit</h2>
        <!-- [40] <h2 (h 2) > is a large heading. style sets the color. 📅 is an emoji icon for visual appeal. -->

        <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 10px;">Aggregating every passenger transaction completed within the current calendar week.</p>
        <!-- [41] <p (p a r a g r a p h) > is a subtitle explaining the data source. -->

        <table class="data-table">
        <!-- [42] <table (t a b l e) > (greater than) starts the data grid for displaying the list of sales. -->

            <thead><tr><th>Ref ID</th><th>Stamp</th><th>Full Name</th><th>Journey Segment</th><th>Seat Map</th><th>Price</th><th>Status Record</th></tr></thead>
            <!-- [43] <thead> (header) contains the top row labels for each column. <th> (table header) defines individual title cells. -->

            <tbody>
            <!-- [44] <tbody> (body) starts the section where the actual database records will be listed row by row. -->

                <?php if (mysqli_num_rows($result) > 0): ?>
                <!-- [45] [php] (start PHP). if starts a check. mysqli_num_rows is a function that counts how many sales were found in the handle $result. > 0 checks if the list is not empty. : (colon) starts the display block. [?] (stop PHP). -->

                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <!-- [46] while (repeat loop). $row (dollar r o w) is a variable holding the data for one single transaction. mysqli_fetch_assoc is a function that retrieves one row from the database handle $result. : (colon) starts the loop block. -->

                        <tr>
                        <!-- [47] <tr (t a b l e r o w) > (greater than) starts a new row of data for a passenger booking. -->

                            <td><strong style="color: #94a3b8;"><?= $row['booking_id'] ?></strong></td>
                            <!-- [48] <td> (cell) prints the Ref ID. [echo] [echo] prints the booking_id data. <strong> makes the text bold. -->

                            <td style="font-family: 'Courier New', monospace; font-size: 0.85rem; color: #475569;"><?= $row['booking_time'] ?></td>
                            <!-- [49] <td> cell for the transaction timestamp. font-family: 'Courier New' makes it look like a digital clock output. -->

                            <td style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($row['passenger_name']) ?></td>
                            <!-- [50] <td> cell for the Passenger Name. htmlspecialchars (h t m l s p e c i a l c h a r s) is a security function that ensures names are displayed safely. -->

                            <td style="color: #64748b;"><?= htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) ?></td>
                            <!-- [51] <td> cell for the Journey details. . (dot) is a connector that joins the "from" and "to" location texts together. -->

                            <td><span style="background: #f8fafc; color: #475569; padding: 5px 10px; border-radius: 6px; font-weight: 800; border: 1px solid #e2e8f0;"><?= $row['seat_number'] ?></span></td>
                            <!-- [52] <td> cell for the Seat Number. <span> is used to add a grey box style around the number. -->

                            <td style="font-weight: 700; color: #0f172a;">$<?= number_format($row['cost'], 2) ?></td>
                            <!-- [53] <td> cell for the ticket Price. number_format (n u m b e r underscore f o r m a t) is a function that ensures the price shows exactly 2 decimal places (e.g. $50.00). -->

                            <td style="color: <?= ($row['booking_status'] == 'CANCELLED') ? '#ef4444' : '#10b981' ?>; font-weight: 900; text-transform: uppercase; font-size: 0.85rem;"><?= $row['booking_status'] ?></td>
                            <!-- [54] <td> cell for the booking Status. ? (question) and : (colon) is a ternary choice that makes CANCELLED red and confirmed tickets green. -->

                        </tr>
                        <!-- [55] </tr (slash t r) > (greater than) ends the current data row. -->

                        <?php if($row['booking_status'] !== 'CANCELLED') { $total_revenue += $row['cost']; } ?>
                        <!-- [56] [php] (start PHP). if starts a check to see if the status is NOT (!) CANCELLED. $total_revenue is our sum variable. += (plus equals) adds the cost of this ticket to the total. ; ends the instruction. [?] (stop PHP). -->

                    <?php endwhile; ?>
                    <!-- [57] [php] endwhile; [?] (e n d w h i l e) tells the machine the transaction list loop is finished. -->

                    <tr style="background-color: #f8fafc; font-size: 1.1rem; border-top: 2px solid var(--purple);">
                    <!-- [58] <tr> starts the final summary row for the total revenue. -->

                        <td colspan="5" style="text-align: right; font-weight: 800; color: var(--purple);">TOTAL REVENUE:</td>
                        <!-- [59] <td> colspan="5" (quote) merges 5 cells together for the label. style aligns text to the right. -->

                        <td colspan="2" style="font-weight: 900; color: #10b981; font-size: 1.3rem;">$<?= number_format($total_revenue, 2) ?></td>
                        <!-- [60] <td> prints the final calculated revenue sum in large green bold text. -->

                    </tr>
                    <!-- [61] </tr> ends the summary row. -->

                <?php else: ?>
                <!-- [62] [php] else: [?] (e l s e) runs if the database search returned zero records for the week. -->

                    <tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 100px;"><div style="font-size: 1.25rem; font-style: italic;">No booking records detected for the current weekly cycle.</div></td></tr>
                    <!-- [63] <tr> and <td> creates a large centered message notifying the admin that there is no data to show. -->

                <?php endif; ?>
                <!-- [64] [php] endif; [?] (e n d i f) ends the results check logic. -->

            </tbody>
            <!-- [65] </tbody> (slash t b o d y) ends the data section. -->

        </table>
        <!-- [66] </table> (slash t a b l e) ends the data grid. -->

    </div>
    <!-- [67] </div> (slash d i v) closes the white report card box. -->

    <div style="height: 120px;"></div>
    <!-- [68] <div> bottom spacer box for visual breathing room. -->

    <script src="js/footer.js"></script>
    <!-- [69] <script> pulls in the footer logic to display the site copyright and credits. -->

</body>
<!-- [70] </body> (slash b o d y) ends the visible part of the page. -->

</html>
<!-- [71] </html> (slash h t m l) ends the web document structure. -->

<?php mysqli_close($conn); ?>
