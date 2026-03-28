<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * ADMINISTRATION: STRATEGIC INSIGHTS HUB (admin_insights.php)       // [4] Title identifying this script as the business intelligence gateway.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This acts as the centralized gateway for all analytical   // [6] Main objective: provide an interface for fiscal reporting.
 * reports, allowing Admins to pivot business data by time ranges.  // [7] Task: bridge for time-bound performance audits.
 * UI Design: Modern Grid Dashboard with Interactive Card Elements.  // [8] Design aesthetic: clean data-driven grid.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

session_start();                                                    // [11] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {      // [12] Access Control: Verify if requester possesses the 'ADMIN' credential.
    header("Location: login.html");                                  // [13] Security Redirect: Kick unauthorized visitors to the login gate.
    exit();                                                          // [14] Halt Execution: Ensure no business metrics are leaked to guests.
}                                                                    // [15] Close security barrier.
?>                                                                   <!-- [16] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [17] Define standard HTML5 document type for modern browsers. -->
<html lang="en">                                                     <!-- [18] Root element identifying English as layout language. -->
<head>                                                               <!-- [19] Metadata and style asset header section. -->
    <meta charset="UTF-8">                                           <!-- [20] Declare UTF-8 for international character support. -->
    <title>Business Intel: Performance Analytics - Wema Travellers</title> <!-- [21] Browser tab title identifier. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [22] Responsive scaling for mobile device compatibility. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [23] Load global theme variables (Colors/Typography). -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [24] Load shared layout grid components. -->
    <style>                                                          /* [25] Start internal CSS for insights dashboard layout. */
        body { font-family: 'Inter', 'Segoe UI', Roboto, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; } /* [26] base body. */
        .container { width: 95%; max-width: 1100px; margin: 60px auto; background: #ffffff; padding: 50px; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.04); text-align: center; } /* [27] main hud. */
        .back-btn-container { margin-bottom: 30px; text-align: left; } /* [28] navigator layout. */
        .container h1 { color: var(--purple); margin-bottom: 5px; font-size: 2.8rem; font-weight: 800; } /* [29] hub title. */
        p.subtitle { color: #64748b; margin-bottom: 50px; font-size: 1.15rem; max-width: 600px; margin-left: auto; margin-right: auto; } /* [30] hub text. */
        .report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; } /* [31] grid engine. */
        .report-link { display: flex; flex-direction: column; justify-content: center; height: 140px; background: #ffffff; border: 2px solid #f1f5f9; border-radius: 12px; color: #1e293b; text-decoration: none; font-weight: 700; font-size: 1rem; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-sizing: border-box; box-shadow: 0 2px 5px rgba(0,0,0,0.02); } /* [32] link card. */
        .report-link:hover { background: var(--purple); color: #ffffff; border-color: var(--purple); transform: translateY(-8px); box-shadow: 0 10px 25px rgba(106, 17, 203, 0.15); } /* [33] hover interaction. */
    </style>                                                         <!-- [34] Terminate internal CSS block. -->
</head>                                                              <!-- [35] Close head section. -->

<body>                                                               <!-- [36] Start visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [37] Inject external global navigation header. -->
    <div style="height: 100px;"></div>                                   <!-- [38] Layout offset for fixed header. -->

    <div class="container">                                              <!-- [39] Content constraint start. -->
        <div class="back-btn-container"><a href="admin_dashboard.php" class="button regular-button" style="text-decoration:none; background-color: var(--purple); color: white; border-radius: 50px; display: inline-block; width: auto; padding: 12px 35px; font-weight: 700;">← Return to Admin Panel</a></div> <!-- [40] Navigator. -->
        <h1>Data Insights Hub</h1>                                       <!-- [41] Hub Title. -->
        <p class="subtitle">Access comprehensive business intelligence reports and time-filtered booking analytics.</p> <!-- [42] Hub Text. -->

        <div class="report-grid">                                        <!-- [43] Start report listing grid. -->
            <a href="report_this_week.php" class="report-link">📊 Weekly Performance Report</a> <!-- [44] Weekly. -->
            <a href="report_last_week.php" class="report-link">⏮️ Last Week's Audit</a>          <!-- [45] Last Week. -->
            <a href="report_this_month.php" class="report-link">📅 Monthly Sales Review</a>      <!-- [46] Monthly. -->
            <a href="report_last_month.php" class="report-link">🕒 Previous Month Audit</a>     <!-- [47] Last Month. -->
            <a href="report_this_year.php" class="report-link">💰 Annual Growth Report</a>       <!-- [48] Yearly. -->
        </div>                                                           <!-- [49] End grid. -->
    </div>                                                               <!-- [50] End container. -->

    <div style="height: 120px;"></div>                                   <!-- [51] buffer. -->
    <script src="js/footer.js"></script>                                 <!-- [52] inject footer. -->
</body>                                                              <!-- [53] end body. -->
</html>                                                              <!-- [54] end document. -->
