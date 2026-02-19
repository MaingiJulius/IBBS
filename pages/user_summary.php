<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.html");
    exit();
}

// Fetch user data from the user_summary view
$sql = "SELECT * FROM user_summary WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Profile</title>
  <link rel="stylesheet" href="css/main.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/entry-page.css" />
</head>
<body>

<!-- Include header and footer scripts -->
<script src="js/header.js"></script>
<script src="js/footer.js"></script>

<!-- Content Section -->
<div class="container" style="max-width: 700px; margin: 50px auto 30px; padding: 20px; height: 80vh; overflow-y: auto; background-color: #fff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);">

  <h1 style="text-align: center; font-size: 2rem; margin-bottom: 30px;">Your Profile</h1>

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
          <td style="padding: 12px; border: 1px solid #ccc;">User ID</td>
          <td style="padding: 12px; border: 1px solid #ccc;"><?= htmlspecialchars($user['user_id']) ?></td>
        </tr>
        <tr>
          <td style="padding: 12px; border: 1px solid #ccc;">First Name</td>
          <td style="padding: 12px; border: 1px solid #ccc;"><?= htmlspecialchars($user['first_name']) ?></td>
        </tr>
        <tr>
          <td style="padding: 12px; border: 1px solid #ccc;">Last Name</td>
          <td style="padding: 12px; border: 1px solid #ccc;"><?= htmlspecialchars($user['last_name']) ?></td>
        </tr>
        <tr>
          <td style="padding: 12px; border: 1px solid #ccc;">Email</td>
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
