<?php
require_once 'db_connection.php';
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
    header("Location: login.html");
    exit();
}
if (isset($_GET['delete_route'])) {
    $route_id = $_GET['delete_route'];
    $sql_del = "DELETE FROM routes WHERE route_id = ?";
    mysqli_query($conn,$sql_del);
    header("Location: view_routes.php?msg=Success: Route removed.");
    exit();
}
if (isset($_POST['add_route'])) {
    $from   = $_POST['from_location'];
    $to     = $_POST['to_location'];
    $date   = $_POST['departure_date'];
    $time   = $_POST['departure_time'];
    $cost   = $_POST['cost'];
    $bus_id = $_POST['bus_id'];
    $sql_add = "INSERT INTO routes (from_location, to_location, departure_date, departure_time, cost, bus_id) VALUES (?, ?, ?, ?, ?, ?)";
    mysqli_stmt_execute($stmt_add);
    header("Location: view_routes.php?msg=Success: Route created.");
    exit();
}
if (isset($_POST['update_route'])) {
    $route_id = $_POST['route_id'];
    $from     = $_POST['from_location'];
    $to       = $_POST['to_location'];
    $date     = $_POST['departure_date'];
    $time     = $_POST['departure_time'];
    $cost     = $_POST['cost'];
    $bus_id   = $_POST['bus_id'];
    $sql_upd = "UPDATE routes SET from_location=?, to_location=?, departure_date=?, departure_time=?, cost=?, bus_id=? WHERE route_id=?";
    mysqli_stmt_execute($stmt_upd);
    header("Location: view_routes.php?msg=Success: Route updated.");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedules & Routes - Wema Travellers</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'staff') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 100px;"></div>
    <div class="container" style="margin: 0 auto; padding: 0 20px;">
        <div style="padding: 20px 0;"><a href="dashboard.php" class="button regular-button green-background" style="text-decoration:none;">← Control Panel Home</a></div>
        <div class="view-container">
            <h2 style="color: var(--purple);">🗺️ Travel Route Management Hub</h2>
            <?php if(isset($_GET['msg'])): ?><div style="background-color: #f0fff4; color: #22543d; padding: 15px; border-radius: 6px; margin-bottom: 25px; border-left: 5px solid #38a169;"><strong>Notice:</strong> <?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
            <div class="add-form">
                <h3 style="margin-top:0;">🆕 Initialize New travel path</h3>
                <form method="POST" id="routeForm" onsubmit="return validateForm()">
                    <div class="form-row">
                        <div class="form-group"><label>Departure City/Country</label><input type="text" name="from_location" id="from_location" class="input" placeholder="Kisumu, Kenya"></div>
                        <div class="form-group"><label>Arrival Endpoint</label><input type="text" name="to_location" id="to_location" class="input" placeholder="Kampala, Uganda"></div>
                        <div class="form-group"><label>Calendar date (YYYY-MM-DD)</label><input type="text" name="departure_date" id="departure_date" class="input" placeholder="2024-12-31"></div>
                        <div class="form-group"><label>Departure time (HH:MM)</label><input type="text" name="departure_time" id="departure_time" class="input" placeholder="14:30"></div>
                        <div class="form-group"><label>Price (KES)</label><input type="text" name="cost" id="cost" class="input" placeholder="1500"></div>
                        <div class="form-group"><label>Fleet vehicle</label>
                            <select name="bus_id" id="bus_id" class="input">
                                <option value="">Select Bus...</option>
                                <?php
                                $buses_sql = "SELECT bus_id, bus_name FROM buses ORDER BY bus_name ASC";
                                $buses_res = mysqli_query($conn, $buses_sql);
                                while($b = mysqli_fetch_assoc($buses_res)) { echo "<option value='{$b['bus_id']}'>{$b['bus_name']} (Fleet {$b['bus_id']})</option>"; }
                                ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="add_route" class="button regular-button pink-background" style="margin-top: 15px;">Save Route</button>
                </form>
            </div>
            <script>
                function toggleRouteEdit(rid) {
                    var views = document.querySelectorAll('.view-route-' + rid);
                    var edits = document.querySelectorAll('.edit-route-' + rid);
                    views.forEach(v => v.style.display = (v.style.display === 'none' ? 'inline' : 'none'));
                    edits.forEach(e => e.style.display = (e.style.display === 'none' ? 'inline' : 'none'));
                    document.getElementById('route-ops-main-' + rid).style.display =
                        (document.getElementById('route-ops-main-' + rid).style.display === 'none' ? 'inline-block' : 'none');
                    document.getElementById('route-ops-save-' + rid).style.display =
                        (document.getElementById('route-ops-save-' + rid).style.display === 'none' ? 'inline-block' : 'none');
                }
                function validateForm() {
                    var from = document.getElementById("from_location").value.trim();
                    var to = document.getElementById("to_location").value.trim();
                    if (from == "" || to == "") { alert("Please enter valid path."); return false; }
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
                    var headerRow = null;
                    var headerHasMatch = false;
                    for (var i = 0; i < trs.length; i++) {
                        var tr = trs[i];
                        var isHeader = tr.querySelector("td[colspan]");
                        if (isHeader) {
                            if (headerRow && !headerHasMatch && filter !== "") {
                                headerRow.style.display = "none";
                            }
                            headerRow = tr;
                            headerHasMatch = false;
                            tr.style.display = ""; 
                            continue;
                        }
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
                            headerHasMatch = true;
                            if (headerRow) {
                                headerRow.style.display = "";
                            }
                        } else {
                            tr.style.display = "none";
                        }
                    }
                    if (headerRow && !headerHasMatch && filter !== "") {
                        headerRow.style.display = "none";
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
                    🔍 Search By:
                </span>
                <select id="search-column" style="padding: 10px 15px; border-radius: 20px; border: 2px solid var(--button-border); background-color: var(--input-bg); color: var(--text-color); font-weight: bold; outline: none; cursor: pointer;">
                    <option value="all">All Fields</option>
// option for general scanning.
                    <option value="0">Ref ID</option>
// option for Ref ID search.
                    <option value="1">From</option>
// option for From location search.
                    <option value="2">To</option>
// option for To location search.
                    <option value="3">Date</option>
// option for Date search.
                    <option value="4">Time</option>
// option for Time search.
                    <option value="5">Fleet</option>
// option for Fleet search.
                    <option value="6">Cost</option>
// option for Cost search.
                </select>
                <input type="text" id="search-input" placeholder="Type to filter routes..." onkeyup="filterTable()" style="flex: 1; padding: 10px 20px; border-radius: 20px; border: 2px solid var(--button-border); background-color: var(--input-bg); color: var(--text-color); font-size: 1rem; outline: none;">
            </div>
            <table class="crud-table">
                <thead><tr><th>Ref ID</th><th>From</th><th>To</th><th>Date</th><th>Time</th><th>Fleet</th><th>Cost</th><th>Operations</th></tr></thead>
                <tbody>
                    <?php
                    $buses_list = [];
                    mysqli_data_seek($buses_res, 0);
                    while($b = mysqli_fetch_assoc($buses_res)) { $buses_list[] = $b; }
                    $sql_routes = "SELECT r.*, b.bus_name, SUBSTRING_INDEX(r.from_location, ', ', -1) as country FROM routes r JOIN buses b ON r.bus_id = b.bus_id ORDER BY country ASC, r.departure_date ASC";
                    $res_routes = mysqli_query($conn, $sql_routes);
                    $current_country = "";
                    while($row = mysqli_fetch_assoc($res_routes)):
                        $rid = $row['route_id'];
                        if ($row['country'] != $current_country) {
                            $current_country = $row['country'];
                            echo "<tr><td colspan='8' style='background:#f9fafb; font-weight:800; color:#4a5568;'>🌍 Region: " . htmlspecialchars($current_country) . "</td></tr>";
                        }
                    ?>
                    <tr>
                        <form method="POST">
                            <input type="hidden" name="route_id" value="<?= $rid ?>">
                            <td style="font-weight: bold;"><?= $rid ?></td>
// From
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= htmlspecialchars($row['from_location']) ?></span>
                                <input type="text" name="from_location" value="<?= htmlspecialchars($row['from_location']) ?>" class="edit-route-<?= $rid ?> table-input" style="display:none;">
                            </td>
// To
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= htmlspecialchars($row['to_location']) ?></span>
                                <input type="text" name="to_location" value="<?= htmlspecialchars($row['to_location']) ?>" class="edit-route-<?= $rid ?> table-input" style="display:none;">
                            </td>
// Date
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= $row['departure_date'] ?></span>
                                <input type="text" name="departure_date" value="<?= $row['departure_date'] ?>" class="edit-route-<?= $rid ?> table-input" style="display:none; width: 100px;">
                            </td>
// Time
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= $row['departure_time'] ?></span>
                                <input type="text" name="departure_time" value="<?= $row['departure_time'] ?>" class="edit-route-<?= $rid ?> table-input" style="display:none; width: 70px;">
                            </td>
// Fleet
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= htmlspecialchars($row['bus_name']) ?></span>
                                <select name="bus_id" class="edit-route-<?= $rid ?> table-input" style="display:none;">
                                    <?php foreach($buses_list as $b): ?>
                                    <option value="<?= $b['bus_id'] ?>" <?= ($b['bus_id'] == $row['bus_id'] ? 'selected' : '') ?>><?= htmlspecialchars($b['bus_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
// Cost
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= $row['cost'] ?></span>
                                <span class="edit-route-<?= $rid ?>" style="display:none;"><input type="text" name="cost" value="<?= $row['cost'] ?>" class="table-input" style="width: 80px;"> KES</span>
                            </td>
                            <td style="white-space: nowrap;">
// Main Operations
                                 <div id="route-ops-main-<?= $rid ?>">
                                     <button type="button" class="action-btn btn-update" onclick="toggleRouteEdit(<?= $rid ?>)">Update</button>
                                     <a href="?delete_route=<?= $rid ?>" class="action-btn btn-delete" onclick="return confirm('Delete this route?')">Delete</a>
                                 </div>
                                 <div id="route-ops-save-<?= $rid ?>" style="display:none;">
                                     <button type="submit" name="update_route" class="action-btn btn-update">Update</button>
                                     <button type="button" class="action-btn btn-delete" onclick="toggleRouteEdit(<?= $rid ?>)">Cancel</button>
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