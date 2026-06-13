<?php
require_once 'lib/db.php';

$sql = file_get_contents('school_setup.sql');

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "School setup tables (school_info, academic_years) created successfully!\n";
} else {
    echo "Error creating school setup tables: " . $conn->error . "\n";
}

$conn->close();
?>
