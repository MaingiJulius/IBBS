<?php
/**
 * VIEW_FEEDBACK.PHP
 * Purpose: This page allows Staff (Admins and Agents) to read what passengers think of our service.
 * It displays ratings, emojis, and comments submitted by users after their trips.
 * Accessible to: ADMIN and AGENT roles.
 */

// --- BOOTSTRAP: CORE SYSTEM FILES ---
// Include the standard database connection script to enable MySQL interactions.
require_once 'db_connection.php';

// Start the PHP session to access the current user's login status and role.
session_start();

// --- SECURITY CHECK (RBAC - Role Based Access Control) ---
// We only allow users with the 'ADMIN' or 'AGENT' role to view this internal report.
// If the role key is not found in the session, or if the role is 'USER', we block access.
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'ADMIN' && $_SESSION['role'] != 'AGENT')) {
    // Stop execution and show a generic security error.
    die("Access Denied: Staff Only Area.");
}

// --- LOGIC: MODERATE/DELETE FEEDBACK ---
// This part runs if the "Remove" link is clicked. The link passes a 'delete_feedback' ID in the URL.
if (isset($_GET['delete_feedback'])) {
    // Capture the specific feedback ID from the GET parameters.
    $fid = $_GET['delete_feedback']; 

    // SECURE DELETE: Use a Prepared Statement to prevent SQL Injection when deleting records.
    $stmt = $conn->prepare("DELETE FROM feedback WHERE feedback_id = ?");
    
    // Bind the $fid variable to the '?' placeholder as an integer ("i").
    $stmt->bind_param("i", $fid);
    
    // Run the deletion query on the server.
    $stmt->execute();
    
    // Close the statement to free up database resources.
    $stmt->close();
    
    // Redirect the browser back to the same page (view_feedback.php) with a URL message 'msg'.
    // This prevents accidental re-deletion on page refresh.
    header("Location: view_feedback.php?msg=Feedback review has been removed.");
    exit(); // Always exit after a header redirect.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- DOCUMENT META INFORMATION -->
    <meta charset="UTF-8">
    <title>User Feedback - Wema Travellers</title>
    
    <!-- CORE STYLESHEETS -->
    <link rel="stylesheet" href="css/main.css">  <!-- General project layout -->
    <link rel="stylesheet" href="css/style.css"> <!-- Color and spacing variables -->
    
    <style>
        /* CSS styling for the feedback list view (Local Overrides for specialized layout) */
        .view-container {
            max-width: 1200px; /* Limits the table width for readability on large screens */
            margin: 20px auto; /* Perfectly centers the container horizontally */
            padding: 20px;
            background: white; /* Clean white card look */
            border-radius: 8px; /* Smooth rounded corners */
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* Subtle shadow for depth */
        }

        /* Container for the 'Back to Dashboard' button */
        .back-btn-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Styling for the main data table */
        .crud-table {
            width: 100%;
            border-collapse: collapse; /* Merges borders for a modern 'one-line' look */
            margin-top: 20px;
        }

        /* Cell padding and border color for the table */
        .crud-table th, .crud-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        /* Table header styling using the project's brand purple */
        .crud-table th {
            background-color: var(--purple);
            color: white; /* White text for contrast on purple background */
        }

        /* Base styles for action buttons (like Delete) */
        .action-btn {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 0.9em;
        }
        
        /* Vibrant Red background to indicate a destructive 'Delete' action */
        .btn-delete { background-color: #ff4d4d; }
    </style>
</head>
<body>
    <!-- HEADER COMPONENT -->
    <!-- Inject the dashboard navigation bar (Home, Profile, logout, etc.) using communal JS -->
    <script src="js/header2.js"></script>
    
    <!-- SPACER: Prevents the header from hiding the top of our content -->
    <div style="height: 100px;"></div>

    <!-- BACK ACTION BUTTON -->
    <div class="back-btn-container">
        <!-- Dashboard back link allows staff to return to their main panel instantly -->
        <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Back to Dashboard</a>
    </div>

    <!-- MAIN DATA VIEW -->
    <div class="view-container">
        <h2>Customer Feedback Overview</h2>

        <!-- NOTIFICATION ALERT -->
        <!-- Logic: If a 'msg' exists in the URL, display it in bold green text -->
        <?php if(isset($_GET['msg'])): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($_GET['msg']) ?></p>
        <?php endif; ?>

        <!-- DATA TABLE: List of all customer ratings and comments -->
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Date Received</th>
                    <th>Passenger Name</th>
                    <th>Rating</th>
                    <th>Customer Comments</th>
                    <th>Travel Route</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                /* 
                   SQL QUERY LOGIC:
                   - Select everything (* ) from feedback table (f).
                   - JOIN with users (u) on user_id to translate IDs into actual names.
                   - JOIN with routes (r) on route_id to show WHERE the trip was from/to.
                   - ORDER BY f.feedback_date DESC ensures the newest feedback is at the TOP.
                */
                $sql = "SELECT f.*, u.first_name, u.last_name, r.from_location, r.to_location 
                        FROM feedback f
                        JOIN users u ON f.user_id = u.user_id
                        JOIN routes r ON f.route_id = r.route_id
                        ORDER BY f.feedback_date DESC";
                
                // Execute the query via the MySQL object.
                $result = $conn->query($sql);
                
                // DATA LOOP: fetch_assoc() grabs one database row at a time until no more are left.
                while($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <!-- CELL 1: The date the feedback was submitted -->
                    <td><?= $row['feedback_date'] ?></td>
                    
                    <!-- CELL 2: The full name of the passenger (Concatenated first + last) -->
                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                    
                    <!-- CELL 3: Rating Visualization.
                         Uses str_repeat to loop a Star symbol (★) based on the database rating number (1-5).
                    -->
                    <td style="color: #ffcc00; font-weight:bold;"><?= str_repeat("★", $row['rating']) ?></td>
                    
                    <!-- CELL 4: The text comments written by the user.
                         htmlspecialchars is used to prevent any malicious script injection (XSS).
                    -->
                    <td><?= htmlspecialchars($row['comments']) ?></td>
                    
                    <!-- CELL 5: Descriptive travel route (e.g. "Nairobi to Mombasa") -->
                    <td><?= htmlspecialchars($row['from_location'] . ' to ' . $row['to_location']) ?></td>
                    
                    <!-- CELL 6: Management Operations -->
                    <td>
                        <!-- DELETE ACTION: 
                             - Passes the specific feedback_id back to the PHP block at the top via GET.
                             - onclick: Triggers a browser pop-up to confirm the deletion.
                        -->
                        <a href="?delete_feedback=<?= $row['feedback_id'] ?>" class="action-btn btn-delete" onclick="return confirm('Do you want to PERMANENTLY remove this feedback?')">Remove</a>
                    </td>
                </tr>
                <?php endwhile; // End of the while loop ?>
            </tbody>
        </table>
    </div>

    <!-- BOTTOM UI PADDING: Ensure some scrolling space at the end of the page -->
    <div style="height: 100px;"></div>
    
    <!-- FOOTER COMPONENT -->
    <!-- Inject the site-wide global footer script -->
    <script src="js/footer.js"></script>
</body>
</html>
