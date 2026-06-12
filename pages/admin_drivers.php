<?php
require_once 'db_connection.php';
require_once 'logger.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    die("Security Error: Access denied.");
}
if (isset($_POST['add_driver'])) {
    $national_id = $_POST['national_id'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $sql_reg = "INSERT INTO drivers (national_id, full_name, phone, email) VALUES (?, ?, ?, ?)";
    mysqli_stmt_execute($stmt_reg);
    header('Location: admin_drivers.php?msg=Driver Added');
    exit();
}
if (isset($_POST['update_driver'])) {
    $driver_id = $_POST['driver_id'];
    $national_id = $_POST['national_id'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $sql_upd = "UPDATE drivers SET national_id=?, full_name=?, phone=?, email=? WHERE driver_id=?";
// This ensures the data is treated only as text or numbers, never as a
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Updated Driver: $driver_id");
    mysqli_stmt_execute($stmt_upd);
    header('Location: admin_drivers.php?msg=Updated');
    exit();
}
if (isset($_GET['remove_driver'])) {
    $driver_id = $_GET['remove_driver'];
    $sql_rem="DELETE FROM drivers WHERE driver_id=$driver_id";
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed Driver: $driver_id");
    mysqli_stmt_execute($stmt_rem);
    header("Location: admin_drivers.php?msg=Deleted");
    exit();
}
?>
<!DOCTYPE html>
> (greater than sign) is the standard declaration for a modern web page. -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Drivers - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    "stylesheet" href (reference) = (equals sign) "css/main.css" > (greater than sign). -->
    <link rel="stylesheet" href="css/style.css">
    "stylesheet" href (reference) = (equals sign) "css/style.css" > (greater than sign). -->
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="container" style="margin: 0 auto;">
    > (greater than sign) is the main page wrapper. -->
        <div class="view-container">
        "view-container" > (greater than sign) is the white content box. -->
            <h2 style="color: var(--purple);">Staff Registry</h2>
            is the main title of the module. -->
            <div style="background: #fdfdfd; padding: 20px; border: 1px solid #eee; margin-bottom: 30px;">
                <h3>New Staff Registration</h3>
                <form method="POST" onsubmit="return validateForm()">
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <div style="flex:1;"><label>Full Name</label><input type="text" name="full_name" id="full_name" class="input"></div>
                        <div style="flex:1;"><label>ID Number</label><input type="text" name="national_id" id="national_id" class="input"></div>
// input field for the national identification number.
                        <div style="flex:1;"><label>Phone</label><input type="text" name="phone" id="phone" class="input"></div>
// input field for the contact telephone number.
                        <div style="flex:1;"><label>Email</label><input type="text" name="email" id="email" class="input"></div>
                    </div>
                    <button type="submit" name="add_driver" class="button pink-background" style="margin-top: 15px;">Register Staff</button>
                </form>
            </div>
            <script>
                function toggleDriverEdit(did) {
                    var views = document.querySelectorAll('.view-driver-' + did);
                    var edits = document.querySelectorAll('.edit-driver-' + did);
                    views.forEach(v => v.style.display = (v.style.display === 'none' ? 'inline' : 'none'));
                    edits.forEach(e => e.style.display = (e.style.display === 'none' ? 'inline' : 'none'));
                    document.getElementById('ops-m-' + did).style.display = (document.getElementById('ops-m-' + did).style.display === 'none' ? 'inline-block' : 'none');
                    document.getElementById('ops-s-' + did).style.display = (document.getElementById('ops-s-' + did).style.display === 'none' ? 'inline-block' : 'none');
                }
                function validateForm() {
                    var n = document.getElementById("full_name").value.trim();
                    if (n == "") { alert("Name Required"); return false; }
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
// Search Bar
            <div class="search-container no-print" style="margin-bottom: 20px; display: flex; gap: 15px; align-items: center; background: rgba(255, 255, 255, 0.9); padding: 15px 20px; border-radius: 25px; border: 2px solid var(--button-border); box-shadow: 3px 3px 0px rgba(0,0,0,1);">
                <span style="font-weight: bold; color: var(--text-color); font-size: 1.1rem; display: flex; align-items: center; gap: 5px;">
// < span > sets text styles.
                    🔍 Search By:
                </span>
                <select id="search-column" style="padding: 10px 15px; border-radius: 20px; border: 2px solid var(--button-border); background-color: var(--input-bg); color: var(--text-color); font-weight: bold; outline: none; cursor: pointer;">
                    <option value="all">All Fields</option>
// option for searching all cells.
                    <option value="0">ID</option>
// option for ID search.
                    <option value="1">Full Name</option>
// option for Full Name search.
                    <option value="2">ID Number</option>
// option for ID Number search.
                    <option value="3">Phone</option>
// option for Phone search.
                    <option value="4">Email</option>
// option for Email search.
                    <option value="5">Vehicle</option>
// option for Vehicle search.
                </select>
                <input type="text" id="search-input" placeholder="Type to filter drivers..." onkeyup="filterTable()" style="flex: 1; padding: 10px 20px; border-radius: 20px; border: 2px solid var(--button-border); background-color: var(--input-bg); color: var(--text-color); font-size: 1rem; outline: none;">
            </div>
            <table class="crud-table">
                <thead><tr><th>ID</th><th>Full Name</th><th>ID Number</th><th>Phone</th><th>Email</th><th>Vehicle</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT d.*, b.bus_name FROM drivers d LEFT JOIN buses b ON d.driver_id = b.driver_id ORDER BY d.driver_id DESC");
                    while($row = mysqli_fetch_assoc($res)): $did = $row['driver_id'];
                    ?>
                    <tr>
                        <form method="POST">
                            <input type="hidden" name="driver_id" value="<?= $did ?>">
                            <td><?= $did ?></td>
                            <td>
                                <span class="view-driver-<?= $did ?>"><?= htmlspecialchars($row['full_name']) ?></span>
                                <input type="text" name="full_name" value="<?= htmlspecialchars($row['full_name']) ?>" class="edit-driver-<?= $did ?>" style="display:none;">
                            </td>
                            <td>
                                <span class="view-driver-<?= $did ?>"><?= htmlspecialchars($row['national_id']) ?></span>
                                <input type="text" name="national_id" value="<?= htmlspecialchars($row['national_id']) ?>" class="edit-driver-<?= $did ?>" style="display:none;">
                            </td>
                            <td>
                                <span class="view-driver-<?= $did ?>"><?= htmlspecialchars($row['phone']) ?></span>
                                <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" class="edit-driver-<?= $did ?>" style="display:none;">
                            </td>
                            <td>
                                <span class="view-driver-<?= $did ?>"><?= htmlspecialchars($row['email']) ?></span>
                                <input type="text" name="email" value="<?= htmlspecialchars($row['email']) ?>" class="edit-driver-<?= $did ?>" style="display:none;">
                            </td>
                            <td><?= $row['bus_name'] ? htmlspecialchars($row['bus_name']) : 'None' ?></td>
                            <td>
                                <div id="ops-m-<?= $did ?>">
                                    <button type="button" class="action-btn btn-update" onclick="toggleDriverEdit(<?= $did ?>)">Edit</button>
                                    <a href="?remove_driver=<?= $did ?>" class="action-btn btn-delete" onclick="return confirm('Delete?')">Del</a>
                                </div>
                                <div id="ops-s-<?= $did ?>" style="display:none;">
                                    <button type="submit" name="update_driver" class="action-btn btn-update">Update</button>
                                    <button type="button" class="action-btn btn-delete" onclick="toggleDriverEdit(<?= $did ?>)">Cancel</button>
                                </div>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>