<?php
/**
 * PSM System - Database Configuration
 * Backend Configuration File
 */

// Database Configuration
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$database = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT') ?: 3306;

// If we are on Railway but variables are missing
if (!$host && getenv('RAILWAY_ENVIRONMENT')) {
    die("❌ Error: Railway environment detected but MYSQLHOST is missing.");
}

// Defaults for local Laragon
$host = $host ?: '127.0.0.1';
$user = $user ?: 'root';
$password = $password ?: '';
$database = $database ?: 'psm_system';

// IMPORTANT: On Linux (Railway), if host is 'localhost', PHP tries to use a socket file.
// We want to force it to use TCP.
if ($host === 'localhost') {
    $host = '127.0.0.1';
}


// Define Base Paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

// BASE_URL detection
if (!defined('BASE_URL')) {
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '';
    define('BASE_URL', (strpos($script_name, '/psm_system') !== false) ? '/psm_system' : '');
}

// Create connection with error handling
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);
try {
    $conn = mysqli_connect($host, $user, $password, $database, $port);
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("❌ Database Connection Failed: " . $e->getMessage());
}

?>

