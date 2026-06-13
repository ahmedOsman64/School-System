<?php
require_once 'lib/db.php';

$sqls = [
    // Rename fullname → full_name
    "ALTER TABLE teachers CHANGE COLUMN fullname full_name VARCHAR(255) NOT NULL",
    // Rename subject → qualification
    "ALTER TABLE teachers CHANGE COLUMN subject qualification VARCHAR(100) NOT NULL",
    // Add new columns
    "ALTER TABLE teachers ADD COLUMN IF NOT EXISTS employee_no VARCHAR(50) NOT NULL DEFAULT '' AFTER id",
    "ALTER TABLE teachers ADD COLUMN IF NOT EXISTS gender VARCHAR(20) NOT NULL DEFAULT '' AFTER full_name",
    "ALTER TABLE teachers ADD COLUMN IF NOT EXISTS email VARCHAR(255) NOT NULL DEFAULT '' AFTER phone",
    "ALTER TABLE teachers ADD COLUMN IF NOT EXISTS hire_date DATE NULL AFTER email",
    "ALTER TABLE teachers ADD COLUMN IF NOT EXISTS salary DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER hire_date",
];

foreach ($sqls as $sql) {
    if ($conn->query($sql)) {
        echo "<p style='color:green'>✔ OK: <code>" . htmlspecialchars($sql) . "</code></p>";
    } else {
        echo "<p style='color:orange'>⚠ Skipped/Error: " . htmlspecialchars($conn->error) . "<br><code>" . htmlspecialchars($sql) . "</code></p>";
    }
}
echo "<p><strong>Done! You can delete this file now.</strong></p>";
?>
