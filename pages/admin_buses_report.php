<?php
// the block.
require_once 'db_connection.php';
require_once 'logger.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    die("Security Error: Access denied.");
}
if (isset($_POST['add_bus'])) {
    $reg_no = $_POST['reg_no'];
    $bus_name = $_POST['bus_name'];
    $max_passengers = $_POST['max_passengers'];
    $sql_add = "INSERT INTO buses (reg_no, bus_name, max_passengers) VALUES (?, ?, ?)";
    mysqli_query($conn,$sql_add);
    header('Location: admin_buses_report.php?msg=Bus Added');
    exit();
}
if (isset($_POST['update_bus'])) {
    $bus_id = $_POST['bus_id'];
    $reg_no = $_POST['reg_no'];
    $bus_name = $_POST['bus_name'];
    $max_passengers = $_POST['max_passengers'];
    $driver_id = !empty($_POST['driver_id']) ? $_POST['driver_id'] : null;
    $sql_upd = "UPDATE buses SET reg_no=?, bus_name=?, max_passengers=?, driver_id=? WHERE bus_id=?";
// from injecting malicious code because the machine already knows the
    mysqli_stmt_bind_param($stmt_upd, "ssiii", $reg_no, $bus_name, $max_passengers, $driver_id, $bus_id);
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'UPDATE', "Updated Bus: $bus_id");
    mysqli_stmt_execute($stmt_upd);
    header('Location: admin_buses_report.php?msg=Updated');
    exit();
}
if (isset($_GET['remove_bus'])) {
    $bus_id = $_GET['remove_bus'];
    $sql_del="DELETE FROM buses WHERE bus_id=$bus_id";
    logActivity($_SESSION['user_id'], $_SESSION['name'], 'DELETION', "Removed Bus: $bus_id");
    mysqli_stmt_execute($stmt_del);
    header("Location: admin_buses_report.php?msg=Deleted");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fleet Management - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= strtolower($_SESSION['role']) ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="container" style="margin: 0 auto;">
        <div class="view-container">
            <h2 style="color: var(--purple);">Fleet Management</h2>
            <div style="background: #fdfdfd; padding: 20px; border: 1px solid #eee; margin-bottom: 30px;">
                <h3>Add New Bus</h3>
                <form method="POST" onsubmit="return validateForm()">
                    <div style="display: flex; gap: 10px;">
                        <div style="flex:1;"><label>Plate No.</label><input type="text" name="reg_no" id="reg_no" class="input"></div>
                        <div style="flex:1;"><label>Name</label><input type="text" name="bus_name" id="bus_name" class="input"></div>
                        <div style="flex:1;"><label>Capacity</label><input type="text" name="max_passengers" id="max_passengers" class="input" value="40"></div>
                    </div>
                    <button type="submit" name="add_bus" class="button pink-background" style="margin-top: 15px;">Save Bus</button>
                </form>
            </div>
            <script>
                function toggleBusEdit(bid) {
                    var views = document.querySelectorAll('.view-bus-' + bid);
                    var edits = document.querySelectorAll('.edit-bus-' + bid);
                    views.forEach(v => v.style.display = (v.style.display === 'none' ? 'inline' : 'none'));
                    edits.forEach(e => e.style.display = (e.style.display === 'none' ? 'inline' : 'none'));
                    document.getElementById('ops-m-' + bid).style.display = (document.getElementById('ops-m-' + bid).style.display === 'none' ? 'inline-block' : 'none');
                    document.getElementById('ops-s-' + bid).style.display = (document.getElementById('ops-s-' + bid).style.display === 'none' ? 'inline-block' : 'none');
                }
                function validateForm() {
                    var r = document.getElementById("reg_no").value.trim();
                    if (r == "") { alert("Plate Required"); return false; }
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
            <div class="search-container no-print" style="margin-bottom: 20px; display: flex; gap: 15px; align-items: center; background: rgba(255, 255, 255, 0.9); padding: 15px 20px; border-radius: 25px; border: 2px solid var(--button-border); box-shadow: 3px 3px 0px rgba(0,0,0,1);">
                <span style="font-weight: bold; color: var(--text-color); font-size: 1.1rem; display: flex; align-items: center; gap: 5px;">
                    🔍 Search By:
                </span>
                <select id="search-column" style="padding: 10px 15px; border-radius: 20px; border: 2px solid var(--button-border); background-color: var(--input-bg); color: var(--text-color); font-weight: bold; outline: none; cursor: pointer;">
                    <option value="all">All Fields</option>
                    <option value="0">ID</option>
                    <option value="1">Name</option>
                    <option value="2">Plate</option>
                    <option value="3">Capacity</option>
                    <option value="4">Driver</option>
                </select>
                <input type="text" id="search-input" placeholder="Type to filter fleet..." onkeyup="filterTable()" style="flex: 1; padding: 10px 20px; border-radius: 20px; border: 2px solid var(--button-border); background-color: var(--input-bg); color: var(--text-color); font-size: 1rem; outline: none;">
            </div>
            <table class="crud-table">
                <thead><tr><th>ID</th><th>Name</th><th>Plate</th><th>Capacity</th><th>Driver</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php
                    $drvs = mysqli_query($conn, "SELECT driver_id, full_name FROM drivers ORDER BY full_name ASC");
                    $dlist = []; while($d = mysqli_fetch_assoc($drvs)) { $dlist[] = $d; }
                    $res = mysqli_query($conn, "SELECT b.*, d.full_name as driver_name FROM buses b LEFT JOIN drivers d ON b.driver_id = d.driver_id ORDER BY b.bus_id DESC");
                    while($row = mysqli_fetch_assoc($res)): $bid = $row['bus_id'];
                    ?>
                    <tr>
                        <form method="POST">
                            <input type="hidden" name="bus_id" value="<?= $bid ?>">
                            <td><?= $bid ?></td>
                            <td>
                                <span class="view-bus-<?= $bid ?>"><?= htmlspecialchars($row['bus_name']) ?></span>
                                <input type="text" name="bus_name" value="<?= htmlspecialchars($row['bus_name']) ?>" class="edit-bus-<?= $bid ?>" style="display:none;">
                            </td>
                            <td>
                                <span class="view-bus-<?= $bid ?>"><?= htmlspecialchars($row['reg_no']) ?></span>
                                <input type="text" name="reg_no" value="<?= htmlspecialchars($row['reg_no']) ?>" class="edit-bus-<?= $bid ?>" style="display:none;">
                            </td>
                            <td>
                                <span class="view-bus-<?= $bid ?>"><?= $row['max_passengers'] ?></span>
                                <input type="text" name="max_passengers" value="<?= $row['max_passengers'] ?>" class="edit-bus-<?= $bid ?>" style="display:none; width:50px;">
                            </td>
                            <td>
                                <select name="driver_id" class="input" style="padding:5px; width:auto;">
                                    <option value="">-- No Driver --</option>
                                    <?php foreach($dlist as $d): ?>
                                    <option value="<?= $d['driver_id'] ?>" <?= ($d['driver_id'] == $row['driver_id'] ? 'selected' : '') ?>><?= htmlspecialchars($d['full_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_bus" class="action-btn" style="background:#48bb78; margin:0;">Save</button>
                            </td>
                            <td>
                                <div id="ops-m-<?= $bid ?>">
                                    <button type="button" class="action-btn btn-update" onclick="toggleBusEdit(<?= $bid ?>)">Edit</button>
                                    <a href="?remove_bus=<?= $bid ?>" class="action-btn btn-delete" onclick="return confirm('Delete?')">Del</a>
                                </div>
                                <div id="ops-s-<?= $bid ?>" style="display:none;">
                                    <button type="submit" name="update_bus" class="action-btn btn-update">Update</button>
                                    <button type="button" class="action-btn btn-delete" onclick="toggleBusEdit(<?= $bid ?>)">Cancel</button>
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