<?php
// =================================================================
// USER PROFILE PAGE (profile.php)
// =================================================================
// This page displays the User's Personal Account Information.
// It is a simple viewing page with no action buttons (read-only views).
// =================================================================

// 1. Include DB connection.
require_once 'db_connection.php';
// 2. Start Session.
session_start();

// --- SECURITY CHECK ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// 3. Get ID.
$user_id = $_SESSION['user_id'];

// 4. Fetch User Data from Database.
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Wema Travellers</title>
    <!-- CSS -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* --- PROFILE CARD STYLES --- */
        .profile-container {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        /* Header section with Role Badge */
        .profile-header {
            text-align: center;
            border-bottom: 2px solid #f0f0f0; /* Separator line */
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        /* Info Grid Layout */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 2fr; /* Label takes 1 part, Value takes 2 parts */
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-label { font-weight: bold; color: var(--purple); }
        .info-value { color: #333; }
        
        /* Role Badge Style */
        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            background: var(--pink);
            color: #1e1b4b;
            font-size: 0.9em;
            text-transform: uppercase;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--purple);
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <!-- Main Content -->
    <div class="profile-container">
        <div class="profile-header">
            <h2>Personal Profile Settings</h2>
            <!-- Show Role Badge (ADMIN, AGENT, PASSENGER) -->
            <div class="role-badge"><?= $_SESSION['role'] ?> Account</div>
        </div>

        <!-- Data Grid -->
        <div class="info-grid">
            <div class="info-label">First Name:</div>
            <div class="info-value"><?= htmlspecialchars($user_data['first_name']) ?></div>

            <div class="info-label">Last Name:</div>
            <div class="info-value"><?= htmlspecialchars($user_data['last_name']) ?></div>

            <div class="info-label">Email Address:</div>
            <div class="info-value"><?= htmlspecialchars($user_data['email']) ?></div>

            <div class="info-label">Phone Number:</div>
            <div class="info-value"><?= htmlspecialchars($user_data['phone_number']) ?></div>

            <div class="info-label">User ID:</div>
            <div class="info-value">#<?= $user_data['user_id'] ?></div>
        </div>

        <a href="dashboard.php" class="back-link">← Return to Dashboard</a>
    </div>

    <!-- Footer -->
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
