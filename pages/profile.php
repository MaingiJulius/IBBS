<?php
// =================================================================
// USER PROFILE PAGE (profile.php)
// =================================================================
// Purpose: This page serves as a personal dashboard for the user to view 
// their own account details stored in the Wema Travellers system.
// Functionality: 
// 1. Validates that the user is logged in.
// 2. Retrieves the specific user's record from the MySQL database.
// 3. Formats and displays the data in a clean, card-based layout.
// =================================================================

// --- STEP 1: INITIALIZATION ---
// Link the primary database bridge (server, credentials, connection logic).
require_once 'db_connection.php';

// Start a session to track the identity and status of the current visitor.
session_start();

/**
 * --- STEP 2: SECURITY ACCESS CONTROL ---
 * We must verify that the visitor is actually an authenticated user.
 * We check if 'user_id' exists in their session "notebook".
 * If it's missing, it means they are trying to peek at this page without logging in.
 */
if (!isset($_SESSION['user_id'])) {
    // SECURITY ACTION: Bounce the unauthorized visitor back to the login portal.
    header("Location: login.html");
    // Terminate script execution to prevent the sensitive profile UI from loading.
    exit();
}

/**
 * --- STEP 3: DATA RETRIEVAL LOGIC ---
 * Now that we know who the user is, we need to get their full dossier from the database.
 */
// Grab the user's unique identifier from the session memory.
$user_id = $_SESSION['user_id'];

// PREPARE: Set up the SQL template. We use '?' (a placeholder) to prevent SQL injection.
// We are selecting all columns (*) for the user matching our current ID.
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");

// BIND: Connect the PHP variable '$user_id' to the SQL placeholder '?' as an integer ('i').
$stmt->bind_param("i", $user_id);

// EXECUTE: Tell the database to run the search.
$stmt->execute();

// CAPTURE: Fetch the resulting data row into an associative array called '$user_data'.
// This array will hold keys like 'first_name', 'email', 'phone_number', etc.
$user_data = $stmt->get_result()->fetch_assoc();

// CLEANUP: Close the statement tool to save server memory.
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en"> <!-- Standard HTML5 document language declaration. -->
<head>
    <meta charset="UTF-8"> <!-- Enforces UTF-8 character encoding for broad compatibility. -->
    <title>My Profile - Wema Travellers</title> <!-- The text that appears in the browser tab. -->
    
    <!-- EXTERNAL STYLESHEETS -->
    <link rel="stylesheet" href="css/main.css"> <!-- Links the general layout and typography rules. -->
    <link rel="stylesheet" href="css/style.css"> <!-- Links the core theme variables (colors, spacing). -->
    
    <style>
        /* --- INTERNAL CSS STYLING --- */
        /* Purpose: Specifically designed for the profile layout. */

        /* The main card that holds all user information. */
        .profile-container {
            max-width: 700px; /* Limits width to keep text readable. */
            margin: 40px auto; /* Adds vertical spacing and centers the card horizontally. */
            background: white; /* Clean white background for the card. */
            padding: 40px; /* Internal spacing between the border and content. */
            border-radius: 12px; /* Smooth, modern rounded corners. */
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); /* Soft shadow for a "floating" look. */
        }
        
        /* Header section containing the title and account type. */
        .profile-header {
            text-align: center; /* Centers the text alignment. */
            border-bottom: 2px solid #f0f0f0; /* Light gray separator line. */
            padding-bottom: 20px; /* Space above the separator line. */
            margin-bottom: 30px; /* Space below the separator line. */
        }
        
        /* Grid layout for aligning fields (labels on left, values on right). */
        .info-grid {
            display: grid; /* Enables CSS Grid layout. */
            grid-template-columns: 1fr 2fr; /* Divide: 1 part for labels, 2 parts for values. */
            gap: 20px; /* Spacing between grid items. */
            margin-bottom: 30px; /* Space at the bottom of the grid. */
        }

        /* Styling for the bold field labels. */
        .info-label { 
            font-weight: bold; /* Makes the text bold. */
            color: var(--purple); /* Uses the theme's core purple color. */
        }

        /* Styling for the actual user data values. */
        .info-value { 
            color: #333; /* Dark gray for high contrast and readability. */
        }
        
        /* The badge that shows the user's role (e.g., ADMIN Account). */
        .role-badge {
            display: inline-block; /* Allows padding and keeps it on its own line. */
            padding: 5px 15px; /* Internal spacing. */
            border-radius: 20px; /* Pill-shaped design. */
            background: var(--pink); /* Vibrant theme color for visibility. */
            color: #1e1b4b; /* Deep navy text for contrast on pink. */
            font-size: 0.9em; /* Slightly smaller than regular text. */
            text-transform: uppercase; /* Converts role to ALL CAPS. */
        }
        
        /* The link at the bottom that goes back to the dashboard. */
        .back-link {
            display: block; /* Turns the link into a block to allow centering. */
            text-align: center; /* Centers the link text. */
            margin-top: 20px; /* spacing above the link. */
            color: var(--purple); /* Matches theme colors. */
            text-decoration: none; /* Removes the default underline. */
            font-weight: bold; /* Makes it stand out. */
        }
    </style>
</head>
<body>
    <!-- NAVIGATION COMPONENT -->
    <!-- This script dynamically injects the site's top navigation bar. -->
    <script src="js/header2.js"></script>
    
    <!-- PADDING: Ensures the profile content isn't hidden by the fixed header. -->
    <div style="height: 100px;"></div>

    <!-- CORE INTERFACE -->
    <div class="profile-container"> <!-- Main content card. -->
        <div class="profile-header">
            <h2>Personal Profile Settings</h2>
            <!-- SHOWCASE: Display the user's account type (PASSENGER, ADMIN, or AGENT). -->
            <!-- We echo the role directly from the active session. -->
            <div class="role-badge"><?= $_SESSION['role'] ?> Account</div>
        </div>

        <!-- DATA MATRIX: Organized labels and user-specific values -->
        <div class="info-grid">
            <!-- FIELD: First Name -->
            <div class="info-label">First Name:</div>
            <!-- htmlspecialchars() ensures the output is safe from cross-site scripting. -->
            <div class="info-value"><?= htmlspecialchars($user_data['first_name']) ?></div>

            <!-- FIELD: Last Name -->
            <div class="info-label">Last Name:</div>
            <div class="info-value"><?= htmlspecialchars($user_data['last_name']) ?></div>

            <!-- FIELD: Primary Email -->
            <div class="info-label">Email Address:</div>
            <div class="info-value"><?= htmlspecialchars($user_data['email']) ?></div>

            <!-- FIELD: Contact Number -->
            <div class="info-label">Phone Number:</div>
            <div class="info-value"><?= htmlspecialchars($user_data['phone_number']) ?></div>

            <!-- FIELD: Internal Database ID -->
            <div class="info-label">User ID:</div>
            <div class="info-value">#<?= $user_data['user_id'] ?></div>
        </div>

        <!-- ACTION: Way-finding link back to the dashboard. -->
        <a href="dashboard.php" class="back-link">← Return to Dashboard</a>
    </div>

    <!-- FOOTER PADDING -->
    <div style="height: 100px;"></div>
    
    <!-- FOOTER COMPONENT -->
    <!-- Injects the standard global footer using JavaScript. -->
    <script src="js/footer.js"></script>
</body>
</html>
