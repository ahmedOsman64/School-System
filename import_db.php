<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'school_systemdb';
$port = 3307;

// Create connection without database
$conn = new mysqli($host, $user, $pass, null, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
if ($conn->query("CREATE DATABASE IF NOT EXISTS $dbname")) {
    // echo "Database created successfully\n";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

$sql = file_get_contents('schema.sql');

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "Database schema imported successfully!";
} else {
    echo "Error importing database: " . $conn->error;
}

$conn->close();
?>
