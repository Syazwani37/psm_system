<?php
/**
 * PSM System - Database Configuration
 * Backend Configuration File
 */

$host = 'localhost';
$user = 'root';
$password = ''; // Default Laragon password is empty
$database = 'psm_system';
$port = 3306;

// Create connection
$conn = mysqli_connect($host, $user, $password, $database, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");
?>
