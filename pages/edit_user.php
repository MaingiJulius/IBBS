<?php
require_once 'db_connection.php';
require_once 'logger.php';
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    die("Access Denied: Staff Only Area.");
}
if (!isset($_GET['user_id'])) {
    die("Error: No user ID specified for modification.");
}
$target_id = $_GET['user_id'];
$err = "";
$result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $target_id");
$user = mysqli_fetch_assoc($result);
if (!$user) {
    die("Error: The requested user profile was not found in our records.");
}
if (isset($_POST['update_user'])) {
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $role = $_POST['role'];
    $new_pass = $_POST['password'];
    if (!empty($new_pass)) {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET first_name='$fname', last_name='$lname', email='$email', phone_number='$phone', role='$role', password='$hashed' WHERE user_id=$target_id";
    } else {
        $sql = "UPDATE users SET first_name='$fname', last_name='$lname', email='$email', phone_number='$phone', role='$role' WHERE user_id=$target_id";
    }
    if (mysqli_query($conn, $sql)) {
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Modified User Profile (UID: $target_id)");
        header("Location: view_users_sorted.php?msg=System: User profile updated successfully.");
        exit();
    } else { $err = "Critical Database Failure: " . $conn->error; }
}
?>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="form-container">
        <h2 style="color: var(--purple); text-align: center;">Edit User Details</h2>
        <?php if($err): ?><p style="color: red; text-align: center;"><?= $err ?></p><?php endif; ?>
        <form method="POST" id="editUserForm" onsubmit="return validateForm()">
            <div class="form-group"><label>First Name</label><input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" onmouseout="validateFirstName()"></div>
            <div class="form-group"><label>Last Name</label><input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" onmouseout="validateLastName()"></div>
            <div class="form-group"><label>Role</label><select name="role" id="role" onmouseout="validateRole()">
                <option value="PASSENGER" <?= ($user['role'] == 'PASSENGER') ? 'selected' : '' ?>>PASSENGER</option>
                <option value="AGENT" <?= ($user['role'] == 'AGENT') ? 'selected' : '' ?>>AGENT</option>
                <option value="ADMIN" <?= ($user['role'] == 'ADMIN') ? 'selected' : '' ?>>ADMIN</option>
            </select></div>
            <div class="form-group"><label>New Password (Leave blank to keep current)</label><input type="password" name="password" id="password" placeholder="Enter new password only if changing" onmouseout="validatePassword()"></div>
        </form>
        <script>
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
    </div>
    <div style="height: 100px;"></div><script src="js/footer.js"></script>
</body>
</html>