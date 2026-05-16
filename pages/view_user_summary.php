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
    /* $stmt (statement tool) -> (connector) bind_param (bind parameter) 
       is the security function that attaches the data to the query blueprint. 
       ( starts. "i" (integer number type) , (comma) $user_id (the data) ) ends. 
       ; (semicolon). */
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        /* $user (container) = (assignment). $result (the found list) -> (connector) 
           fetch_assoc (fetch associative) pulls one row and converts it into 
           labeled pieces. ( ) (empty brackets). ; (semicolon). */
        echo "<p><strong>First Name:</strong> " . htmlspecialchars($user['first_name']) . "</p>";
        /* . (dot) connects text. html (HyperText) special (special) chars (characters) 
           is a security tool that encodes text for safety. ( starts the tool. 
           $user (data list) ['first_name'] (label) ) ends. */
        echo "<p><strong>Last Name:</strong> " . htmlspecialchars($user['last_name']) . "</p>";
        /* htmlspecialchars (security tool) ( $user ['last_name'] ) */
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
