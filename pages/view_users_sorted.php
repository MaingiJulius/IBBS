<?php
require_once 'db_connection.php';
require_once 'logger.php';
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    die("Access Denied: Staff Authorization Required.");
}
if (isset($_GET['delete_user'])) {
    $uid = $_GET['delete_user'];
    if ($uid == $_SESSION['user_id']) {
        header("Location: view_users_sorted.php?err=System Safety: You cannot delete yourself!");
        exit();
    }
    $sql_del = "DELETE FROM users WHERE user_id = ?";
    mysqli_stmt_execute($stmt_del);
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed User UID: $uid");
    header("Location: view_users_sorted.php?msg=User deleted successfully.");
    exit();
}
if (isset($_GET['reset_password'])) {
    $uid = $_GET['reset_password'];
    $sql_get = "SELECT first_name FROM users WHERE user_id = ?";
    $res_get=mysqli_query($conn,$sql_get);
    if ($row_get = mysqli_fetch_assoc($res_get)) {
        $first_name = $row_get['first_name'];
        $plain_pass = ucfirst($first_name) . "123";
        $hashed_pass = password_hash($plain_pass, PASSWORD_DEFAULT);
        $sql_reset = "UPDATE users SET password = ? WHERE user_id = ?";
        mysqli_query($conn,$sql_reset);
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'PASSWORD_RESET', "Reset password for user ID $uid to " . $plain_pass);
        header("Location: view_users_sorted.php?msg=Password reset to " . htmlspecialchars($plain_pass) . " successfully.");
    } else {
        header("Location: view_users_sorted.php?err=User not found.");
    }
    exit();
}
if (isset($_POST['add_user'])) {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $role = $_POST['role'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql_add = "INSERT INTO users (first_name, last_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?, ?)";
    if(mysqli_stmt_execute($stmt_add)) {
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'REGISTRATION', "Created new user: $email");
        header("Location: view_users_sorted.php?msg=New user added.");
    } else {
        header("Location: view_users_sorted.php?err=Error: " . mysqli_error($conn));
    }
    exit();
}
if (isset($_POST['update_user'])) {
    $uid = $_POST['user_id'];
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $role = $_POST['role'];
    $sql_upd = "UPDATE users SET first_name=?, last_name=?, email=?, phone_number=?, role=? WHERE user_id=?";
    if(mysqli_stmt_execute($stmt_upd)) {
        logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Modified User UID: $uid");
        header("Location: view_users_sorted.php?msg=User updated.");
    } else {
        header("Location: view_users_sorted.php?err=Update Failed.");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <script>
        function toggleEdit(uid) {
            var views = document.querySelectorAll('.view-' + uid);
            var edits = document.querySelectorAll('.edit-' + uid);
            views.forEach(v => v.style.display = (v.style.display === 'none' ? 'inline' : 'none'));
            edits.forEach(e => e.style.display = (e.style.display === 'none' ? 'inline' : 'none'));
            document.getElementById('btn-main-' + uid).style.display =
                (document.getElementById('btn-main-' + uid).style.display === 'none' ? 'inline-block' : 'none');
            document.getElementById('btn-save-' + uid).style.display =
                (document.getElementById('btn-save-' + uid).style.display === 'none' ? 'inline-block' : 'none');
        }
        function validateInline(uid) {
            var f = document.getElementById("f-"+uid).value.trim();
            if (f == "") { alert("First Name is required"); return false; }
            return true;
        }
        function validateAdd() {
            var f = document.getElementById("add_f").value.trim();
            if (f == "") { alert("First Name is required"); return false; }
            return true;
        }
        function filterTable() {
            var input = document.getElementById("search-input");
            var filter = input.value.toLowerCase();
            var select = document.getElementById("search-column");
            var colIndex = select.value;
            var table = document.querySelector(".crud-table");
            var tbody = table.getElementsByTagName("tbody")[0];
            var trs = tbody.getElementsByTagName("tr");
            for (var i = 0; i < trs.length; i++) {
                var tr = trs[i];
                var match = false;
                if (colIndex === "all") {
                    var tds = tr.getElementsByTagName("td");
                    for (var j = 0; j < tds.length - 1; j++) { 
                        var td = tds[j];
                        if (td) {
                            var txtValue = getCellText(td);
                            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                match = true;
                                break;
                            }
                        }
                    }
                } else {
                    var td = tr.getElementsByTagName("td")[colIndex];
                    if (td) {
                        var txtValue = getCellText(td);
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            match = true;
                        }
                    }
                }
                if (match) {
                    tr.style.display = "";
                } else {
                    tr.style.display = "none";
                }
            }
        }
        function getCellText(td) {
            var selectElement = td.querySelector("select");
            if (selectElement && selectElement.style.display !== "none") {
                if (selectElement.selectedIndex >= 0) {
                    return selectElement.options[selectElement.selectedIndex].text;
                }
                return "";
            }
            var viewSpan = td.querySelector("span[class^='view-']");
            if (viewSpan) {
                return viewSpan.textContent || viewSpan.innerText;
            }
            return td.innerText || td.textContent || "";
        }
    </script>
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="view-container">
        <h2 style="color:var(--purple);">👥 User Management</h2>
        <?php if(isset($_GET['msg'])): ?>
            <div style="color:green; font-weight:bold;"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['err'])): ?>
            <div style="color:red; font-weight:bold;"><?= htmlspecialchars($_GET['err']) ?></div>
        <?php endif; ?>
        <div class="add-form">
            <h3>Add New User</h3>
            <form method="POST" onsubmit="return validateAdd()">
                <input type="text" name="first_name" id="add_f" placeholder="First Name" required class="input">
                <input type="text" name="last_name" placeholder="Last Name" class="input">
                <input type="email" name="email" id="add_e" placeholder="Email" required class="input">
                <input type="text" name="phone_number" placeholder="Phone" class="input">
                <input type="password" name="password" id="add_p" placeholder="Password" required class="input">
                <select name="role" class="input">
                    <option value="PASSENGER">PASSENGER</option>
                    <option value="AGENT">AGENT</option>
                    <option value="ADMIN">ADMIN</option>
                </select>
                <button type="submit" name="add_user" class="action-btn btn-update">Save New User</button>
            </form>
        </div>
        <div class="search-container no-print" style="margin-bottom: 20px; display: flex; gap: 15px; align-items: center; background: rgba(255, 255, 255, 0.9); padding: 15px 20px; border-radius: 25px; border: 2px solid var(--button-border); box-shadow: 3px 3px 0px rgba(0,0,0,1);">
            <span style="font-weight: bold; color: var(--text-color); font-size: 1.1rem; display: flex; align-items: center; gap: 5px;">
                🔍 Search By:
            </span>
            <select id="search-column" style="padding: 10px 15px; border-radius: 20px; border: 2px solid var(--button-border); background-color: var(--input-bg); color: var(--text-color); font-weight: bold; outline: none; cursor: pointer;">
                <option value="all">All Fields</option>
                <option value="0">ID</option>
                <option value="1">First Name</option>
                <option value="2">Last Name</option>
                <option value="3">Email</option>
                <option value="4">Phone</option>
                <option value="5">Role</option>
            </select>
            <input type="text" id="search-input" placeholder="Type to filter users..." onkeyup="filterTable()" style="flex: 1; padding: 10px 20px; border-radius: 20px; border: 2px solid var(--button-border); background-color: var(--input-bg); color: var(--text-color); font-size: 1rem; outline: none;">
        </div>
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_list = "SELECT * FROM users ORDER BY user_id ASC";
                $res_list = mysqli_query($conn, $sql_list);
                while($row = mysqli_fetch_assoc($res_list)):
                    $uid = $row['user_id'];
                ?>
                <tr>
                    <form method="POST" onsubmit="return validateInline(<?= $uid ?>)">
                        <input type="hidden" name="user_id" value="<?= $uid ?>">
                        <td><?= $uid ?></td>
                        <td>
                            <span class="view-<?= $uid ?>"><?= htmlspecialchars($row['first_name']) ?></span>
                            <input type="text" name="first_name" id="f-<?= $uid ?>" value="<?= htmlspecialchars($row['first_name']) ?>" class="edit-<?= $uid ?> input-inline" style="display:none;">
                        </td>
                        <td>
                            <span class="view-<?= $uid ?>"><?= htmlspecialchars($row['last_name']) ?></span>
                            <input type="text" name="last_name" id="l-<?= $uid ?>" value="<?= htmlspecialchars($row['last_name']) ?>" class="edit-<?= $uid ?> input-inline" style="display:none;">
                        </td>
                        <td>
                            <span class="view-<?= $uid ?>"><?= htmlspecialchars($row['email']) ?></span>
                            <input type="email" name="email" id="e-<?= $uid ?>" value="<?= htmlspecialchars($row['email']) ?>" class="edit-<?= $uid ?> input-inline" style="display:none;">
                        </td>
                        <td>
                            <span class="view-<?= $uid ?>"><?= htmlspecialchars($row['phone_number']) ?></span>
                            <input type="text" name="phone_number" value="<?= htmlspecialchars($row['phone_number']) ?>" class="edit-<?= $uid ?> input-inline" style="display:none;">
                        </td>
                        <td>
                            <span class="view-<?= $uid ?>"><?= $row['role'] ?></span>
                            <select name="role" class="edit-<?= $uid ?> input-inline" style="display:none;">
                                <option value="PASSENGER" <?= $row['role']=='PASSENGER'?'selected':'' ?>>PASSENGER</option>
                                <option value="AGENT" <?= $row['role']=='AGENT'?'selected':'' ?>>AGENT</option>
                                <option value="ADMIN" <?= $row['role']=='ADMIN'?'selected':'' ?>>ADMIN</option>
                            </select>
                        </td>
                        <td>
                            <div id="btn-main-<?= $uid ?>">
                                <button type="button" class="action-btn btn-update" onclick="toggleEdit(<?= $uid ?>)">Update</button>
                                <a href="?delete_user=<?= $uid ?>" class="action-btn btn-delete" onclick="return confirm('Delete user?')">Delete</a>
                                <a href="?reset_password=<?= $uid ?>" class="action-btn btn-reset" style="background-color: var(--purple);" onclick="return confirm('Reset password for this user?')">Reset</a>
                            </div>
                            <div id="btn-save-<?= $uid ?>" style="display:none;">
                                <button type="submit" name="update_user" class="action-btn btn-update">Save</button>
                                <button type="button" class="action-btn btn-delete" onclick="toggleEdit(<?= $uid ?>)">Cancel</button>
                            </div>
                        </td>
                    </form>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
    <div style="text-align:center;"><a href="dashboard.php" style="color:var(--purple); font-weight:bold;">← Back to Dashboard</a></div>
</body>
</html>
<?php mysqli_close($conn); ?>