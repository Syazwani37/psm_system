<?php
/**
 * PSM System - Database Configuration
 * Backend Configuration File
 */

$host = getenv('MYSQLHOST') ?: 'localhost';
$user = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: ''; // Default Laragon password is empty
$database = getenv('MYSQLDATABASE') ?: 'psm_system';
$port = getenv('MYSQLPORT') ?: 3306;

// Create connection
$conn = mysqli_connect($host, $user, $password, $database, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");
?>
