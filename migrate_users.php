<?php
require_once 'lib/db.php';

$sqls = [
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS status ENUM('active','inactive') NOT NULL DEFAULT 'active'",
    "ALTER TABLE users MODIFY COLUMN IF EXISTS role ENUM('super_admin','admin','staff','finance') NOT NULL DEFAULT 'staff'",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('super_admin','admin','staff','finance') NOT NULL DEFAULT 'staff'",
];

foreach ($sqls as $sql) {
    if ($conn->query($sql)) {
        echo "<p style='color:green'>✔ OK: $sql</p>";
    } else {
        echo "<p style='color:red'>✘ Error: " . $conn->error . " — $sql</p>";
    }
}
echo "<p><strong>Done.</strong> You can delete this file now.</p>";
?>
