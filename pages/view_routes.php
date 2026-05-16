<?php
// <?php (opening tag) tells the server to start interpreting the code as PHP.

/**
 * ADMINISTRATION: ROUTE & SCHEDULE MANAGEMENT (view_routes.php)
 */

// require_once (require once) includes the database connection.
require_once 'db_connection.php';
// session_start (session start) starts the user session.
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['ADMIN', 'AGENT'])) {
// if (if) starts a logic check to verify the visitor's identity and permissions. 
// ( (opening bracket) starts the condition. ! (exclamation mark) is the 
// NOT operator. isset (is set) checks if a variable exists. ( (bracket) 
// $ (dollar sign) _ (underscore) SESSION (Superglobal Array used for 
// server-side memory) [ 'user_id' ] (key for identification) ) (bracket). 
// || (double pipe) is the logical OR operator. ! (exclamation mark) is NOT. 
// in_array (in array) is a tool that checks if a value exists within a list. 
// ( (bracket) $ (dollar sign) _ (underscore) SESSION [ 'role' ] (current 
// user type) , (comma) [ 'ADMIN' , 'AGENT' ] (list of allowed roles) ) 
// (closing bracket). ) (closing bracket) ends the condition. { (opening 
// curly bracket) marks the start of the denial logic.

    header("Location: login.html");
    // header (header) redirection tool sends unauthorized users to the login 
    // page. ; (semicolon).

    exit();
    // exit (exit) stops all further script execution immediately. ; (semicolon).
}

// --- ACTION 1: DELETE ROUTE ---
if (isset($_GET['delete_route'])) {
// if (if) starts a logic check to determine if a route removal request has 
// been transmitted through the URL query string. ( (opening bracket) 
// starts the condition. isset (is set) checks if a variable exists. 
// ( (bracket) $ (dollar sign) is the variable prefix. _ (underscore) 
// connects the prefix to the array name. GET (G E T) is a Superglobal Array 
// used by the server to collect and pull data from the URL query string. 
// It does NOT send data to the database directly; it only captures what 
// is in the address bar. [ (opening square bracket) starts the index pointer. 
// 'delete_route' (quote) is the key. ] (closing square bracket) ends the 
// pointer. ) (bracket) ends the isset tool. ) (closing bracket) ends the 
// condition. { (opening curly bracket) marks the start of the deletion logic.

    $route_id = $_GET['delete_route'];
    // $ (dollar sign) variable marker. route_id (r o u t e underscore i d) is 
    // the logical label chosen to identify the identification number of the 
    // route. = (equals sign) assignment operator. $ (dollar sign) _ (underscore) 
    // GET [ 'delete_route' ] retrieves the ID from the URL. ; (semicolon).
    
    $sql_del = "DELETE FROM routes WHERE route_id = ?";
    // $ (dollar sign) variable marker. sql_del (s q l underscore d e l) is a 
    // logical identifier chosen to describe the database removal command. 
    // = (equals sign) assignment operator. "DELETE FROM..." (quote) starts the 
    // SQL instruction. ? (question mark) is a critical security placeholder 
    // that neutralizes SQL Injection by ensuring the ID added later is treated 
    // only as a literal value. ; (semicolon) terminates the line.
    
    $stmt_del = mysqli_prepare($conn, $sql_del);
    /* $stmt_del (handle) = (assignment). 
       mysqli (MySQL Improved) _ (underscore) prepare (prepare) is the security 
       function that pre-compiles the command blueprint. 
       "Improved" (mysqli) is used because it supports secure "Prepared Statements" 
       using placeholders like ? to prevent SQL Injection. 
       ( starts. $conn (bridge) , (comma) $sql_del (blueprint) ) ends. ; (semicolon). */
    
    mysqli_stmt_bind_param($stmt_del, "i", $route_id);
    /* mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
       bind (bind) _ (underscore) param (parameter) is the function that 
       securely pours the data into the ? placeholders. 
       The ? (Question Mark) is a safety hole that ensures user data 
       is never treated as a command, blocking SQL Injection. 
       ( starts. $stmt_del (tool handle) , (comma) "i" (integer number type) 
       , (comma) $route_id (route identity data) ) ends. ; (semicolon). */
    
    mysqli_stmt_execute($stmt_del);
    // mysqli (MySQL Improved) _ (underscore) stmt (statement) _ (underscore) 
    // execute (execute) is the command that triggers the database removal. 
    // ( (opening bracket) $ (dollar sign) stmt_del (handle) ) (closing bracket). 
    // ; (semicolon).

    mysqli_stmt_close($stmt_del);
    // mysqli_stmt_close (close) terminates the tool and releases resources. 
    // ( (bracket) $ (dollar sign) stmt_del ) (bracket). ; (semicolon).
    
    header("Location: view_routes.php?msg=Success: Route removed.");
    // header (header) redirection tool sends the user back to the list. 
    // ; (semicolon).

    exit();
    // exit (exit) kills the script processing immediately. ( ) (empty brackets). 
    // ; (semicolon).
}
// } (closing curly bracket) ends the deletion block.

// --- ACTION 2: ADD NEW ROUTE ---
if (isset($_POST['add_route'])) {
// if (if) starts a logic check to determine if the route creation form has 
// been submitted. ( (opening bracket) starts the condition. isset (is set) 
// checks if a variable exists. ( (bracket) $ (dollar sign) _ (underscore) 
// POST [ 'add_route' ] (submit button) ) (bracket) ) (bracket). 
// { (opening curly bracket) marks the start of the insertion logic.

    $from   = $_POST['from_location'];
    // $ (dollar sign) variable marker. from (f r o m) is the label chosen to 
    // identify the departure origin. = (equals sign) assignment operator. 
    // $ (dollar sign) _ (underscore) POST (Superglobal Array used by the 
    // server to collect and pull data from an HTML form sent via the secure 
    // HTTP POST method. It does NOT send data to the database directly; it 
    // only captures what the user typed) [ 'from_location' ] retrieves data. 
    // ; (semicolon).

    $to     = $_POST['to_location'];
    // $ (dollar sign) variable. to label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST [ 'to_location' ] ; (semicolon).

    $date   = $_POST['departure_date'];
    // $ (dollar sign) variable. date label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST [ 'departure_date' ] ; (semicolon).

    $time   = $_POST['departure_time'];
    // $ (dollar sign) variable. time label. = (equals sign) assignment. $ (dollar sign) 
    // _ (underscore) POST [ 'departure_time' ] ; (semicolon).

    $cost   = $_POST['cost'];
    // $ (dollar sign) variable. cost (c o s t) label. = (equals sign) 
    // assignment. $ (dollar sign) _ (underscore) POST [ 'cost' ] ; (semicolon).

    $bus_id = $_POST['bus_id'];
    // $ (dollar sign) variable. bus_id (b u s underscore i d) label. = (equals 
    // sign) assignment. $ (dollar sign) _ (underscore) POST [ 'bus_id' ] 
    // ; (semicolon).
    
    $sql_add = "INSERT INTO routes (from_location, to_location, departure_date, departure_time, cost, bus_id) VALUES (?, ?, ?, ?, ?, ?)";
    // $ (dollar sign) variable marker. sql_add (s q l underscore a d d) is a 
    // logical identifier chosen to describe the database creation command. 
    // = (equals sign) assignment. "INSERT INTO..." (quote) starts the SQL 
    // instruction. ? (question marks) are six critical security placeholders 
    // that neutralize SQL Injection by separating the command structure from 
    // user data. ; (semicolon) terminates the command.

    $stmt_add = mysqli_prepare($conn, $sql_add);
    // $ (dollar sign) variable marker. stmt_add (s t m t underscore a d d) is 
    // the handle for the creation tool object. = (equals sign) assignment. 
    // mysqli_prepare (prepare) pre-compiles the command blueprint. Pre-compiling 
    // (pre compiling) locks the structural shape of the INSERT command in the 
    // database before any data is added. ( (opening bracket) $ (dollar sign) 
    // conn (bridge handle) , (comma) $ (dollar sign) sql_add (the command 
    // blueprint) ) (closing bracket). ; (semicolon).
    // machine already knows the exact shape of the instruction. ( (opening 
    // bracket) $ (dollar sign) conn (bridge) , (comma) $ (dollar sign) 
    // sql_add (command) ) (closing bracket). ; (semicolon).
    
    mysqli_stmt_bind_param($stmt_add, "ssssdi", $from, $to, $date, $time, $cost, $bus_id);
    // mysqli_stmt_bind_param attaches the 4 strings, 1 decimal, and 1 integer to 
    // the query. Binding (binding) is the process of safely pouring the 
    // user's data into the pre-compiled blueprint holes (?). This ensures 
    // the data is treated only as text or numbers, never as a command. 
    // ( (opening bracket) $ (dollar sign) stmt_add (handle) , (comma) 
    // "ssssdi" (types) , (comma) $ (dollar sign) from , (comma) $ (dollar sign) 
    // to , (comma) $ (dollar sign) date , (comma) $ (dollar sign) time 
    // , (comma) $ (dollar sign) cost , (comma) $ (dollar sign) bus_id 
    // ) (closing bracket). ; (semicolon).
    
    mysqli_stmt_execute($stmt_add);
    // mysqli_stmt_execute runs the insertion command. ( (opening bracket) 
    // $ (dollar sign) stmt_add (handle) ) (closing bracket). ; (semicolon).

    mysqli_stmt_close($stmt_add);
    // mysqli_stmt_close releases resources. ( (opening bracket) $ (dollar sign) 
    // stmt_add (handle) ) (closing bracket). ; (semicolon).

    header("Location: view_routes.php?msg=Success: Route created.");
    // header redirection with success message. ; (semicolon).

    exit();
    // exit stops script. ; (semicolon).
}
// } (closing curly bracket).

// --- ACTION 3: UPDATE ROUTE (INLINE) ---
if (isset($_POST['update_route'])) {
// if (if) check for the route update form submission. ( (opening bracket) 
// isset (is set) ( $ (dollar sign) _ (underscore) POST [ 'update_route' ] ) 
// (bracket). ) (closing bracket). { (opening curly bracket).

    $route_id = $_POST['route_id'];
    // $ (dollar sign) variable. route_id label. = (equals sign) assignment. ; (semicolon).

    $from     = $_POST['from_location'];
    // $ (dollar sign) variable. from label. = (equals sign). ; (semicolon).

    $to       = $_POST['to_location'];
    // $ (dollar sign) variable. to label. = (equals sign). ; (semicolon).

    $date     = $_POST['departure_date'];
    // $ (dollar sign) variable. date label. = (equals sign). ; (semicolon).

    $time     = $_POST['departure_time'];
    // $ (dollar sign) variable. time label. = (equals sign). ; (semicolon).

    $cost     = $_POST['cost'];
    // $ (dollar sign) variable. cost label. = (equals sign). ; (semicolon).

    $bus_id   = $_POST['bus_id'];
    // $ (dollar sign) variable. bus_id label. = (equals sign). ; (semicolon).

    $sql_upd = "UPDATE routes SET from_location=?, to_location=?, departure_date=?, departure_time=?, cost=?, bus_id=? WHERE route_id=?";
    // $ (dollar sign) variable. sql_upd label. = (equals sign). "UPDATE..." (quote). ; (semicolon).

    $stmt_upd = mysqli_prepare($conn, $sql_upd);
    // $ (dollar sign) variable. stmt_upd handle. = (equals sign). mysqli_prepare 
    // pre-compiles the update. Pre-compiling (pre compiling) means the machine 
    // creates a structural blueprint of the command before any data is added. 
    // This stops hackers from injecting malicious code because the machine 
    // already knows the exact shape of the instruction. ( (opening bracket) 
    // $ (dollar sign) conn (bridge) , (comma) $ (dollar sign) sql_upd (command) 
    // ) (closing bracket). ; (semicolon).
    
    mysqli_stmt_bind_param($stmt_upd, "ssssdii", $from, $to, $date, $time, $cost, $bus_id, $route_id);
    // mysqli_stmt_bind_param attaches the data to the placeholders. Binding 
    // (binding) is the process of safely pouring the user's data into the 
    // pre-compiled blueprint holes (?). This ensures the data is treated only 
    // as text or numbers, never as a command. ( (opening bracket) $ (dollar sign) 
    // stmt_upd (handle) , (comma) "ssssdii" (types) , (comma) $ (dollar sign) 
    // from , (comma) $ (dollar sign) to , (comma) $ (dollar sign) date 
    // , (comma) $ (dollar sign) time , (comma) $ (dollar sign) cost 
    // , (comma) $ (dollar sign) bus_id , (comma) $ (dollar sign) route_id 
    // ) (closing bracket). ; (semicolon).
    
    mysqli_stmt_execute($stmt_upd);
    // mysqli_stmt_execute runs the update command. ( (opening bracket) $ (dollar sign) 
    // stmt_upd (handle) ) (closing bracket). ; (semicolon).

    mysqli_stmt_close($stmt_upd);
    // mysqli_stmt_close releases resources. ( (opening bracket) $ (dollar sign) 
    // stmt_upd (handle) ) (closing bracket). ; (semicolon).
    
    header("Location: view_routes.php?msg=Success: Route updated.");
    // header redirection with success message. ; (semicolon).

    exit();
    // exit stops script. ; (semicolon).
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
                // toggleRouteEdit function (toggle route edit) switches between label view and input view for route details.
                function toggleRouteEdit(rid) {
                    // Find elements with classes 'view-route-ID' and 'edit-route-ID'.
                    var views = document.querySelectorAll('.view-route-' + rid);
                    var edits = document.querySelectorAll('.edit-route-' + rid);
                    
                    // Toggle visibility of spans and inputs.
                    views.forEach(v => v.style.display = (v.style.display === 'none' ? 'inline' : 'none'));
                    edits.forEach(e => e.style.display = (e.style.display === 'none' ? 'inline' : 'none'));
                    
                    // Toggle the row action buttons.
                    document.getElementById('route-ops-main-' + rid).style.display = 
                        (document.getElementById('route-ops-main-' + rid).style.display === 'none' ? 'inline-block' : 'none');
                    document.getElementById('route-ops-save-' + rid).style.display = 
                        (document.getElementById('route-ops-save-' + rid).style.display === 'none' ? 'inline-block' : 'none');
                }

                // Front-end validation using simple string checks (No Regex)
                function validateForm() {
                    var from = document.getElementById("from_location").value.trim();
                    var to = document.getElementById("to_location").value.trim();
                    if (from == "" || to == "") { alert("Please enter valid path."); return false; }
                    return true;
                }
            </script>

            <table class="crud-table">
                <thead><tr><th>Ref ID</th><th>From</th><th>To</th><th>Date</th><th>Time</th><th>Fleet</th><th>Cost</th><th>Operations</th></tr></thead>
                <tbody>
                    <?php 
                    // Re-fetch buses for the inline dropdowns.
                    $buses_list = [];
                    mysqli_data_seek($buses_res, 0);
                    while($b = mysqli_fetch_assoc($buses_res)) { $buses_list[] = $b; }

                    $sql_routes = "SELECT r.*, b.bus_name, SUBSTRING_INDEX(r.from_location, ', ', -1) as country FROM routes r JOIN buses b ON r.bus_id = b.bus_id ORDER BY country ASC, r.departure_date ASC";
                    $res_routes = mysqli_query($conn, $sql_routes);
                    /* $res_routes (result list) = (assignment). 
                       mysqli_query (MySQL Improved query) is the command that sends 
                       the instruction to the database server. "Improved" (mysqli) is used 
                       everywhere in the system to ensure modern security and 
                       faster performance. ( starts. $conn (bridge) , (comma) 
                       $sql_routes (the instruction) ) ends. ; (semicolon). */
                    $current_country = "";
                    while($row = mysqli_fetch_assoc($res_routes)): 
                        /* while (while) starts a loop. $row (row container) pulls data. 
                           mysqli_fetch_assoc (fetch associative) converts raw data into labeled pieces. 
                           ( starts. $res_routes (result source). ) ends. : (colon) starts the loop block. */
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
                            
                            <!-- From -->
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= htmlspecialchars($row['from_location']) ?></span>
                                <!-- html (HyperText) special (special) chars (characters) is a security tool 
                                     that converts dangerous symbols like < into safe text so hackers cannot 
                                     run scripts. ( starts the tool. $row (row variable) ['from_location'] (origin) 
                                     is the data being protected. ) ends the tool. -->

                                <input type="text" name="from_location" value="<?= htmlspecialchars($row['from_location']) ?>" class="edit-route-<?= $rid ?> table-input" style="display:none;">
                                <!-- value = [echo] htmlspecialchars (security tool) ( $row ['from_location'] ) -->
                            </td>

                            <!-- To -->
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= htmlspecialchars($row['to_location']) ?></span>
                                <!-- htmlspecialchars (security tool) ( $row ['to_location'] (destination data) ) -->

                                <input type="text" name="to_location" value="<?= htmlspecialchars($row['to_location']) ?>" class="edit-route-<?= $rid ?> table-input" style="display:none;">
                                <!-- value = [echo] htmlspecialchars (security tool) ( $row ['to_location'] ) -->
                            </td>

                            <!-- Date -->
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= $row['departure_date'] ?></span>
                                <input type="text" name="departure_date" value="<?= $row['departure_date'] ?>" class="edit-route-<?= $rid ?> table-input" style="display:none; width: 100px;">
                            </td>

                            <!-- Time -->
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= $row['departure_time'] ?></span>
                                <input type="text" name="departure_time" value="<?= $row['departure_time'] ?>" class="edit-route-<?= $rid ?> table-input" style="display:none; width: 70px;">
                            </td>

                            <!-- Fleet -->
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= htmlspecialchars($row['bus_name']) ?></span>
                                <!-- htmlspecialchars (security tool) ( $row ['bus_name'] (vehicle identity) ) -->

                                <select name="bus_id" class="edit-route-<?= $rid ?> table-input" style="display:none;">
                                    <?php foreach($buses_list as $b): ?>
                                    <option value="<?= $b['bus_id'] ?>" <?= ($b['bus_id'] == $row['bus_id'] ? 'selected' : '') ?>><?= htmlspecialchars($b['bus_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <!-- Cost -->
                            <td>
                                <span class="view-route-<?= $rid ?>"><?= $row['cost'] ?></span>
                                <span class="edit-route-<?= $rid ?>" style="display:none;"><input type="text" name="cost" value="<?= $row['cost'] ?>" class="table-input" style="width: 80px;"> KES</span>
                            </td>

                            <td style="white-space: nowrap;">
                                 <!-- Main Operations -->
                                 <div id="route-ops-main-<?= $rid ?>">
                                 <!-- < (less than sign) div (box) id (identity) = "route-ops-main- [echo] $rid" (unique number) > starts the visible action box. -->

                                     <button type="button" class="action-btn btn-update" onclick="toggleRouteEdit(<?= $rid ?>)">Update</button>
                                     <!-- < (less than sign) button (clickable item) type (nature) = "button" (does not submit) 
                                          class (style) = "action-btn (standard look) btn-update (blue color)" 
                                          onclick (on click event) = "toggleRouteEdit (run the toggle tool) ( [echo] $rid (for this route) )" 
                                          > (greater than sign) Update (label) < / (slash) button > (ends item). -->

                                     <a href="?delete_route=<?= $rid ?>" class="action-btn btn-delete" onclick="return confirm('Delete this route?')">Delete</a>
                                     <!-- < (less than sign) a (anchor link) href (destination) = "?delete_route = [echo] $rid" (sends ID to URL) 
                                          class (style) = "action-btn (standard look) btn-delete (red color)" 
                                          onclick (on click event) = "return confirm (ask a question) ( 'Delete this route?' )" 
                                          > (greater than sign) Delete (label) < / (slash) a > (ends link). -->

                                 </div>
                                 
                                 <!-- Save/Cancel Operations -->
                                 <div id="route-ops-save-<?= $rid ?>" style="display:none;">
                                 <!-- < (less than sign) div (box) id (identity) = "route-ops-save- [echo] $rid" (unique number) 
                                      style (visual) = "display:none;" (starts hidden) > starts the edit action box. -->

                                     <button type="submit" name="update_route" class="action-btn btn-update">Update</button>
                                     <!-- < (less than sign) button (clickable item) type (nature) = "submit" (sends the form data) 
                                          name (server label) = "update_route" (tells PHP which logic to run) 
                                          class (style) = "action-btn (standard look) btn-update (blue color)" 
                                          > (greater than sign) Update (label) < / (slash) button > (ends item). -->

                                     <button type="button" class="action-btn btn-delete" onclick="toggleRouteEdit(<?= $rid ?>)">Cancel</button>
                                     <!-- < (less than sign) button (clickable item) type (nature) = "button" (does not submit) 
                                          class (style) = "action-btn (standard look) btn-delete (red color)" 
                                          onclick (on click event) = "toggleRouteEdit (run the toggle tool) ( [echo] $rid (for this route) )" 
                                          > (greater than sign) Cancel (label) < / (slash) button > (ends item). -->

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
