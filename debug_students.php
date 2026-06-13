<?php
require_once 'lib/db.php';
$res = $conn->query("SELECT * FROM students");
if (!$res) {
    echo "Query Error: " . $conn->error;
} else {
    echo "Found " . $res->num_rows . " students.\n";
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
}
$conn->close();
?>
