<?php
/**
 * PSM System - Database Configuration
 */

// Aktifkan error reporting untuk kita nampak punca blank screen
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Database Configuration - Try multiple naming conventions used by Railway
$host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: getenv('DATABASE_HOST');
$user = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: getenv('DATABASE_USER');
$password = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('DATABASE_PASSWORD');
$database = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: getenv('DATABASE_NAME');
$port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: 3306;

// If we are on Railway but variables are still missing
if (getenv('RAILWAY_ENVIRONMENT') && !$host) {
    // List available ENVs (safe ones only) to help debug
    $available_envs = array_keys(getenv());
    die("❌ Error: Railway detected but DB variables missing. Found ENVs: " . implode(', ', array_filter($available_envs, fn($k) => strpos($k, 'MYSQL') === false)));
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


// Define Base Paths - Paling stabil guna realpath
if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/../../'));
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

