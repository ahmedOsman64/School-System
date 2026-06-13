<?php
require_once 'lib/db.php';

$sql = "ALTER TABLE subjects ADD COLUMN class_id INT(11) AFTER subject_name";
if ($conn->query($sql)) {
    echo "Column class_id added.\n";
    $sql_fk = "ALTER TABLE subjects ADD CONSTRAINT fk_subject_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE";
    if ($conn->query($sql_fk)) {
        echo "Foreign key added.\n";
    } else {
        echo "Error adding FK: " . $conn->error . "\n";
    }
} else {
    echo "Error adding column: " . $conn->error . "\n";
}
$conn->close();
?>
