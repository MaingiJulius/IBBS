<?php
/**
 * REVIEWS & RATINGS REPORT (admin_feedback.php)
 * Purpose: This is the primary Administrative view for auditing customer Satisfaction.
 * It allows the management team to see exactly what passengers are saying about 
 * specific buses and routes, including their numerical star ratings.
 */

// --- STEP 1: SESSION VERIFICATION ---
// session_start() gives us access to $_SESSION to check who is logged in.
session_start();

// Include the database bridge for SQL queries.
require_once 'db_connection.php';

/**
 * --- STEP 2: SECURITY ACCESS CONTROL ---
 * We only allow users with the 'admin' permission level to view this report.
 * If the user isn't logged in OR their role isn't 'admin', we terminate immediately.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Security Error: Access denied. This report is restricted to authorized Administrators.");
}

/**
 * --- STEP 3: DATA RETRIEVAL ---
 * We fetch all records from the 'feedback_report_view'.
 * 
 * NOTE ON ARCHITECTURE:
 * 'feedback_report_view' is a Virtual Table (View) in MySQL.
 * It automatically joins 'feedback', 'users', 'trips', and 'buses' tables 
 * so we don't have to write a complex 4-table JOIN query here in PHP.
 */
$sql_report = "SELECT * FROM feedback_report_view ORDER BY feedback_date DESC";
$res_report = $conn->query($sql_report);

// Handle cases where the database query might fail (e.g., database is down).
if (!$res_report) {
    die("System Error: Unable to generate feedback report. Detail: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quality Audit: Feedback Report - Wema Travellers</title>
    
    <!-- site theme -->
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* DASHBOARD DESIGN: Specific styles for the feedback table */
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: #f8f9fa; /* Light grey backdrop */
            margin: 0;
            padding: 0;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border-radius: 12px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 35px;
            font-size: 1.8rem;
        }

        /* Table UI: Clean and professional data display */
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left; /* Left align for better readability of comments */
        }

        th, td {
            padding: 15px;
            border-bottom: 1px solid #edf2f7;
        }

        /* Header: Shade of grey to distinguish from data */
        th {
            background-color: #f1f5f9;
            color: #4a5568;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }

        /* Hover: Highlight the row the Admin is looking at */
        tr:hover {
            background-color: #fcfdfe;
        }

        /* "Back" button styling */
        .btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 25px;
            background-color: #4CAF50; /* Green */
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn:hover {
            background-color: #388e3c;
        }

        .center-button {
            text-align: center;
        }

        /* Mobile Responsive adjustment */
        @media screen and (max-width: 900px) {
            table { font-size: 0.8rem; }
            .container { padding: 15px; }
        }
    </style>
</head>
<body>

<!-- Load Site Navbar -->
<script src="js/header2.js"></script>

<div class="container">
    <h1>Customer Satisfaction Report</h1>

    <!-- DATA TABLE -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Passenger</th>
                <th>Bus ID</th>
                <th>Vehicle Name</th>
                <th>Score (1-5)</th>
                <th>Passenger Comments</th>
                <th>Date Submitted</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            /**
             * RENDER LOOP: 
             * We check if the database returned any rows. 
             * If yes, we loop through and print them.
             */
            if ($res_report && $res_report->num_rows > 0): ?>
                <?php while ($fb_row = $res_report->fetch_assoc()): ?>
                    <tr>
                        <!-- Entry unique ID -->
                        <td style="color: #888;">#<?= $fb_row['feedback_id'] ?></td>
                        
                        <!-- Name: Sanitized to prevent XSS (cross-site scripting) -->
                        <td style="font-weight: 600;"><?= htmlspecialchars($fb_row['user_name']) ?></td>
                        
                        <!-- Bus Details: Fetching N/A if data is missing -->
                        <td style="font-family: monospace;"><?= $fb_row['bus_id'] !== null ? htmlspecialchars($fb_row['bus_id']) : '<em>N/A</em>' ?></td>
                        <td><?= $fb_row['bus_name'] !== null ? htmlspecialchars($fb_row['bus_name']) : '<span style="color: #bbb;">No Bus Linked</span>' ?></td>
                        
                        <!-- Rating: Displays the 1-5 numerical value -->
                        <td style="font-weight: bold; color: <?= $fb_row['rating'] >= 4 ? '#27ae60' : '#e67e22' ?>;">
                            <?= $fb_row['rating'] ?> / 5
                        </td>
                        
                        <!-- Comment: The actual text review -->
                        <td style="max-width: 300px; font-style: italic; color: #444;">
                            "<?= htmlspecialchars($fb_row['comments']) ?>"
                        </td>
                        
                        <!-- Timestamp -->
                        <td style="font-size: 0.85rem; color: #718096;"><?= $fb_row['feedback_date'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- EMPTY STATE: If no one has left feedback yet -->
                <tr>
                    <td colspan="7" style="text-align: center; padding: 50px; color: #999;">
                        The feedback registry is currently empty. No reviews have been submitted yet.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- NAVIGATION: Return to the Admin Hub -->
    <div class="center-button">
        <a href="dashboard.php" class="btn">← Back to Dashboard Hub</a>
    </div>
</div>

<!-- Load Site Footer -->
<script src="js/footer.js"></script>

</body>
</html>
