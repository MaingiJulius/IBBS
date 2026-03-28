<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * AGENT OPERATIONAL HUB (agent_dashboard.php)                       // [3] Title identifying this script as the ticket agent workstation.
 * Purpose: This is the dedicated workspace for Wema Travellers Agents. // [4] Core objective: operational gateway for field staff.
 * Agents are customer-facing staff who assist passengers with bookings, // [5] Task: bridge for bookings and cancellations.
 * cancellations, and real-time travel information.                 // [6] Task: information hub for commuters.
 */                                                                  // [7] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [8] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [9] Initialize or resume user session to identify the operative.

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'AGENT' && $_SESSION['role'] !== 'ADMIN')) { // [10] Restrict access to authenticated STAFF (Admin/Agent) only.
    header("Location: login.html");                                  // [11] Security Redirect: Kick unauthorized visitors to the login gate.
    exit();                                                          // [12] Halt Execution: Protect agent operational UI.
}                                                                    // [13] Close security barrier.

$agent_name = $_SESSION['name'];                                     // [14] Extract operative identifier for greeting personalization.
?>                                                                   <!-- [15] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [16] Define standard HTML5 document type for modern browsers. -->
<html lang="en">                                                     <!-- [17] Root element identifying English as the layout language. -->
<head>                                                               <!-- [18] Metadata and style asset header section. -->
    <meta charset="UTF-8">                                           <!-- [19] Declare UTF-8 for international character support. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [20] Responsive scaling for mobile device compatibility. -->
    <title>Agent Dashboard - Wema Travellers</title>                    <!-- [21] Browser tab title identifier. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [22] Load shared layout grid components. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [23] Load global theme variables (Colors/Typography). -->
    <style>                                                          /* [24] Start internal CSS for agent-specific layout. */
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 20px; } /* [25] Main wrapper. */
        .back-btn-container { padding: 20px 0; max-width: 1200px; margin: 0 auto; } /* [26] navigator wrap. */
        .welcome-banner { background: linear-gradient(135deg, var(--purple), var(--pink)); color: #1e1b4b; padding: 50px 40px; border-radius: 20px; margin-bottom: 40px; text-align: center; box-shadow: 0 15px 30px rgba(0,0,0,0.1); } /* [27] greeting branding. */
        .welcome-banner h1 { margin: 0; font-size: 2.8em; font-weight: 800; } /* [28] banner title. */
        .welcome-banner p { margin: 15px 0 0; font-size: 1.3em; font-weight: 500; opacity: 0.85; } /* [29] banner text. */
        .card { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease; } /* [30] interactive card. */
        .card:hover { transform: translateY(-8px); box-shadow: 0 10px 25px rgba(0,0,0,0.12); } /* [31] hover lift. */
    </style>                                                         <!-- [32] Terminate internal CSS block. -->
</head>                                                              <!-- [33] Close head section. -->

<body>                                                               <!-- [34] Start visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [35] Inject external global navigation header. -->
    <div style="height: 100px;"></div>                                   <!-- [36] Layout offset for fixed header. -->

    <div class="dashboard-container">                                    <!-- [37] Content constraint start. -->
        <div class="back-btn-container"><a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Dashboard Hub</a></div> <!-- [38] Navigator. -->
        <div class="welcome-banner">                                     <!-- [39] Operative greeting hub. -->
            <h1>Agent Portal</h1>                                        <!-- [40] Section Title. -->
            <p>Ready for duty, <?= htmlspecialchars($agent_name) ?>. Access your control modules below.</p> <!-- [41] Personalized welcome. -->
        </div>                                                           <!-- [42] End greeting. -->

        <div class="admin-options" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;"> <!-- [43] Open the div container styled as a responsive CSS grid displaying the options. -->
            <div class="card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #eee;"><h3>Route Information</h3><p style="color: #666; margin-bottom: 20px;">Review schedules, destinations, and updated ticket costs for passengers.</p><a href="view_routes.php" class="button regular-button pink-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Open Schedule</a></div> <!-- [44] Output a structural card displaying the 'Route Information' text and presenting a hyperlink button to view_routes.php so the agent can scan route schedules. -->
            <div class="card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #eee;"><h3>Ticket Management</h3><p style="color: #666; margin-bottom: 20px;">Search passenger bookings, process refunds, or handle trip cancellations.</p><a href="view_admin_bookings.php" class="button regular-button pink-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Manage Tickets</a></div> <!-- [45] Output a structural card displaying the 'Ticket Management' option with a hyperlink button navigating to view_admin_bookings.php for handling cancellation requests. -->
            <div class="card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #eee;"><h3>Service Feedback</h3><p style="color: #666; margin-bottom: 20px;">Listen to passenger concerns and monitor service satisfaction levels.</p><a href="view_feedback.php" class="button regular-button pink-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Read Reviews</a></div> <!-- [46] Output a structural card establishing the 'Service Feedback' endpoint containing a hyperlink to view_feedback.php to review customer concerns. -->
            <div class="card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #eee;"><h3>Bus Occupancy</h3><p style="color: #666; margin-bottom: 20px;">Visual map of seat availability across all active fleet vehicles.</p><a href="view_bus_occupancy.php" class="button regular-button pink-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Check Capacity</a></div> <!-- [47] Output a structural card displaying 'Bus Occupancy' directing via hyperlink to view_bus_occupancy.php for viewing live capacity. -->
            <div class="card" style="background: #f0fdf4; padding: 30px; border-radius: 12px; border: 2px solid #22c55e;"><h3 style="color: #166534;">Instant Booking</h3><p style="color: #166534; margin-bottom: 20px;">Accelerated flow specifically designed for booking walk-in office customers.</p><a href="book.php" class="button regular-button green-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Open Booking Engine</a></div> <!-- [48] Output a uniquely highlighted green structural card displaying 'Instant Booking' linking to book.php acting as the portal for making real-time reservations for walk-ins. -->
            <div class="card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid #eee;"><h3>User Management</h3><p style="color: #666; margin-bottom: 20px;">Register new passengers or update existing traveller contact profiles.</p><a href="view_users_sorted.php" class="button regular-button pink-background" style="text-decoration:none; width: 100%; display: block; text-align: center;">Profiles Hub</a></div> <!-- [49] Output a structural card containing 'User Management' offering a link button pointing to view_users_sorted.php to access the CRM and handle user accounts. -->
        </div>                                                           <!-- [50] Close the admin options grid container element. -->
    </div>                                                               <!-- [51] Close the main dashboard container wrapper. -->

    <div style="height: 100px;"></div>                                   <!-- [52] Insert a structural spacer div with a fixed height of 100 pixels to provide visual clearance at the bottom of the dashboard grid. -->
    <script src="js/footer.js"></script>                                 <!-- [53] Inject the site-wide footer JavaScript component to render the dashboard footer area. -->
</body>                                                              <!-- [54] Close the visible body content section of the HTML document. -->
</html>                                                              <!-- [55] Formal termination and closure of the entire HTML document structure. -->
