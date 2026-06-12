<?php
session_start();
require_once 'db_connection.php';
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.html");
    exit(); // Terminate script execution immediately.
}
// Views are virtual tables that simplify complex JOINs into a single SELECT.
$sql = "SELECT * FROM user_summary WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>
<head>
  <link rel="stylesheet" href="css/main.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/entry-page.css" />
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'passenger') ?>-role">
<script src="js/header2.js"></script>
<div style="height: 100px;"></div>
<script src="js/footer.js"></script>
<div class="container" style="max-width: 700px; margin: 50px auto 30px; padding: 20px; height: 80vh; overflow-y: auto; background-color: #fff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);">
  <?php if ($user): ?>
    <table style="width: 100%; border: 1px solid #ccc; border-collapse: collapse; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
      <thead>
        <tr style="background-color: #f8f8f8;">
          <th style="padding: 12px; border: 1px solid #ccc; text-align: left;">Field</th>
          <th style="padding: 12px; border: 1px solid #ccc; text-align: left;">Value</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="padding: 12px; border: 1px solid #ccc;"><?= htmlspecialchars($user['user_id']) ?></td>
        </tr>
        <tr>
        </tr>
        <tr>
        </tr>
        <tr>
          <td style="padding: 12px; border: 1px solid #ccc;"><?= htmlspecialchars($user['email']) ?></td>
        </tr>
      </tbody>
    </table>
  <?php else: ?>
    <p style="text-align: center; color: red;">No user information found.</p>
  <?php endif; ?>
</div>
</body>
</html>