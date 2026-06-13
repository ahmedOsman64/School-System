<?php
require_once 'lib/db.php';

$sql = file_get_contents('student_tables.sql');

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "Student tables created successfully!\n";
} else {
    echo "Error creating student tables: " . $conn->error . "\n";
}

$conn->close();
?>
