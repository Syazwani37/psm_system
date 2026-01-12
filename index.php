<?php
/**
 * PSM System - Entry Point
 * Redirects to login page
 */

// Debug: Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'backend/config/database.php';
    header("Location: " . BASE_URL . "/frontend/views/auth/login.php");
    exit();
} catch (Exception $e) {
    echo "<h1>Critical Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>



