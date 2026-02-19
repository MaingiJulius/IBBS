<?php
/**
 * PASSENGER DASHBOARD (dashboard.php)
 * Purpose: This is the primary control panel for registered passengers.
 * It provides shortcuts to core traveler functions like Booking, Ticket Viewing, and History.
 * Accessible to: LOGGED-IN PASSENGERS ONLY.
 */

// --- BOOTSTRAP: CORE SYSTEM ACCESS ---
// Include the database connection so we can fetch specific user data.
require_once 'db_connection.php';

// Start the PHP session to track the user's login state.
session_start();

// --- SECURITY CHECK: AUTHENTICATION ---
// We check if the 'user_id' exists in the current session.
// If it's missing, the person is not logged in, so we kick them back to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit(); // Stop all execution immediately.
}

// Extract the user's role from the session.
$role = $_SESSION['role'];

// --- SECURITY CHECK: ROLE REDIRECTION ---
// This dashboard is specifically designed for the 'PASSENGER' layout.
// If an Admin or Agent accidentally lands here, we redirect them to their respective specialized portals.
if ($role === 'ADMIN') {
    header("Location: admin_dashboard.php");
    exit();
} elseif ($role === 'AGENT') {
    header("Location: agent_dashboard.php");
    exit();
}

// --- DATA FETCHING ---
// Capture the logged-in ID.
$user_id = $_SESSION['user_id'];

// Prepare a secure query to get the user's first name to create a personalized greeting.
$stmt = $conn->prepare("SELECT first_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Fetch the associative array result from the database.
$user_data = $stmt->get_result()->fetch_assoc();

// Close the statement to free up resources.
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- DOCUMENT SETUP -->
    <meta charset="UTF-8">
    <title>Passenger Dashboard - Wema Travellers</title>
    
    <!-- THEME DEPENDENCIES -->
    <link rel="stylesheet" href="css/main.css"> <!-- Site-wide structure -->
    <link rel="stylesheet" href="css/style.css"> <!-- Colors and fonts -->
    
    <style>
        /* DASHBOARD COMPONENT STYLING */
        .dashboard-container {
            max-width: 1000px;           /* Limits width for better readability on desktop */
            margin: 40px auto;          /* Centers the card on the screen */
            background: white;          /* Clean white dashboard card */
            padding: 40px;
            border-radius: 12px;        /* Soft rounded corners */
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); /* Subtle premium shadow for depth */
        }

        /* HEADER SECTION: Title and personalized greeting */
        .dashboard-header {
            text-align: center;
            border-bottom: 2px solid #f0f0f0; /* Light separator line */
            padding-bottom: 20px;
            margin-bottom: 40px;
        }

        /* ACTION GRID: Layout for the three main navigation cards */
        .action-grid {
            display: flex;
            justify-content: center;
            gap: 30px;                  /* Gap between cards */
            flex-wrap: wrap;            /* Allows cards to stack on mobile phones */
        }

        /* INDIVIDUAL ACTION CARD */
        .action-card {
            flex: 1;
            min-width: 250px;           /* Prevents cards from getting too thin */
            text-align: center;
        }

        /* Title inside the card (e.g., "Book trip") */
        .action-card h3 {
            color: var(--purple);       /* Thematic color */
            margin-bottom: 15px;
            font-size: 1.4em;
        }

        /* GIANT ACTION BUTTONS */
        .action-card .btn {
            display: block;             /* Makes the link fill the container area */
            padding: 20px;
            font-size: 1.1em;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            transition: opacity 0.3s;   /* Smooth hover effect */
        }

        /* Hover interaction: makes the button slightly transparent */
        .action-card .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <!-- HEADER INJECTION -->
    <!-- Loads the communal navigation bar (Home, Profile, etc.) -->
    <script src="js/header2.js"></script>
    
    <!-- SPACER: Adjusts for fixed header height -->
    <div style="height: 100px;"></div>

    <!-- MAIN DASHBOARD CARD -->
    <div class="dashboard-container">
        
        <!-- TOP SECTION: Welcome Greeting -->
        <div class="dashboard-header">
            <h2>Passenger Dashboard</h2>
            <!-- Personalized greeting using data fetched from PHP at the top -->
            <p>Welcome back, <?= htmlspecialchars($user_data['first_name']) ?>! What would you like to do today?</p>
        </div>

        <!-- LOWER SECTION: Primary Navigation Hub -->
        <div class="action-grid">
            
            <!-- SECTION 1: BOOKING -->
            <div class="action-card">
                <h3>Book trip</h3>
                <!-- Green button for 'Positive' starting action -->
                <a href="book.php" class="button regular-button green-background" style="text-decoration:none; padding: 20px 40px;">BOOK NOW</a>
            </div>

            <!-- SECTION 2: HISTORY -->
            <div class="action-card">
                <h3>View history</h3>
                <!-- Pink button for secondary action -->
                <a href="view_user_history.php" class="button regular-button pink-background" style="text-decoration:none; padding: 20px 40px;">VIEW HISTORY</a>
            </div>

            <!-- SECTION 3: TICKETS -->
            <div class="action-card">
                <h3>View ticket</h3>
                <!-- Purple button matching the main branding -->
                <a href="view_tickets.php" class="button regular-button purple-background" style="text-decoration:none; padding: 20px 40px;">VIEW TICKETS</a>
            </div>
            
        </div>
    </div>

    <!-- UI FOOTER PADDING -->
    <div style="height: 100px;"></div>
    
    <!-- FOOTER INJECTION -->
    <script src="js/footer.js"></script>
</body>
</html>
