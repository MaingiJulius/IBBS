<?php
/**
 * AGENT OPERATIONAL HUB (agent_dashboard.php)
 * Purpose: This is the dedicated workspace for Wema Travellers Agents.
 * Agents are customer-facing staff who assist passengers with bookings, 
 * cancellations, and real-time travel information.
 */

// --- INITIALIZATION ---
// Include the database connection and start session tracking.
require_once 'db_connection.php';
session_start();

/**
 * --- SECURITY ACCESS CONTROL ---
 * We must ensure that only authorized 'AGENT' or 'ADMIN' users can access this portal.
 * If a regular passenger or an unauthenticated user attempts to access this, 
 * they are immediately booted back to the login page.
 */
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'AGENT' && $_SESSION['role'] !== 'ADMIN')) {
    header("Location: login.html");
    exit();
}

// Extract the operative's name for a personalized greeting.
$agent_name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - Wema Travellers</title>
    
    <!-- site-wide design tokens and layout rules -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* PAGE LAYOUT: Specific visual logic for the Agent Dashboard */
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .back-btn-container {
            padding: 20px 0;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Branding: High-impact welcome banner with Wema gradient */
        .welcome-banner {
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color: #1e1b4b; /* Deep Indigo for contrast */
            padding: 50px 40px;
            border-radius: 20px;
            margin-bottom: 40px;
            text-align: center;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .welcome-banner h1 { margin: 0; font-size: 2.8em; font-weight: 800; }
        .welcome-banner p { margin: 15px 0 0; font-size: 1.3em; font-weight: 500; opacity: 0.85; }
        
        /* Interactive Design: Subtle micro-animations for the module cards */
        .card {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
    </style>
</head>
<body>
    <!-- Universal Site Navigation Component -->
    <script src="js/header2.js"></script>
    
    <!-- Standard spacing for fixed navbar -->
    <div style="height: 100px;"></div>

    <div class="dashboard-container">
        
        <div class="back-btn-container">
            <!-- Navigation path to the central Hub -->
            <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Dashboard Hub</a>
        </div>

        <!-- IDENTITY: Identifying the current staff member -->
        <div class="welcome-banner">
            <h1>Agent Portal</h1>
            <p>Ready for duty, <?= htmlspecialchars($agent_name) ?>. Access your control modules below.</p>
        </div>

        <!-- OPERATIONAL GRID: Logical grouping of Agent tasks -->
        <div class="admin-options" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
            
            <!-- MODULE 1: DESTINATION & PRICING LOOKUP -->
            <div class="card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #eee;">
                <h3>Route Information</h3>
                <p style="color: #666; margin-bottom: 20px;">Review schedules, destinations, and updated ticket costs for passengers.</p>
                <a href="view_routes.php" class="button regular-button pink-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Open Schedule</a>
            </div>

            <!-- MODULE 2: BOOKING MANAGEMENT & CANCELLATIONS -->
            <div class="card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #eee;">
                <h3>Ticket Management</h3>
                <p style="color: #666; margin-bottom: 20px;">Search passenger bookings, process refunds, or handle trip cancellations.</p>
                <a href="view_admin_bookings.php" class="button regular-button pink-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Manage Tickets</a>
            </div>

            <!-- MODULE 3: QUALITY ASSURANCE MONITORING -->
            <div class="card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #eee;">
                <h3>Service Feedback</h3>
                <p style="color: #666; margin-bottom: 20px;">Listen to passenger concerns and monitor service satisfaction levels.</p>
                <a href="view_feedback.php" class="button regular-button pink-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Read Reviews</a>
            </div>

            <!-- MODULE 4: REAL-TIME CAPACITY CHECKING -->
            <div class="card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #eee;">
                <h3>Bus Occupancy</h3>
                <p style="color: #666; margin-bottom: 20px;">Visual map of seat availability across all active fleet vehicles.</p>
                <a href="view_bus_occupancy.php" class="button regular-button pink-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Check Capacity</a>
            </div>

            <!-- MODULE 5: WALK-IN CUSTOMER BOOKING ENGINE -->
            <div class="card" style="background: #f0fdf4; padding: 30px; border-radius: 12px; border: 2px solid #22c55e;">
                <h3 style="color: #166534;">Instant Booking</h3>
                <p style="color: #166534; margin-bottom: 20px;">Accelerated flow specifically designed for booking walk-in office customers.</p>
                <a href="book.php" class="button regular-button green-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Open Booking Engine</a>
            </div>

            <!-- MODULE 6: PASSENGER ONBOARDING -->
            <div class="card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #eee;">
                <h3>User Management</h3>
                <p style="color: #666; margin-bottom: 20px;">Register new passengers or update existing traveller contact profiles.</p>
                <a href="view_users_sorted.php" class="button regular-button pink-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Profiles Hub</a>
            </div>

            <!-- MODULE 7: BOARDING GATE VERIFICATION (ROADMAP) -->
            <div class="card" style="background: #fafafa; padding: 30px; border-radius: 12px; border: 1px dashed #ccc; opacity: 0.8;">
                <h3 style="color: #999;">QR Token Scanner</h3>
                <p style="color: #999; margin-bottom: 20px;">Future enhancement for digital boarding verification via mobile tokens.</p>
                <a href="#" class="button regular-button" style="text-decoration:none; width: 100%; display: block; text-align: center; background: #ddd; color: #777; cursor: not-allowed;">Coming Soon</a>
            </div>

        </div>
    </div>

    <!-- Layout Footer padding -->
    <div style="height: 100px;"></div>
    
    <!-- Universal Site Footer component -->
    <script src="js/footer.js"></script>
</body>
</html>
