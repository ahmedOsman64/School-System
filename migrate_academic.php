<?php
require_once 'lib/db.php';

$sql = file_get_contents('academic_tables.sql');

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "Academic tables created successfully!\n";
} else {
    echo "Error creating academic tables: " . $conn->error . "\n";
}

$conn->close();
?>
