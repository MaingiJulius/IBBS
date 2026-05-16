<?php
// <?php (opening tag) tells the server to start interpreting the code as PHP.

/**
 * USER BOOKING HISTORY PAGE (view_user_history.php)
 */

// require_once (require once) includes the database connection.
require_once 'db_connection.php';
// session_start (session start) starts the user session.
session_start();

// --- SECURITY CHECK ---
// if (if) check for login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// $ (dollar sign) variable marker.
// user_id (user i d) label.
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'passenger') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <div class="view-container">
        <h2 style="color:var(--purple); text-align:center;">My Travel History</h2>
        <p style="text-align:center;">Hello, <?= htmlspecialchars($_SESSION['name']) ?>! Here is a list of your trip records.</p>

        <table class="crud-table">
            <thead>
                <tr>
                    <th>Date Booked</th>
                    <th>Route & Departure</th>
                    <th>Passenger</th>
                    <th>Bus</th>
                    <th>Seat</th>
                    <th>Cost (KES)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // $ (dollar sign) variable marker.
                // sql (s q l) label.
                $sql = "SELECT b.*, r.from_location, r.to_location, r.cost, r.departure_date, bs.bus_name, u.first_name, u.last_name 
                        FROM bookings b 
                        JOIN routes r ON b.route_id = r.route_id 
                        JOIN buses bs ON b.bus_id = bs.bus_id
                        JOIN users u ON b.user_id = u.user_id
                        WHERE b.user_id = ? 
                        ORDER BY b.booking_time DESC";
                
                $stmt = mysqli_prepare($conn, $sql);
                /* $stmt (handle) = (assignment). 
                   mysqli (MySQL Improved) _ (underscore) prepare (prepare) is the security 
                   function that pre-compiles the command blueprint. 
                   "Improved" (mysqli) is used because it supports secure "Prepared Statements" 
                   using placeholders like ? to prevent SQL Injection. 
                   ( starts. $conn (bridge) , (comma) $sql (blueprint) ) ends. ; (semicolon). */
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                /* mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
                   bind (bind) _ (underscore) param (parameter) is the function that 
                   securely pours the data into the ? placeholders. 
                   The ? (Question Mark) is a safety hole that ensures user data 
                   is never treated as a command. 
                   ( starts. $stmt (tool handle) , (comma) "i" (integer number type) 
                   , (comma) $user_id (current user's identity data) ) ends. ; (semicolon). */
                /* mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
                   bind (bind) _ (underscore) param (parameter) is the security function that 
                   attaches (binds) the data to the query. ( starts. "i" (integer number) 
                   , (comma) $user_id (current user identity data) ) ends. ; (semicolon). */
                mysqli_stmt_execute($stmt);
                /* mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
                   execute (execute) is the command that triggers the database search. 
                   ( starts. $stmt (handle) ) ends. ; (semicolon). */
                $result = mysqli_stmt_get_result($stmt);
                /* $result (list) = (assignment). 
                   mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
                   get (get) _ (underscore) result (result) retrieves the found records. 
                   ( starts. $stmt (handle) ) ends. ; (semicolon). */
                
                // mysqli_num_rows (MySQL Improved number of rows).
                if (mysqli_num_rows($result) > 0) {
                    // mysqli_fetch_assoc (MySQL Improved fetch associative array).
                    while($row = mysqli_fetch_assoc($result)):
                        /* while (while) starts a loop. $row (row container) pulls data. 
                           mysqli_fetch_assoc (fetch associative) converts raw data into labeled pieces. 
                           ( starts. $result (result source). ) ends. : (colon) starts the loop block. */
                ?>
                <tr>
                    <td><?= $row['booking_time'] ?></td>
                    <td><?= htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) ?><br><small>Dep: <?= $row['departure_date'] ?></small></td>
                    <!-- html (HyperText) special (special) chars (characters) is a security tool 
                         that encodes text for safety. ( starts the tool. $row (data row) 
                         ['from_location'] (origin) . (connects) ' to ' (text) . (connects) 
                         $row ['to_location'] (destination) ) ends. -->
                    <td><?= htmlspecialchars($row['passenger_name'] ?: ($row['first_name'] . ' ' . $row['last_name'])) ?></td>
                    <!-- htmlspecialchars (security tool) ( $row ['passenger_name'] (label) ) -->
                    <td><?= htmlspecialchars($row['bus_name']) ?></td>
                    <!-- htmlspecialchars (security tool) ( $row ['bus_name'] (vehicle label) ) -->
                    <td><?= $row['seat_number'] ?></td>
                    <td><?= number_format($row['cost'], 2) ?></td>
                    <td><span style="color:<?= ($row['booking_status']=='CANCELLED'?'red':'green') ?>; font-weight:bold;"><?= $row['booking_status'] ?></span></td>
                    <td>
                        <?php if ($row['booking_status'] == 'PAID' || $row['booking_status'] == 'CONFIRMED'): ?>
                            <a href="user_cancel_ticket.php?booking_id=<?= $row['booking_id'] ?>" 
                               style="color:red;" onclick="return confirm('Cancel this trip?')">Cancel</a>
                        <?php else: ?>
                            --
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile;
                } else {
                      echo "<tr><td colspan='8' style='text-align:center;'>No bookings found.</td></tr>";
                }
                // mysqli_stmt_close (MySQL Improved statement close).
                mysqli_stmt_close($stmt);
                ?>
            </tbody>
        </table>
    </div>

    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>

