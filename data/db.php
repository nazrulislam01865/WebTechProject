<?php
// Database connection details (XAMPP defaults)
$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "gobus";

// Create connection without specifying database
$conn = new mysqli($servername, $username, $password_db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if database exists, create if not
$result = $conn->query("SHOW DATABASES LIKE '$dbname'");
if ($result->num_rows == 0) {
    $sql = "CREATE DATABASE $dbname";
    if (!$conn->query($sql)) {
        die("Error creating database: " . $conn->error);
    }
}

// Select the database
$conn->select_db($dbname);

// Check if users table exists, create if not
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows == 0) {
    $sql = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(20) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(11) NOT NULL UNIQUE,
        nid VARCHAR(10) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )";
    if (!$conn->query($sql)) {
        die("Error creating users table: " . $conn->error);
    }
}

// Return the connection
return $conn;
?>