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
        
        /* Body: Soft grey background to make cards "pop" */
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        /* Container: Centers the dashboard on the screen */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        /**
         * The Grid Layout:
         * We use 'grid' to align our management cards in a clean row/column structure.
         * 'repeat(auto-fit, minmax(280px, 1fr))' is a magic responsive rule:
         * It fits as many cards as it can in a row, but never smaller than 280px.
         */
        .admin-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 25px; /* Spacing between cards */
            padding: 20px 0;
        }

        /* Card Styling: Each card represents one module (Users, Routes, etc.) */
        .card {
            background-color: #ffffff;
            border: 1px solid #e1e8ed;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease; /* Smooth hover transition */
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); /* Soft shadow */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Space button at bottom */
        }

        /* Card Interaction: Lift and Glow on hover */
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--purple);
        }

        /* Card Typography */
        .card h3 {
            margin-top: 0;
            color: var(--purple);
            font-size: 1.4rem;
        }

        .card p {
            color: #5a6b7d;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        /* Layout for the 'Back' link container */
        .back-btn-container {
            margin-bottom: 20px;
        }

        /**
         * The Welcome Banner:
         * A visually striking section to greet the Admin.
         * Uses a gradient path from Purple to Pink (Wema Branding).
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
        
        .welcome-banner h1 {
            margin: 0;
            font-size: 2.5rem;
            letter-spacing: -1px;
        }
        
        .welcome-banner p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Load the common Header script -->
    <script src="js/header2.js"></script>
    
    <!-- Buffer to push content below the fixed header -->
    <div style="height: 100px;"></div>

    <div class="container">
        
        <!-- Navigation: Allow Admin to return to the shared Dashboard.php router -->
        <div class="back-btn-container">
            <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← External Dashboard</a>
        </div>

        <!-- HERO SECTION: Greeting -->
        <div class="welcome-banner">
            <h1>Administrative Operations</h1>
            <!-- Logic: Grab Admin's name from session and sanitize it for the screen -->
            <p>Authorized access granted to: <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></p>
        </div>

        <!-- MODULE GRID: Management options organized in cards -->
        <div class="admin-options">
            
            <!-- MODULE 1: CUSTOMER BOOKING -->
            <div class="card">
                <h3>Walk-in Booking</h3>
                <p>Register tickets for customers who arrive at the station without a mobile account.</p>
                <a href="book.php" class="button regular-button green-background" style="text-decoration:none;">Open Booking Desk</a>
            </div>

            <!-- MODULE 2: USER BASE -->
            <div class="card">
                <h3>User Accounts</h3>
                <p>Full control over passengers, agents, and other administrators.</p>
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

            <!-- MODULE 11: SECURITY GATE (FUTURE) -->
            <div class="card">
                <h3>Gate Verification</h3>
                <p>Toolkit for verifying passenger digital hashes during boarding.</p>
                <!-- NOTE: Visual indicator of a work-in-progress feature -->
                <a href="#" class="button regular-button pink-background" style="text-decoration:none; opacity: 0.5; cursor: not-allowed;">(Coming Soon)</a>
            </div>

        </div> <!-- Grid End -->
    </div> <!-- Container End -->

    <!-- Final Spacing for visual balance -->
    <div style="height: 100px;"></div>
    
    <!-- Inject Global Footer -->
    <script src="js/footer.js"></script>

</body>
</html>
