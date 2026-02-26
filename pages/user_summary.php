<?php
// Start the PHP session - this is mandatory to access data stored in $_SESSION like 'user_id'.
session_start();

// require_once: Loads the database connection logic. 'once' prevents errors if it's already loaded elsewhere.
require_once 'db_connection.php';

// Check if user is logged in using the null coalescing operator (??).
// If $_SESSION['user_id'] is not set, $user_id will become NULL.
$user_id = $_SESSION['user_id'] ?? null;

// Security check: If the user is not authenticated, kick them back to the login page.
if (!$user_id) {
    header("Location: login.html"); // Redirect instruction.
    exit(); // Terminate script execution immediately.
}

// Fetch user data from the 'user_summary' database view.
// Views are virtual tables that simplify complex JOINs into a single SELECT.
$sql = "SELECT * FROM user_summary WHERE user_id = ?"; // ? is a placeholder for security.
$stmt = $conn->prepare($sql); // Prepares the SQL statement to prevent injection.
$stmt->bind_param("i", $user_id); // Binds the $user_id variable to the 'i' (integer) placeholder.
$stmt->execute(); // Runs the query on the database server.
$result = $stmt->get_result(); // Captures the resulting data set.
$user = $result->fetch_assoc(); // Fetches the user data as an associative array.

// Clean up: Close the statement and database connection to save server resources.
$stmt->close();
$conn->close();
?>

<!DOCTYPE html> <!-- Declares the document type as HTML5. -->
<html lang="en"> <!-- Root element. -->
<head>
  <meta charset="UTF-8" /> <!-- Standard characters. -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/> <!-- Mobile responsiveness. -->
  <title>Your Profile</title> <!-- Tab title. -->
  <!-- Link external CSS files for branding (pink/purple themes) -->
  <link rel="stylesheet" href="css/main.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/entry-page.css" />
</head>
<body>

<!-- Dynamically load the header and footer via JavaScript modules -->
<script src="js/header.js"></script>
<script src="js/footer.js"></script>

<!-- Main Profile Content Section -->
<div class="container" style="max-width: 700px; margin: 50px auto 30px; padding: 20px; height: 80vh; overflow-y: auto; background-color: #fff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);">

  <h1 style="text-align: center; font-size: 2rem; margin-bottom: 30px;">Your Profile</h1> <!-- Heading. -->

  <?php if ($user): ?> <!-- Logic: Only render the table if user data was successfully fetched. -->
    <table style="width: 100%; border: 1px solid #ccc; border-collapse: collapse; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
      <thead>
        <tr style="background-color: #f8f8f8;"> <!-- Light grey background for the header row. -->
          <th style="padding: 12px; border: 1px solid #ccc; text-align: left;">Field</th> <!-- Column 1 header. -->
          <th style="padding: 12px; border: 1px solid #ccc; text-align: left;">Value</th> <!-- Column 2 header. -->
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="padding: 12px; border: 1px solid #ccc;">User ID</td> <!-- Field name. -->
          <td style="padding: 12px; border: 1px solid #ccc;"><?= htmlspecialchars($user['user_id']) ?></td> <!-- Dynamic User ID with XSS protection. -->
        </tr>
        <tr>
          <td style="padding: 12px; border: 1px solid #ccc;">First Name</td> <!-- Field name. -->
          <td style="padding: 12px; border: 1px solid #ccc;"><?= htmlspecialchars($user['first_name']) ?></td> <!-- Dynamic First Name. -->
        </tr>
        <tr>
          <td style="padding: 12px; border: 1px solid #ccc;">Last Name</td> <!-- Field name. -->
          <td style="padding: 12px; border: 1px solid #ccc;"><?= htmlspecialchars($user['last_name']) ?></td> <!-- Dynamic Last Name. -->
        </tr>
        <tr>
          <td style="padding: 12px; border: 1px solid #ccc;">Email</td> <!-- Field name. -->
          <td style="padding: 12px; border: 1px solid #ccc;"><?= htmlspecialchars($user['email']) ?></td> <!-- Dynamic Email Address. -->
        </tr>
      </tbody>
    </table>
  <?php else: ?>
    <!-- Error message if for some reason the database did not return the user record. -->
    <p style="text-align: center; color: red;">No user information found.</p>
  <?php endif; ?>

</div>

</body>
</html>
