<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * REVIEWS & RATINGS REPORT (admin_feedback.php)                     // [3] Title identifying this script as the administrative feedback audit.
 * Purpose: This is the primary Administrative view for auditing customer Satisfaction. // [4] Main objective: maintain customer service quality standards.
 * It allows the management team to see exactly what passengers are saying about // [5] Functionality: bridge for staff to audit user sentiments.
 * specific buses and routes, including their numerical star ratings. // [6] Task: review and moderate passenger reviews and ratings.
 */                                                                  // [7] Close multi-line documentation block.

session_start();                                                    // [8] Initialize or resume user session to identify the administrative officer.
require_once 'db_connection.php';                                    // [9] Import database bridge object ($conn) for MySQL communication.

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {      // [10] Security Barrier: Verify requester identity and 'admin' credential.
    die("Security Error: Access denied. This report is restricted to authorized Administrators."); // [11] Halt execution with descriptive error.
}                                                                    // [12] Close security barrier.

$sql_report = "SELECT * FROM feedback_report_view ORDER BY feedback_date DESC"; // [13] Data Retrieval: Fetch all records from the virtual feedback view.
$res_report = $conn->query($sql_report);                             // [14] Execute the standardized report query on the database.

if (!$res_report) {                                                  // [15] Integrity Check: Verify successful dataset generation.
    die("System Error: Unable to generate feedback report. Detail: " . $conn->error); // [16] Halt if virtual view or connection fails.
}                                                                    // [17] Close integrity check.
?>                                                                   <!-- [18] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [19] Define standard HTML5 document type for modern browsers. -->
<html lang="en">                                                     <!-- [20] Root element identifying English as layout language. -->
<head>                                                               <!-- [21] Metadata and resource header section. -->
    <meta charset="UTF-8">                                           <!-- [22] Declare UTF-8 for international character support. -->
    <title>Quality Audit: Feedback Report - Wema Travellers</title>      <!-- [23] Browser tab title identifier. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [24] Load global project branding variables. -->
    <style>                                                          /* [25] Page-specific internal CSS architecture. */
        body { font-family: 'Segoe UI', system-ui, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; } /* [26] Body styling. */
        .container { width: 95%; max-width: 1200px; margin: 40px auto; padding: 30px; background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-radius: 12px; } /* [27] main card. */
        h1 { text-align: center; color: #2c3e50; margin-bottom: 35px; font-size: 1.8rem; } /* [28] page title. */
        table { width: 100%; border-collapse: collapse; text-align: left; } /* [29] data grid. */
        th, td { padding: 15px; border-bottom: 1px solid #edf2f7; }  /* [30] cell layout. */
        th { background-color: #f1f5f9; color: #4a5568; font-weight: 700; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em; } /* [31] branding. */
        tr:hover { background-color: #fcfdfe; }                      /* [32] interaction. */
        .btn { display: inline-block; margin-top: 30px; padding: 12px 25px; background-color: #4CAF50; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s; } /* [33] cmd btn. */
        .btn:hover { background-color: #388e3c; }                    /* [34] interaction. */
        .center-button { text-align: center; }                       /* [35] button wrapper. */
        @media screen and (max-width: 900px) { table { font-size: 0.8rem; } .container { padding: 15px; } } /* [36] responsive tweaks. */
    </style>                                                         <!-- [37] Terminate internal CSS block. -->
</head>                                                              <!-- [38] Close head section. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">                                                               <!-- [39] Start visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [40] Inject the unified administrative header. -->
    <div class="container">                                              <!-- [41] Open the audit card. -->
        <h1>Customer Satisfaction Report</h1>                            <!-- [42] Section title. -->
        <table>                                                          <!-- [43] Start data grid. -->
            <thead><tr><th>ID</th><th>Passenger</th><th>Bus ID</th><th>Vehicle Name</th><th>Score (1-5)</th><th>Passenger Comments</th><th>Date Submitted</th></tr></thead> <!-- [44] head labels. -->
            <tbody>                                                      <!-- [45] Records start. -->
                <?php if ($res_report && $res_report->num_rows > 0): while ($fb_row = $res_report->fetch_assoc()): ?> <!-- [46] Loop through virtual feedback view records. -->
                <tr><td style="color: #888;"><?= $fb_row['feedback_id'] ?></td> <!-- [47] identifier. -->
                    <td style="font-weight: 600;"><?= htmlspecialchars($fb_row['user_name']) ?></td> <!-- [48] identity. -->
                    <td style="font-family: monospace;"><?= $fb_row['bus_id'] !== null ? htmlspecialchars($fb_row['bus_id']) : '<em>N/A</em>' ?></td> <!-- [49] vehicle link. -->
                    <td><?= $fb_row['bus_name'] !== null ? htmlspecialchars($fb_row['bus_name']) : '<span style="color: #bbb;">No Bus Linked</span>' ?></td> <!-- [50] vehicle branding. -->
                    <td style="font-weight: bold; color: <?= $fb_row['rating'] >= 4 ? '#27ae60' : '#e67e22' ?>;"><?= $fb_row['rating'] ?> / 5</td> <!-- [51] score weighting. -->
                    <td style="max-width: 300px; font-style: italic; color: #444;">"<?= htmlspecialchars($fb_row['comments']) ?>"</td> <!-- [52] verbatim comment. -->
                    <td style="font-size: 0.85rem; color: #718096;"><?= $fb_row['feedback_date'] ?></td> <!-- [53] timestamp. -->
                </tr>                                                    <!-- [54] end row. -->
                <?php endwhile; else: ?>                                 <!-- [55] end loop. -->
                <tr><td colspan="7" style="text-align: center; padding: 50px; color: #999;">The feedback registry is currently empty. No reviews have been submitted yet.</td></tr> <!-- [56] empty state. -->
                <?php endif; ?>                                          <!-- [57] end record set check. -->
            </tbody>                                                     <!-- [58] end grid body. -->
        </table>                                                         <!-- [59] end grid. -->
        <div class="center-button"><a href="dashboard.php" class="btn">← Back to Dashboard Hub</a></div> <!-- [60] return cmd. -->
    </div>                                                               <!-- [61] end card enclosure. -->
    <script src="js/footer.js"></script>                                 <!-- [62] inject footer. -->
</body>                                                              <!-- [63] end body. -->
</html>                                                              <!-- [64] end document. -->
