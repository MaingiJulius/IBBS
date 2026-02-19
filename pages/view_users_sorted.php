<?php
// =================================================================
// USER MANAGEMENT PAGE (view_users_sorted.php)
// =================================================================
// This page allows Staff (Admins & Agents) to view, add, edit, or delete users.
// It lists all users sorted by their Registration ID (First joined -> Last joined).
// =================================================================

// 1. Include the database connection file to communicate with MySQL.
require_once 'db_connection.php';

// 2. Start the session to check if the current user is authorized.
session_start();

// --- SECURITY CHECK ---
// We verify that the user is logged in AND has the correct role ('ADMIN' or 'AGENT').
// If they are just a 'PASSENGER', they should not see this page.
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    die("Access Denied: Staff Only Area.");
}

// --- LOGIC: DELETING A USER ---
// If the variable 'delete_user' exists in the URL (e.g., ?delete_user=5), we run this block.
if (isset($_GET['delete_user'])) {
    // Get the ID of the user to be deleted from the URL.
    $uid = $_GET['delete_user'];
    
    // SAFETY CHECK: Prevent the Admin from deleting their own account accidentally!
    // We compare the target ID ($uid) with the logged-in ID ($_SESSION['user_id']).
    if ($uid == $_SESSION['user_id']) {
        // If they match, redirect with an error message.
        header("Location: view_users_sorted.php?err=You cannot delete your own account while logged in!");
    } else {
        // PREPARE the SQL delete statement.
        // We use '?' placeholders to prevent SQL Injection attacks.
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        // BIND the parameter: 'i' means integer.
        $stmt->bind_param("i", $uid);
        // EXECUTE the command.
        $stmt->execute();
        // CLOSE the prepared statement.
        $stmt->close();
        
        // REDIRECT back to the user list with a success message.
        header("Location: view_users_sorted.php?msg=User has been deleted successfully.");
    }
    // STOP the script here.
    exit();
}

// --- LOGIC: ADDING A NEW USER ---
// This block runs if the form with button 'add_user' is submitted via POST method.
if (isset($_POST['add_user'])) {
    // CAPTURE all inputs from the form fields.
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $role = $_POST['role'];
    
    // HASH THE PASSWORD.
    // We never store plain text passwords. 'password_hash' scrambles it securely.
    // 'PASSWORD_DEFAULT' uses the industry-standard Bcrypt algorithm.
    // This protects user data even if the database is stolen.
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // PREPARE the INSERT statement.
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    
    // BIND the 6 string parameters ("ssssss").
    $stmt->bind_param("ssssss", $first, $last, $email, $phone, $pass, $role);
    
    // EXECUTE and check if it worked.
    if($stmt->execute()) {
        $msg = "Success: New " . $role . " has been added.";
    } else {
        // If it failed (e.g., duplicate email), grab the error message.
        $msg = "Error: Could not add user. " . $conn->error;
    }
    // CLOSE statement.
    $stmt->close();
    
    // REDIRECT back to refresh the page.
    header("Location: view_users_sorted.php?msg=" . urlencode($msg));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Wema Travellers</title>
    <!-- Import CSS styles -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* --- INTERNAL CSS FOR THIS PAGE --- */
        
        /* Container for the main content box */
        .view-container {
            max-width: 1200px;    /* Limit width */
            margin: 20px auto;    /* Center horizontally */
            padding: 20px;        /* Inner spacing */
            background: white;    /* White background */
            border-radius: 8px;   /* Rounded corners */
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* Subtle shadow */
        }

        /* Container for the back button */
        .back-btn-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Styling for the user list table */
        .crud-table {
            width: 100%;          /* Full width */
            border-collapse: collapse; /* Single borders */
            margin-top: 20px;     /* Space above table */
        }

        /* Table cells and headers */
        .crud-table th, .crud-table td {
            padding: 12px;        /* Space inside cells */
            border: 1px solid #ddd; /* Light grey border */
            text-align: left;     /* Left alignment */
        }

        /* Table Header styles */
        .crud-table th {
            background-color: var(--purple); /* Brand color */
            color: white;         /* White text */
        }

        /* Button styles for actions */
        .action-btn {
            padding: 5px 10px;    /* Small button size */
            border-radius: 4px;   /* Rounded */
            text-decoration: none; /* No underline */
            color: white;         /* White text */
            font-size: 0.9em;     /* Slightly smaller text */
        }
        .btn-delete { background-color: #ff4d4d; } /* Red for delete */
        
        /* Form container style */
        .add-form {
            background: #f9f9f9;  /* Light grey bg */
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #eee;
        }

        .add-form h3 { margin-top: 0; }
        
        /* Form layout using flexbox to put inputs in a row */
        .form-row { display: flex; gap: 10px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 150px; }
    </style>
</head>
<body>
    <!-- Inject standard navigation -->
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>

    <!-- Navigation Back Button -->
    <div class="back-btn-container">
        <a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Back to Dashboard</a>
    </div>

    <!-- Main Content -->
    <div class="view-container">
        <h2>Manage Users</h2>

        <!-- Display success/error messages if present in URL -->
        <?php if(isset($_GET['msg'])): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($_GET['msg']) ?></p>
        <?php endif; ?>
        <?php if(isset($_GET['err'])): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($_GET['err']) ?></p>
        <?php endif; ?>

        <!-- SECTION: ADD NEW USER FORM -->
        <div class="add-form">
            <h3>Add New User</h3>
            <form method="POST">
                <div class="form-row">
                    <!-- Form Fields -->
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="input" placeholder="e.g. John" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="input" placeholder="e.g. Doe" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="input" placeholder="e.g. john@example.com" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" class="input" placeholder="07xxxxxxxx" required>
                    </div>
                    <div class="form-group">
                        <label>Assign Password</label>
                        <input type="password" name="password" class="input" required>
                    </div>
                    <div class="form-group">
                        <label>User Role</label>
                        <select name="role" class="input" required>
                            <option value="PASSENGER">PASSENGER</option>
                            <option value="AGENT">AGENT</option>
                            <option value="ADMIN">ADMIN</option>
                        </select>
                    </div>
                </div>
                <!-- Submit Button -->
                <button type="submit" name="add_user" class="button regular-button pink-background" style="margin-top: 15px;">Create User</button>
            </form>
        </div>

        <!-- SECTION: USER LIST TABLE -->
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Phone Number</th>
                    <th>Account Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // FETCH all users from the database.
                // We order by 'user_id ASC' to show them in registration order (Oldest first).
                $sql = "SELECT * FROM users ORDER BY user_id ASC";
                $result = $conn->query($sql);
                
                // LOOP through each user found in the result set.
                while($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <!-- Display ID -->
                    <td><?= $row['user_id'] ?></td>
                    
                    <!-- Display Name (Sanitized against XSS) -->
                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                    
                    <!-- Display Email -->
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    
                    <!-- Display Phone -->
                    <td><?= htmlspecialchars($row['phone_number']) ?></td>
                    
                    <!-- Display Role -->
                    <td><?= $row['role'] ?></td>
                    
                    <!-- Action Buttons -->
                    <td>
                        <!-- EDIT Button: Links to the Edit Page -->
                        <a href="edit_user.php?user_id=<?= $row['user_id'] ?>" class="action-btn" style="background-color: #4CAF50; margin-right: 5px;">Edit</a>
                        
                        <!-- DELETE Button: Links to self with ?delete_user param. Includes JS confirm(). -->
                        <a href="?delete_user=<?= $row['user_id'] ?>" class="action-btn btn-delete" onclick="return confirm('WARNING: Are you sure you want to delete this user? This cannot be undone.')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer Space -->
    <div style="height: 100px;"></div>
    <!-- Inject Footer -->
    <script src="js/footer.js"></script>
</body>
</html>
