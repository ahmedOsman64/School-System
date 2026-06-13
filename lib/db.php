<?php
// ─────────────────────────────────────────
//  MySQL Database Configuration
// ─────────────────────────────────────────
define('DB_HOST',     'localhost');
define('DB_USER',     'root');       // change if you have a different user
define('DB_PASSWORD', '');           // change to your MySQL password
define('DB_NAME',     'school_systemdb');
define('DB_PORT',     3307);

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;color:#c00;padding:20px;">
          <strong>Database Connection Failed:</strong> ' . htmlspecialchars($conn->connect_error) . '
         </div>');
}

// Set charset to prevent encoding issues
$conn->set_charset('utf8mb4');
?>
