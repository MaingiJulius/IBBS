<?php
session_start();
require_once 'db_connection.php';
$user_id = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Submitted - Wema Transport</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div id="navbar-placeholder"></div>
    <div class="container">
        <h2>Thank You!</h2>
        <p>Your feedback has been submitted successfully. We appreciate your input and will use it to improve our services.</p>
      <button onclick="window.location.href='home.php'" class="btn">Return to Home</button>
    </div>
    <div id="footer-placeholder"></div>
    <script src="js/header.js"></script>
    <script src="js/footer.js"></script>
</body>
</html>