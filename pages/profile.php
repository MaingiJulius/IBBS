<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];
$sql="SELECT * FROM users WHERE user_id=$user_id";
$res=mysqli_query($conn,$sql);
$user_data = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Personal Identity - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-container { max-width: 700px; margin: 60px auto; background: white; padding: 50px; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.06); border-top: 8px solid var(--purple); }
        .profile-header { text-align: center; border-bottom: 2px solid #f8fafc; padding-bottom: 30px; margin-bottom: 40px; }
        .info-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 25px; margin-bottom: 40px; }
        .info-label { font-weight: 800; color: var(--purple); text-transform: uppercase; font-size: 0.8rem; }
        .info-value { color: #1e293b; font-size: 1.1rem; }
        .password-section { margin-top: 50px; padding-top: 30px; border-top: 2px dashed #e2e8f0; }
        .password-form { display: flex; flex-direction: column; gap: 15px; max-width: 400px; margin: 20px auto 0; }
        .password-form input { padding: 12px 20px; border-radius: 50px; border: 1px solid #cbd5e1; background: #f8fafc; }
    </style>
    <script>
        function validateForm() {
            var curr = document.getElementById("current_password").value;
            var n1 = document.getElementById("new_password").value;
            var n2 = document.getElementById("confirm_password").value;
            if (curr == "") { alert("Current password required"); return false; }
            if (n1.length < 8) { alert("New password must be at least 8 characters"); return false; }
            if (n1 !== n2) { alert("Passwords do not match"); return false; }
            return true;
        }
    </script>
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'passenger') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="profile-container">
        <div class="profile-header">
            <h2 style="color: var(--purple); margin: 0;">👤 Profile Insights</h2>
            <div style="padding: 8px 18px; border-radius: 50px; background: var(--pink); color: #1e1b4b; font-size: 0.75rem; font-weight: 900; display: inline-block; margin-top: 15px;"><?= $_SESSION['role'] ?> AUTHORIZED</div>
        </div>
        <div class="info-grid">
            <div class="info-label">First Name:</div><div class="info-value"><?= htmlspecialchars($user_data['first_name']) ?></div>
            <div class="info-label">Last Name:</div><div class="info-value"><?= htmlspecialchars($user_data['last_name']) ?></div>
            <div class="info-label">Email:</div><div class="info-value"><?= htmlspecialchars($user_data['email']) ?></div>
            <div class="info-label">Phone:</div><div class="info-value"><?= htmlspecialchars($user_data['phone_number']) ?></div>
        </div>
        <div class="password-section">
            <h3 style="color: var(--purple); text-align: center;">🔐 Security Vault</h3>
            <form action="op_update_password.php" method="POST" class="password-form" onsubmit="return validateForm()">
                <input type="password" name="current_password" id="current_password" placeholder="Current Password">
                <input type="password" name="new_password" id="new_password" placeholder="New Password">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password">
                <button type="submit" class="button regular-button" style="background: var(--purple); color: white; border-radius: 50px; padding: 12px; font-weight: 700;">Update Password →</button>
            </form>
        </div>
        <div style="text-align: center; margin-top: 30px;"><a href="dashboard.php" style="color:var(--purple); font-weight:bold; text-decoration:none;">← Return to Dashboard Hub</a></div>
    </div>
    <div style="height: 120px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>