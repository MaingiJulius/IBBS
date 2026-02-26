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
        /* Purpose: To provide a clean, uncluttered interface for fast operational tasks. */

        /* Main Container: 
           - Centers the content and limits width for readability.
        */
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Spacing for the top navigation button. */
        .back-btn-container {
            padding: 20px 0;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Welcome Banner: 
           - High-impact visual using the brand's purple-to-pink gradient.
           - #1e1b4b (Deep Indigo) is used for the text to ensure accessibility and contrast on light gradients.
           - padding: 50px 40px creates a prominent greeting area.
           - box-shadow: 0 15px 30px... adds depth and separates the banner from the background.
        */
        .welcome-banner {
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color: #1e1b4b; 
            padding: 50px 40px;
            border-radius: 20px;
            margin-bottom: 40px;
            text-align: center;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        /* Banner Typography: 
           - Large, bold headings for clear identity.
           - Opacity 0.85 makes the sub-text secondary but still legible.
        */
        .welcome-banner h1 { margin: 0; font-size: 2.8em; font-weight: 800; }
        .welcome-banner p { margin: 15px 0 0; font-size: 1.3em; font-weight: 500; opacity: 0.85; }
        
        /* Interactive Cards:
           - transition Rule: Uses a 'cubic-bezier' curve for a "springy" or professional feel during hover.
           - box-shadow transition ensures the shadow grows smoothly as the card lifts.
        */
        .card {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }
        
        /* Card Hover State:
           - translateY(-8px) physically "lifts" the card.
           - increased opacity of box-shadow simulates the card moving closer to the user.
        */
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
    </style>
</head>
<body>
    <!-- HEADER COMPONENT injection -->
    <!-- This script dynamically pulls the global navigation bar into the page. -->
    <script src="js/header2.js"></script>
    
    <!-- PADDING: Buffer to prevent the dashboard from hiding behind the sticky navbar. -->
    <div style="height: 100px;"></div>

    <div class="dashboard-container"> <!-- Main content wrapper. -->
        
        <div class="back-btn-container">
            <!-- NAVIGATION: Returns the agent to the primary system router. -->
            <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Dashboard Hub</a>
        </div>

        <!-- IDENTITY SECTION: Personalizing the operative's workspace. -->
        <div class="welcome-banner">
            <h1>Agent Portal</h1>
            <!-- PHP echo: Injects the name of the logged-in agent. -->
            <p>Ready for duty, <?= htmlspecialchars($agent_name) ?>. Access your control modules below.</p>
        </div>

        <!-- TASK GRID: Organizes operational modules in a responsive grid. -->
        <!-- display: grid allows automatic scaling for mobile and desktop screens. -->
        <div class="admin-options" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
            
            <!-- MODULE 1: DESTINATION & PRICING LOOKUP -->
            <div class="card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #eee;">
                <h3>Route Information</h3>
                <p style="color: #666; margin-bottom: 20px;">Review schedules, destinations, and updated ticket costs for passengers.</p>
                <!-- pink-background identifies management/listing actions. -->
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
            <!-- Highlighted card: Uses green/light-green to signify this as the primary "SALES" action tool. -->
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

            <!-- MODULE 7: BOARDING GATE VERIFICATION (ROADMAP FEATURE) -->
            <!-- 
                 STYLING BLUEPRINT (Coming Soon State):
                 To create a "Disabled" or "Work-in-progress" look, we apply several CSS layer:
                 1. background: #fafafa (Off-white): Differentiates the card from active white cards.
                 2. border: 1px dashed #ccc: A dashed line is the universal UI language for "incomplete" or "provisional".
                 3. opacity: 0.8: Slightly fades the entire container to show it's not the primary focus.
            -->
            <div class="card" style="background: #fafafa; padding: 30px; border-radius: 12px; border: 1px dashed #ccc; opacity: 0.8;">
                <h3 style="color: #999;">QR Token Scanner</h3>
                <p style="color: #999; margin-bottom: 20px;">Future enhancement for digital boarding verification via mobile tokens.</p>
                
                <!-- 
                     DISABLED INTERACTION LOGIC:
                     - background: #ddd: A grey background for a button is a "dead" state indicator.
                     - cursor: not-allowed: Forces the browser to show the 'Stop' icon when hovering.
                     - href="#": Prevents any unwanted page reloads or navigation.
                -->
                <a href="#" class="button regular-button" style="text-decoration:none; width: 100%; display: block; text-align: center; background: #ddd; color: #777; cursor: not-allowed;">Coming Soon</a>
            </div>

        </div> <!-- Closes operational grid. -->
    </div> <!-- Closes main container. -->

    <!-- VISUAL PADDING: Ensures the page doesn't end abruptly against the footer. -->
    <div style="height: 100px;"></div>
    
    <!-- FOOTER COMPONENT injection -->
    <!-- This JavaScript script replaces this line with the standard site footer. -->
    <script src="js/footer.js"></script>
</body>
</html>
