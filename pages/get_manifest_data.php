<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    die("Access Denied.");
}
$route_id = isset($_GET['route_id']) ? $_GET['route_id'] : die("Error");
// the unique label chosen to identify the specific trip identification number.
$sql_t = "SELECT r.*, b.bus_name, b.reg_no, d.full_name as driver_name FROM routes r JOIN buses b ON r.bus_id = b.bus_id LEFT JOIN drivers d ON b.driver_id = d.driver_id WHERE r.route_id = ?";
$res_t = mysqli_query($conn, $sql_t);
$trip = mysqli_fetch_assoc($res_t);
$sql_p = "SELECT * FROM bookings WHERE route_id = ? AND booking_status != 'CANCELLED' ORDER BY seat_number ASC";
$passengers=mysqli_query($conn,$sql_p) or die(mysqli_error($conn));
?>
<!DOCTYPE html>
(greater than sign) is the standard declaration that tells the web browser
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Manifest - IBBS</title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        (font) : (colon) sans-serif (clean style) ; (semicolon) padding (internal
        .header { text-align: center; border-bottom: 2px solid black; padding-bottom: 20px; }
        .details { margin: 20px 0; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; border: 1px solid #ccc; padding: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
    </style>
</head>
<body>
    <div class="header"><h1>Wema Travellers Manifest</h1></div>
    <div class="details">
        <div><strong>Path:</strong> <?php echo htmlspecialchars($trip['from_location']); ?> → <?php echo htmlspecialchars($trip['to_location']); ?></div>
        <div><strong>Date:</strong> <?php echo $trip['departure_date']; ?></div>
        <div><strong>Bus:</strong> <?php echo htmlspecialchars($trip['bus_name']); ?></div>
        <div><strong>Driver:</strong> <?php echo htmlspecialchars($trip['driver_name']); ?></div>
    </div>
    <table>
        <thead>
            <tr><th>No.</th><th>Name</th><th>ID Number</th><th>Seat</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php $i=1; while($p = mysqli_fetch_assoc($passengers)): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($p['passenger_name']); ?></td>
                    <td><?php echo htmlspecialchars($p['passenger_id_number']); ?></td>
                    <td><?php echo $p['seat_number']; ?></td>
                    <td><?php echo $p['booking_status']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <script>
        window.onload = function() {
           entire browser window. . (dot) is the connector used to access its
           tools. onload (o n l o a d) is the specific "event" property that
           detects when the page has finished loading all text, images, and
           styles. = (equals sign) is the assignment operator. function (f u n
           c t i o n) defines the set of instructions to follow when the
           event happens. ( ) (bracket) { (opening curly bracket) marks
            window.print();
               instruction that tells the browser to open the system's
               printing dialog box. ( (bracket) ) (bracket) executes
        };
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>