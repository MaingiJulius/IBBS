<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ADMIN DASHBOARD (admin_dashboard.php)                             // [3] Title identifying this script as the master administrative hub.
 * Purpose: This page serves as the "Master Control Center" for Wema Travellers Administrators. // [4] Core objective: centralized oversight.
 * It provides a visual hub with links to all system management modules. // [5] UI Role: navigation gateway for platform operations.
 * Accessible only to: Users with the 'ADMIN' role.                  // [6] Security scope: restricted to highest clearance level.
 */                                                                  // [7] Close multi-line documentation block.

session_start();                                                    // [8] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {      // [9] Access Control: Verify if requester possesses the 'ADMIN' credential.
    header("Location: login.html");                                  // [10] Security Redirect: Kick unauthorized visitors to the login gate.
    exit();                                                          // [11] Halt Execution: Ensure no administrative UI is leaked to guests.
}                                                                    // [12] Close security barrier.
?>                                                                   <!-- [13] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [14] Define standard HTML5 document type for modern browsers. -->
<html lang="en">                                                     <!-- [15] Root element identifying English as the layout language. -->
<head>                                                               <!-- [16] Metadata and style asset header section. -->
    <meta charset="UTF-8">                                           <!-- [17] Declare UTF-8 for international character support. -->
    <title>Master Control Panel - Wema Travellers</title>                 <!-- [18] Browser tab title identifier. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [19] Responsive scaling for mobile device compatibility. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [20] Load global theme variables (Colors/Typography). -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [21] Load shared layout grid components. -->
    <link rel="stylesheet" href="css/entry-page.css">                <!-- [22] Load utility classes for buttons and inputs. -->

    <style>                                                          /* [23] Start internal CSS for dashboard-specific layout. */
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background-color: #f7f9fc; margin: 0; padding: 0; } /* [24] Base body style. */
        .container { width: 90%; max-width: 1200px; margin: 30px auto; padding: 20px; } /* [25] Main centering wrapper. */
        .admin-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; padding: 20px 0; } /* [26] Grid layout. */
        .card { background-color: #ffffff; border: 1px solid #e1e8ed; border-radius: 12px; padding: 30px; text-align: center; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; } /* [27] card unit. */
        .card:hover { transform: translateY(-8px); box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1); border-color: var(--purple); } /* [28] hover lift. */
        .card h3 { margin-top: 0; color: var(--purple); font-size: 1.4rem; } /* [29] card title. */
        .card p { color: #5a6b7d; font-size: 0.95rem; line-height: 1.5; margin-bottom: 25px; } /* [30] card body. */
        .back-btn-container { margin-bottom: 20px; }                  /* [31] layout buffer. */
        .welcome-banner { background: var(--purple); color: #ffffff; padding: 50px 20px; border-radius: 16px; margin-bottom: 40px; text-align: center; box-shadow: 0 10px 25px rgba(154, 77, 154, 0.3); } /* [32] branding banner. */
        .welcome-banner h1 { margin: 0; font-size: 2.5rem; letter-spacing: -1px; } /* [33] banner title. */
        .welcome-banner p { font-size: 1.1rem; opacity: 0.9; margin-top: 10px; } /* [34] banner subtitle. */
    </style>                                                         <!-- [35] Terminate internal CSS block. -->
</head>                                                              <!-- [36] Close head section. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>                                <!-- [38] Inject external global navigation header. -->
    <div style="height: 100px;"></div>                                   <!-- [39] Layout offset for fixed header. -->

    <div class="container">                                              <!-- [40] Content constraint start. -->
        <div class="welcome-banner">                                     
            <h1>Administrative Operations</h1>                            
            <p>Authorized access granted to: <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></p> 
        </div>                                                           

        <div style="text-align: center; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e8ed;">
            <h3 style="color: var(--purple);">Welcome to the Control Center</h3>
            <p style="color: #5a6b7d;">All administrative tools and reports are now organized in the navigation bar above.</p>
            <p style="color: #5a6b7d;">Select a category (<strong>Manage Actors</strong>, <strong>Logistics</strong>, or <strong>Reports</strong>) to begin your operations.</p>
        </div>
    </div>                                                               <!-- [59] End container. -->

    <div style="height: 100px;"></div>                                   <!-- [60] buffer. -->
    <script src="js/footer.js"></script>                                 <!-- [61] inject footer. -->
</body>                                                              <!-- [62] end body. -->
</html>                                                              <!-- [63] end document. -->
