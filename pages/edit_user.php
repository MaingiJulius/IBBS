<?php                                                                // [1] Open PHP script tag to start server-side logical execution.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * USER EDIT INTERFACE (edit_user.php)                               // [3] Title identifying this script as the user modification portal.
 * Purpose: This page allows Staff (Admins and Agents) to modify existing system profiles. // [4] Main objective: staff-led account modification.
 * It handles profile updates, role elevation/demotion, and password resets. // [5] Functionality: bridge for identity/permission updates.
 */                                                                  // [6] Close multi-line documentation block.

require_once 'db_connection.php';                                    // [7] Import database bridge object ($conn) for MySQL communication.
require_once 'logger.php';                                           // [7.5] Import the logging utility for auditing operations.
session_start();                                                    // [8] Initialize or resume user session to identify the administrative officer.

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) { // [9] Security Barrier: Verify requester identity and staff role.
    die("Access Denied: Staff Only Area.");                          // [10] Halt execution with descriptive error if unauthorized.
}                                                                    // [11] Close security barrier.

if (!isset($_GET['user_id'])) {                                      // [12] Parameter Check: Verify presence of target user identifier in URL.
    die("Error: No user ID specified for modification.");            // [13] Halt execution if no target user is identified.
}                                                                    // [14] Close parameter check.

$target_id = $_GET['user_id'];                                       // [15] Map the URL-provided user ID to a local variable.
$err = "";                                                           // [16] Initialize error string for database feedback display.
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");     // [17] Prepare secure SQL search template with defensive placeholder.
$stmt->bind_param("i", $target_id);                                  // [18] Safely inject the ID variable into the prepared statement.
$stmt->execute();                                                    // [19] Execute data retrieval command on the MySQL server.
$result = $stmt->get_result();                                       // [20] Capture the resulting database outcome.
$user = $result->fetch_assoc();                                      // [21] Associative array mapping for record manipulation.
$stmt->close();                                                      // [22] Release statement resource memory.

if (!$user) {                                                        // [23] Integrity Check: Verify if the ID corresponds to an actual record.
    die("Error: The requested user profile was not found in our records."); // [24] Halt if target ID is invalid or missing.
}                                                                    // [25] Close integrity check.

if (isset($_POST['update_user'])) {                                  // [26] Action Handler: Intercept form submission for profile commit.
    $fname = $_POST['first_name'];                                   // [27] Capture: Edited first name from form input.
    $lname = $_POST['last_name'];                                    // [28] Capture: Edited last name from form input.
    $email = $_POST['email'];                                        // [29] Capture: Updated digital contact address.
    $phone = $_POST['phone_number'];                                 // [30] Capture: Updated mobile contact record.
    $role = $_POST['role'];                                          // [31] Capture: New permission level/role assignment.
    $new_pass = $_POST['password'];                                  // [32] Capture: Optional new password string.

    if (!empty($new_pass)) {                                         // [33] Logic Branch: Detect if a password change was requested.
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);        // [34] Cryptographic Hashing: Create secure one-way password hash.
        $sql = "UPDATE users SET first_name=?, last_name=?, email=?, phone_number=?, role=?, password=? WHERE user_id=?"; // [35] SQL: Update with password change.
        $stmt_upd = $conn->prepare($sql);                            // [36] Prepare secure update statement.
        $stmt_upd->bind_param("ssssssi", $fname, $lname, $email, $phone, $role, $hashed, $target_id); // [37] Bind 7 parameters.
    } else {                                                         // [38] Logic Branch: Update profile without password modification.
        $sql = "UPDATE users SET first_name=?, last_name=?, email=?, phone_number=?, role=? WHERE user_id=?"; // [39] SQL: Update sans password.
        $stmt_upd = $conn->prepare($sql);                            // [40] Prepare secure update statement.
        $stmt_upd->bind_param("sssssi", $fname, $lname, $email, $phone, $role, $target_id); // [41] Bind 6 parameters.
    }                                                                // [42] End logic branch.

    if ($stmt_upd->execute()) {                                      // [43] Attempt to commit the modified record to the database.
        // [AUDIT LOG] Record the modification.
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Modified User Profile (UID: $target_id)");
        header("Location: view_users_sorted.php?msg=System: User profile updated successfully."); // [44] Case: Success. Redirect to list.
        exit();                                                      // [45] Halt further execution.
    } else { $err = "Critical Database Failure: " . $conn->error; } // [46] Case: Failure. Log the technical error.
}                                                                    // [47] End update handler.
?>                                                                   <!-- [48] Close PHP script and prepare for document definition. -->

<!DOCTYPE html>                                                         <!-- [49] Define standard HTML5 document type for modern browsers. -->
<html lang="en">                                                     <!-- [50] Root element identifying English as layout language. -->
<head>                                                               <!-- [51] Metadata and style asset header section. -->
    <meta charset="UTF-8">                                           <!-- [52] Declare UTF-8 for international character support. -->
    <title>Edit User - Wema Travellers</title>                          <!-- [53] Browser tab title identifier. -->
    <link rel="stylesheet" href="css/main.css">                      <!-- [54] Load shared component style assets. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [55] Load global branding layout variables. -->
    <style>                                                          /* [56] Start internal CSS for user modification layout. */
        .form-container { max-width: 600px; margin: 40px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); } /* [57] Card container. */
        .form-group { margin-bottom: 20px; }                         /* [58] Vertical field spacing. */
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; } /* [59] Bold field labels. */
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; } /* [60] Input styling. */
    </style>                                                         <!-- [61] Terminate internal CSS block. -->
</head>                                                              <!-- [62] Close head section. -->

<body class="<?= strtolower($_SESSION['role']) ?>-role">                                                               <!-- [63] Start visible document body. -->
    <script src="js/header2.js"></script>                                <!-- [64] Inject the unified sitewide navigation header. -->
    <div style="height: 100px;"></div>                                   <!-- [65] Fixed header offset buffer. -->

    <div class="form-container">                                         <!-- [66] Open the modification card. -->
        <h2 style="color: var(--purple); text-align: center;">Edit User Details</h2> <!-- [67] Header. -->
        <?php if($err): ?><p style="color: red; text-align: center;"><?= $err ?></p><?php endif; ?> <!-- [68] Conditional error display. -->

        <form method="POST" id="editUserForm" onsubmit="return validateForm()">                                             <!-- [69] Start update form definition. -->
            <div class="form-group"><label>First Name</label><input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" onmouseout="validateFirstName()"></div> <!-- [70] Pre-filled first name. -->
            <div class="form-group"><label>Last Name</label><input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" onmouseout="validateLastName()"></div> <!-- [71] Pre-filled last name. -->
            <div class="form-group"><label>Email Address</label><input type="text" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" onmouseout="validateEmail()"></div> <!-- [72] Pre-filled email. -->
            <div class="form-group"><label>Phone Number</label><input type="text" name="phone_number" id="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" onmouseout="validatePhoneNumber()"></div> <!-- [73] Pre-filled phone. -->
            <div class="form-group"><label>Role</label><select name="role" id="role" onmouseout="validateRole()"> <!-- [74] Role state selection logic. -->
                <option value="PASSENGER" <?= ($user['role'] == 'PASSENGER') ? 'selected' : '' ?>>PASSENGER</option> <!-- [75] Choice: PASSENGER. -->
                <option value="AGENT" <?= ($user['role'] == 'AGENT') ? 'selected' : '' ?>>AGENT</option> <!-- [76] Choice: AGENT. -->
                <option value="ADMIN" <?= ($user['role'] == 'ADMIN') ? 'selected' : '' ?>>ADMIN</option> <!-- [77] Choice: ADMIN. -->
            </select></div>                                              <!-- [78] End role selection. -->
            <div class="form-group"><label>New Password (Leave blank to keep current)</label><input type="password" name="password" id="password" placeholder="Enter new password only if changing" onmouseout="validatePassword()"></div> <!-- [79] Optional password field. -->
            <div style="display: flex; gap: 10px;"><button type="submit" name="update_user" class="button regular-button pink-background" style="flex: 1;">Update User</button><a href="view_users_sorted.php" class="button regular-button" style="background: #999; text-decoration: none; text-align: center;">Cancel</a></div> <!-- [80] Action controls. -->
        </form>                                                          <!-- [81] End form. -->

        <script>
            // Custom JavaScript validation for Admin Edit User Form
            function validateFirstName() {
                var val = document.getElementById("first_name").value.trim();
                if (val.length == 0) {
                    alert("First Name cannot be empty.");
                    document.getElementById("first_name").focus();
                    return false;
                }
                return true;
            }

            function validateLastName() {
                var val = document.getElementById("last_name").value.trim();
                if (val.length == 0) {
                    alert("Last Name cannot be empty.");
                    document.getElementById("last_name").focus();
                    return false;
                }
                return true;
            }

            function validateEmail() {
                var email = document.getElementById("email").value.trim();
                if (email.length == 0 || email.indexOf("@") == -1 || email.indexOf(".") == -1) {
                    alert("Please enter a valid email address.");
                    document.getElementById("email").focus();
                    return false;
                }
                return true;
            }

            function validatePhoneNumber() {
                var phone = document.getElementById("phone_number").value.trim();
                if (phone.length < 9 || isNaN(phone)) {
                    alert("Please enter a valid phone number.");
                    document.getElementById("phone_number").focus();
                    return false;
                }
                return true;
            }

            function validateRole() {
                var role = document.getElementById("role").value;
                if (role == "") {
                    alert("Please select a user role.");
                    return false;
                }
                return true;
            }

            function validatePassword() {
                var password = document.getElementById("password").value;
                if (password.length > 0) {
                    // If changing, enforce strength
                    var strengthRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                    if (!strengthRegex.test(password)) {
                        alert("The new password must be at least 8 characters long and include uppercase, lowercase, a number, and a special character (@$!%*?&)");
                        document.getElementById("password").focus();
                        return false;
                    }
                }
                return true;
            }

            function validateForm() {
                if (!validateFirstName()) return false;
                if (!validateLastName()) return false;
                if (!validateEmail()) return false;
                if (!validatePhoneNumber()) return false;
                if (!validateRole()) return false;
                if (!validatePassword()) return false;
                return true;
            }
        </script>

    </div>                                                               <!-- [82] End modification card. -->

    <div style="height: 100px;"></div><script src="js/footer.js"></script> <!-- [83] Screen spacing and footer injection. -->
</body>                                                              <!-- [84] End body. -->
</html>                                                              <!-- [85] End document termination. -->
