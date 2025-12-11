<?php
// Database connection settings
$host = "localhost"; // Hostname of the MySQL server
$dbname = "projectdb";
$username = "root";
$password = "";

// Connect using PDO (secure way) which prevent SQL injection.
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Enable PDO error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, display an error and stop execution
    die("Database connection failed: " . $e->getMessage());
}
?>
