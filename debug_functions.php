<?php
require_once dirname(__FILE__, 1) . '/backend/config/database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Info</h1>";
echo "PHP Version: " . phpversion() . "<br>";

require_once dirname(__FILE__, 1) . '/backend/config/database.php';
echo "Database included.<br>";

require_once 'backend/helpers/functions.php';
echo "Functions included.<br>";

session_start();
$_SESSION['role'] = 'mother';

echo "Testing getDashboardUrl...<br>";
try {
    echo "URL: " . getDashboardUrl() . "<br>";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "Done.";
?>
