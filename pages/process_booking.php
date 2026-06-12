<?php
ob_start();
try {
    require_once 'db_connection.php';
    session_start();
    header('Content-Type: application/json');
    if (!isset($_SESSION['user_id'])) {
        ob_get_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized Access: Please login.']);
        exit();
    }
// --- [5] INPUT DESERIALIZATION ---
    $raw_input = file_get_contents("php://input");
    $data = json_decode($raw_input, true);
    $route_id = $data['route_id'] ?? null;
    $target_user_id = $data['user_id'] ?? $_SESSION['user_id'];
    $passengersData = $data['passengers'] ?? [];
    /* --- [6] PRE-FLIGHT VALIDATION --- */                             // [26] Marker for ensuring minimal dataset requirements are met.
    if (!$route_id || empty($passengersData)) {
        echo json_encode(['success' => false, 'message' => 'Selection Error: No seats or trip identified.']);
        exit();                                                          // [29] Terminate processing due to insufficient data.
    }
    /* --- [7] TRIP DATA RETRIEVAL --- */                               // [31] Marker for fetching trip context from the database registry.
    $route=mysqli_fetch_assoc(mysqli_query($conn,"SELECT r.*, b.max_passengers FROM routes r JOIN buses b ON r.bus_id=b.bus_id WHERE r.route_id=$route_id"));
    if (!$route) {
        echo json_encode(['success' => false, 'message' => 'System Error: Trip route is invalid or retired.']);
        exit();
    }
// --- [8] ATOMIC TRANSACTION COMMENCEMENT ---
    $conn->begin_transaction();
    foreach ($passengersData as $p) {
        $seat_id = $p['seat_id'];
        $bus_id = $route['bus_id'];
        $p_name = $p['name'];
        $p_age = $p['age'];
        $p_doc = $p['id'];
        $booking_status = 'PAID';
        $res_check = mysqli_query($conn, "SELECT booking_id FROM bookings WHERE route_id = $route_id AND seat_number = '$seat_id' AND booking_status = 'PAID'");
        if (mysqli_num_rows($res_check) > 0) {
            throw new Exception("Collision Error: Seat $seat_id was just reserved by another customer.");
        }
        $qr_token = bin2hex(random_bytes(16));
        $sql_insert = "INSERT INTO bookings (user_id, route_id, bus_id, seat_number, passenger_name, passenger_age, passenger_id_number, booking_status, qr_token, booking_time) VALUES ($target_user_id, $route_id, $bus_id, '$seat_id', '$p_name', $p_age, '$p_doc', '$booking_status', '$qr_token', NOW())";
        if (!mysqli_query($conn, $sql_insert)) {
            throw new Exception("Persistence Error: Failed to record booking for seat " . $seat_id);
        }
    }
    /* --- [9] FINALIZATION --- */
    $conn->commit();
    ob_get_clean();
    echo json_encode(['success' => true, 'message' => 'Success! All seats reserved.', 'redirect' => 'view_tickets.php', 'ticket_count' => count($passengersData)]);
} catch (Throwable $e) {
    if (isset($conn) && $conn->connect_errno == 0 && ($conn->ping() ?? false)) { // [72.1] Rollback only if connection is still alive.
        $conn->rollback();                                           // [73] UNDO all database changes to maintain data integrity.
    }
    ob_get_clean();
    echo json_encode(['success' => false, 'message' => 'Reservation Aborted: ' . $e->getMessage()]);
}
if (isset($conn) && $conn instanceof mysqli && $conn->connect_errno == 0) {
}
?>