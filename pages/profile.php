<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual header for administrative documentation clarity.
 * USER PROFILE SYSTEM: ACCOUNT IDENTITY (profile.php)               // [4] Title identifying this script as the personal identity record.
 * ================================================================= // [5] Visual header for administrative documentation clarity.
 * Purpose: This script provides passengers and staff with a secure  // [6] Main objective: provide an interface for PII review.
 * portal to view their personal identity records and contact details. // [7] Functionality: bridge for identity transparency.
 * Features: Secure session lookup, multi-field identity display.      // [8] Technical scope: session-driven discovery.
 * ================================================================= // [9] Visual header for administrative documentation clarity.
 */                                                                  // [10] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [11] Import database bridge object ($conn) for MySQL communication.
session_start();                                                    // [12] Initialize or resume user session to identify the visitor.

if (!isset($_SESSION['user_id'])) {                                  // [13] Security Check: Intercept anonymous visitors without a valid token.
    header("Location: login.html");                                  // [14] Security Redirect: Kick guest back to the login portal.
    exit();                                                          // [15] Halt Execution: Ensure no PII data is leaked to guests.
}                                                                    // [16] Close security barrier.

$user_id = $_SESSION['user_id'];                                     // [17] Map the active session's primary user ID to a local variable.
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");     // [18] Prepare a secure SQL template with protective '?' placeholder.
$stmt->bind_param("i", $user_id);                                    // [19] Safely inject the session-provided integer ID into the statement.
$stmt->execute();                                                    // [20] Commit the data retrieval request to the MySQL engine.
$user_data = $stmt->get_result()->fetch_assoc();                     // [21] Capture the resulting record as an associative array.
$stmt->close();                                                      // [22] Release statement resource memory from the server.
?>                                                                   <!-- [23] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [24] Define standard HTML5 document type. -->
<html lang="en">                                                     <!-- [25] Root element identifying English as layout language. -->
<head>                                                               <!-- [26] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [27] Declare UTF-8 for international character support. -->
    <title>Personal Identity - Wema Travellers</title>                 <!-- [28] Website title for browser selection. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- [29] Responsive scaling for mobile devices. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [30] Load shared component style assets. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [31] Load global project branding variables. -->
    <style>                                                          /* [32] Page-specific internal CSS architecture. */
        .profile-container { max-width: 700px; margin: 60px auto; background: #ffffff; padding: 50px; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.06); border-top: 8px solid var(--purple); } /* [33] Main card. */
        .profile-header { text-align: center; border-bottom: 2px solid #f8fafc; padding-bottom: 30px; margin-bottom: 40px; } /* [34] header. */
        .info-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 25px; margin-bottom: 40px; } /* [35] data grid. */
        .info-label { font-weight: 800; color: var(--purple); text-transform: uppercase; font-size: 0.8rem; display: flex; align-items: center; } /* [36] labels. */
        .info-value { color: #1e293b; font-size: 1.1rem; word-break: break-all; } /* [37] values. */
        .role-badge { display: inline-block; padding: 8px 18px; border-radius: 50px; background: var(--pink); color: #1e1b4b; font-size: 0.75rem; font-weight: 900; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 15px; } /* [38] badge. */
        .back-link { display: block; text-align: center; margin-top: 30px; color: var(--purple); text-decoration: none; font-weight: 800; font-size: 1rem; } /* [39] cmd. */
        .back-link:hover { text-decoration: underline; }             /* [40] interaction. */
    </style>                                                         <!-- [41] Terminate internal CSS block. -->
</head>                                                              <!-- [42] Close head section. -->

<body>                                                               <!-- [43] Start visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [44] Inject the unified sitewide navigation header. -->
    <div style="height: 100px;"></div>                                   <!-- [45] Fixed header offset buffer. -->

    <div class="profile-container">                                      <!-- [46] Open identity dashboard card. -->
        <div class="profile-header">                                     <!-- [47] Header layer. -->
            <h2 style="color: var(--purple); margin: 0;">👤 Profile Insights</h2> <!-- [48] Title. -->
            <div class="role-badge"><?= $_SESSION['role'] ?> AUTHORIZED</div> <!-- [49] Role status pill. -->
        </div>                                                           <!-- [50] End header. -->

        <div class="info-grid">                                          <!-- [51] Start PII data matrix. -->
            <div class="info-label">Given Name:</div><div class="info-value"><?= htmlspecialchars($user_data['first_name']) ?></div> <!-- [52] First Name. -->
            <div class="info-label">Surname:</div><div class="info-value"><?= htmlspecialchars($user_data['last_name']) ?></div> <!-- [53] Surname. -->
            <div class="info-label">Email Record:</div><div class="info-value"><?= htmlspecialchars($user_data['email']) ?></div> <!-- [54] Contact. -->
            <div class="info-label">Phone Contact:</div><div class="info-value" style="font-family: monospace; letter-spacing: 1px;"><?= htmlspecialchars($user_data['phone_number']) ?></div> <!-- [55] Mobile. -->
            <div class="info-label">Index ID:</div><div class="info-value" style="color: #94a3b8; font-family: monospace;"><?= $user_data['user_id'] ?></div> <!-- [56] System Index. -->
        </div>                                                           <!-- [57] End data matrix. -->

        <a href="dashboard.php" class="back-link">← Return to Dashboard Hub</a> <!-- [58] Navigation exit command. -->
    </div>                                                               <!-- [59] End container. -->

    <div style="height: 120px;"></div>                                   <!-- [60] bottom spacer. -->
    <script src="js/footer.js"></script>                                 <!-- [61] inject footer. -->
</body>                                                              <!-- [62] end body. -->
</html>                                                              <!-- [63] end document. -->
