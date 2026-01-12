<?php
/**
 * PSM System - Authentication Check
 * Include this file at the top of protected pages
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure database configuration is loaded for BASE_URL
require_once dirname(__DIR__) . '/config/database.php';

/**
 * Check if user is logged in
 * @param string|null $required_role - Optional role requirement ('mother', 'professional', 'admin')
 */
function requireLogin($required_role = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "/frontend/views/auth/login.php");
        exit();
    }
    
    if ($required_role !== null && $_SESSION['role'] !== $required_role) {
        // Redirect to appropriate dashboard based on actual role
        redirectToDashboard($_SESSION['role']);
        exit();
    }
}

/**
 * Redirect user to their role-specific dashboard
 * @param string $role - User role
 */
function redirectToDashboard($role) {
    $base = BASE_URL . '/frontend/views/dashboard/';
    
    switch ($role) {
        case 'mother':
            header("Location: {$base}mother.php");
            break;
        case 'professional':
            header("Location: {$base}professional.php");
            break;
        case 'admin':
            header("Location: {$base}admin.php");
            break;
        default:
            header("Location: " . BASE_URL . "/frontend/views/auth/login.php");
    }
    exit();
}


/**
 * Check if user is logged in (returns boolean)
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user's role
 * @return string|null
 */
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current user's name
 * @return string|null
 */
function getUserName() {
    return $_SESSION['name'] ?? null;
}

/**
 * Get current user's ID
 * @return int|null
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}
?>



