<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password
$dbname = "stylish_db";

// Create connection and make sure the database exists.
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Create database if it doesn't exist
if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`")) {
    die(json_encode(['success' => false, 'message' => 'Failed to create database: ' . $conn->error]));
}

// Select the database
if (!$conn->select_db($dbname)) {
    die(json_encode(['success' => false, 'message' => 'Database selection failed: ' . $conn->error]));
}

// Set character set to UTF-8
$conn->set_charset("utf8mb4");
?>
