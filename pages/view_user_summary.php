<?php
// =================================================================
// VIEW: USER SUMMARY
// =================================================================
// Shows user profile information.
// =================================================================
require_once 'db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Access Denied.");
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile Summary</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'passenger') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <h2>My Profile</h2>
    <?php
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<p><strong>First Name:</strong> " . $user['first_name'] . "</p>";
        echo "<p><strong>Last Name:</strong> " . $user['last_name'] . "</p>";
        echo "<p><strong>Email:</strong> " . $user['email'] . "</p>";
        echo "<p><strong>Phone:</strong> " . $user['phone_number'] . "</p>";
        echo "<p><strong>Role:</strong> " . $user['role'] . "</p>";
        echo "<p><strong>Member Since:</strong> " . date("F j, Y", strtotime("now")) . "</p>"; // Assuming current for prototype or add created_at col
    }
    $stmt->close();
    ?>
    <script src="js/footer.js"></script>
</body>
</html>
