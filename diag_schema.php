<?php
require_once 'c:/Users/Admin/Desktop/UDA/Project/IBBS_PROTOTYPE/pages/db_connection.php';
$res = $conn->query("DESCRIBE bookings");
if ($res) {
    while($row = $res->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")";
        if ($row['Null'] === 'NO') echo " NOT NULL";
        if ($row['Extra']) echo " [" . $row['Extra'] . "]";
        echo "\n";
    }
} else {
    echo "Query failed: " . $conn->error;
}
?>
