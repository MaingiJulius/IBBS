<?php
/**
 * ADMIN DASHBOARD (admin_dashboard.php)
 * Purpose: This page serves as the "Master Control Center" for Wema Travellers Administrators.
 * It provides a visual hub with links to all system management modules.
 * Accessible only to: Users with the 'ADMIN' role.
 */

// --- STEP 1: SESSION MANAGEMENT ---
// session_start() allows the script to identify the visitor.
session_start();

/**
 * --- STEP 2: SECURITY ACCESS CONTROL ---
 * We verify that the user is actually an Administrator.
 * 1. Is 'role' set in their session notebook?
 * 2. Is that role exactly 'ADMIN'?
 * If not, we immediately bounce them back to the login screen for safety.
 */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    // header("Location: ...") sends the browser to login.html.
    header("Location: login.html");
    // exit() ensures the sensitive dashboard content below is never loaded for unauthorized users.
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Standard meta-tags for web compatibility -->
    <meta charset="UTF-8">
    <title>Master Control Panel - Wema Travellers</title>
    <!-- Viewport tag makes the dashboard look good on smartphones and tablets -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS LINKS: Pulling in global styles for a consistent brand look -->
    <link rel="stylesheet" href="css/style.css">       <!-- Main Theme (Colors/Fonts) -->
    <link rel="stylesheet" href="css/main.css">        <!-- Layout Rules (Containers/Grids) -->
    <link rel="stylesheet" href="css/entry-page.css">  <!-- Utility styles for buttons and forms -->

    <style>
        /* --- DASHBOARD SPECIFIC STYLES --- */
        /* Purpose: To create a professional, organized "Control Center" look for Administrators. */
        
        /* Body Overrides: 
           - 'Segoe UI' provides a clean, modern look.
           - #f7f9fc is a very light blue/grey that reduces eye strain and contrasts well with white cards.
           - margin 0 and padding 0 reset browser defaults.
        */
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        /* Container: 
           - Limits layout width to 1200px for consistency on large monitors.
           - auto margin centers the entire dashboard horizontally.
           - 30px vertical margin provides "breathing room" from the header.
        */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        /**
         * The Grid Layout System:
         * - display: grid; enables the modern grid layout engine.
         * - grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
         *   ^ This is "magical" CSS: it automatically wraps cards to new lines 
         *     as the screen gets smaller, ensuring responsive design without media queries.
         * - gap: 25px; sets the literal physical distance between each grid cell (the cards).
         */
        .admin-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 25px; 
            padding: 20px 0;
        }

        /* Card Styling (The individual module boxes):
           - white background identifies the interactive area.
           - border-radius: 12px creates the modern "rounded" look.
           - flex-direction: column + justify-content: space-between; 
             ^ This ensures the "Manage" button is always pinned to the very bottom of the card,
               regardless of how much description text is in the middle.
        */
        .card {
            background-color: #ffffff;
            border: 1px solid #e1e8ed;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease; /* Enables smooth animations for all changes. */
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); /* Very subtle bottom shadow for depth. */
            display: flex;
            flex-direction: column;
            justify-content: space-between; 
        }

        /* Card Interaction (Hover Effect):
           - translateY(-8px) makes the card "lift" off the page when the mouse hovers over it.
           - box-shadow increases to simulate the card casting a larger shadow as it "rises".
           - border-color changing to purple highlights the active choice.
        */
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--purple);
        }

        /* Card Typography (Titles):
           - var(--purple) keeps the branding consistent.
           - font-size: 1.4rem makes headings clear but not overwhelming.
        */
        .card h3 {
            margin-top: 0;
            color: var(--purple);
            font-size: 1.4rem;
        }

        /* Card Body Text:
           - #5a6b7d is a softer grey that is easier to read in bulk.
           - line-height: 1.5 adds vertical space between lines of text.
        */
        .card p {
            color: #5a6b7d;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        /* Spacing for the top navigation button. */
        .back-btn-container {
            margin-bottom: 20px;
        }

        /**
         * The Welcome Banner (The Hero Section):
         * - linear-gradient(135deg...) creates the vibrant color blend transition.
         * - padding: 50px creates a large, professional greeting area.
         * - border-radius: 16px rounds the corners slightly more than the cards.
         */
        .welcome-banner {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); 
            color: #ffffff;
            padding: 50px 20px;
            border-radius: 16px;
            margin-bottom: 40px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(37, 117, 252, 0.2);
        }
        
        /* Large banner heading styling. */
        .welcome-banner h1 {
            margin: 0;
            font-size: 2.5rem;
            letter-spacing: -1px; /* Tightens the letters for a "bold" title look. */
        }
        
        /* Sub-text inside the banner. */
        .welcome-banner p {
            font-size: 1.1rem;
            opacity: 0.9; /* Slightly transparent to let the gradient breathe. */
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- NAVIGATION COMPONENT injection -->
    <!-- This JavaScript script replaces this line with the global header HTML. -->
    <script src="js/header2.js"></script>
    
    <!-- LAYOUT ADJUSTMENT: Ensures the content doesn't start underneath the sticky header. -->
    <div style="height: 100px;"></div>

    <div class="container"> <!-- Main content constraint. -->
        
        <!-- BACK NAVIGATION: Link to exit the administrative subsystem. -->
        <div class="back-btn-container">
            <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← External Dashboard</a>
        </div>

        <!-- IDENTITY BANNER: Welcoming the current Admin. -->
        <div class="welcome-banner">
            <h1>Administrative Operations</h1>
            <!-- PHP logic to echo the name stored in the session memory. -->
            <p>Authorized access granted to: <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></p>
        </div>

        <!-- OPERATIONAL HUB: The Grid container for management cards. -->
        <div class="admin-options">
            
            <!-- MODULE 1: CUSTOMER BOOKING -->
            <div class="card"> <!-- Each 'card' represents a functional area. -->
                <h3>Walk-in Booking</h3>
                <p>Register tickets for customers who arrive at the station without a mobile account.</p>
                <!-- Green background indicates a "Go/Action" portal. -->
                <a href="book.php" class="button regular-button green-background" style="text-decoration:none;">Open Booking Desk</a>
            </div>

            <!-- MODULE 2: USER BASE -->
            <div class="card">
                <h3>User Accounts</h3>
                <p>Full control over passengers, agents, and other administrators.</p>
                <!-- Pink background indicates administrative/registry management. -->
                <a href="view_users_sorted.php" class="button regular-button pink-background" style="text-decoration:none;">Manage Identities</a>
            </div>

            <!-- MODULE 3: TRANSPORT NETWORK -->
            <div class="card">
                <h3>Trip Routes</h3>
                <p>Define departure cities, destinations, and adjust ticket prices dynamically.</p>
                <a href="view_routes.php" class="button regular-button pink-background" style="text-decoration:none;">Manage Network</a>
            </div>

            <!-- MODULE 4: TICKET RECORDS -->
            <div class="card">
                <h3>Global Bookings</h3>
                <p>Audit every ticket sold and process manual cancellations if requested.</p>
                <a href="view_admin_bookings.php" class="button regular-button pink-background" style="text-decoration:none;">Audit Tickets</a>
            </div>

            <!-- MODULE 5: STAFF RECORDS -->
            <div class="card">
                <h3>Crew Management</h3>
                <p>Maintain the official registry of licensed bus drivers and their contacts.</p>
                <a href="admin_drivers.php" class="button regular-button pink-background" style="text-decoration:none;">Manage Crew</a>
            </div>

            <!-- MODULE 6: FLEET ASSETS -->
            <div class="card">
                <h3>Bus Fleet</h3>
                <p>Register new physical buses and assign specific drivers to vehicles.</p>
                <a href="admin_buses_report.php" class="button regular-button pink-background" style="text-decoration:none;">Manage Assets</a>
            </div>

            <!-- MODULE 7: QUALITY CONTROL -->
            <div class="card">
                <h3>User Feedback</h3>
                <p>Read customer reviews and moderate any inappropriate content.</p>
                <a href="view_feedback.php" class="button regular-button pink-background" style="text-decoration:none;">Review Ratings</a>
            </div>

            <!-- MODULE 8: FINANCIALS -->
            <div class="card">
                <h3>Revenue Insights</h3>
                <p>High-level report of income generated across all routes and time periods.</p>
                <a href="view_revenue_report.php" class="button regular-button pink-background" style="text-decoration:none;">View Cashflow</a>
            </div>

            <!-- MODULE 9: LOGISTICS -->
             <div class="card">
                <h3>Vehicle Occupancy</h3>
                <p>Real-time look at how full each bus is to optimize departure schedules.</p>
                <a href="view_bus_occupancy.php" class="button regular-button pink-background" style="text-decoration:none;">Check Capacity</a>
            </div>

            <!-- MODULE 10: PERFORMANCE -->
            <div class="card">
                <h3>Agent Sales</h3>
                <p>Leaderboard and performance metrics for ticket agents in the field.</p>
                <a href="view_agent_sales.php" class="button regular-button pink-background" style="text-decoration:none;">Sales Metrics</a>
            </div>

            <!-- MODULE 11: SECURITY GATE (FUTURE ENHANCEMENT) -->
            <!-- STYLING LOGIC: We represent an 'inactive' or 'Coming Soon' state here. -->
            <div class="card">
                <h3>Gate Verification</h3>
                <p>Toolkit for verifying passenger digital hashes during boarding.</p>
                
                <!-- 
                     UNDER THE HOOD (Visual Logic):
                     1. href="#": This points to nowhere, preventing the browser from navigating.
                     2. opacity: 0.5: This CSS property makes the element 50% transparent, visually "fading" it into the background.
                     3. cursor: not-allowed: This specifically triggers the 'circle-slash' mouse pointer, 
                        providing immediate haptic feedback to the user that this element is not interactive.
                -->
                <a href="#" class="button regular-button pink-background" style="text-decoration:none; opacity: 0.5; cursor: not-allowed;">(Coming Soon)</a>
            </div>

        </div> <!-- Closes the responsive grid layout. -->
    </div> <!-- Closes the main container. -->

    <!-- VISUAL PADDING: Ensures enough scroll space at the bottom of the page. -->
    <div style="height: 100px;"></div>
    
    <!-- FOOTER COMPONENT injection -->
    <!-- This JavaScript script injects the standard site-wide footer. -->
    <script src="js/footer.js"></script>

</body>
</html>
