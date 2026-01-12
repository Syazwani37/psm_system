<?php
/**
 * PSM System - Database Configuration
 * Backend Configuration File
 */

// Database Configuration from Environment Variables (Railway) or Defaults (Local)
$host = getenv('MYSQLHOST') ?: 'localhost';
$user = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: ''; // Default Laragon password is empty
$database = getenv('MYSQLDATABASE') ?: 'psm_system';
$port = getenv('MYSQLPORT') ?: 3306;

// Define Base Paths
// BASE_PATH is the physical directory on the server
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

// BASE_URL is the URL path (e.g., /psm_system or /)
if (!defined('BASE_URL')) {
    // Detect if we are in a subdirectory (like /psm_system) or at the root
    $script_name = $_SERVER['SCRIPT_NAME'];
    $project_folder = '/psm_system';
    
    if (strpos($script_name, $project_folder) !== false) {
        define('BASE_URL', $project_folder);
    } else {
        define('BASE_URL', '');
    }
}

// Create connection
$conn = mysqli_connect($host, $user, $password, $database, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");
?>

