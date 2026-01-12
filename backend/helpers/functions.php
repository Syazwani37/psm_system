<?php
/**
 * PSM System - Helper Functions
 * Utility functions used across the application
 */

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Escape output for HTML display
 * @param string $text
 * @return string
 */
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Display flash message
 * @param string $type - 'success', 'error', 'warning', 'info'
 * @param string $message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message as Bootstrap alert
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $alertClass = match($flash['type']) {
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            default => 'alert-secondary'
        };
        
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo escape($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

/**
 * Format date for display
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}

/**
 * Format datetime for display
 * @param string $datetime
 * @return string
 */
function formatDateTime($datetime) {
    return date('d M Y, h:i A', strtotime($datetime));
}

/**
 * Get initials from name
 * @param string $name
 * @return string
 */
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }
    return substr($initials, 0, 2);
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get dashboard URL based on user role
 * @param string|null $role
 * @return string
 */
function getDashboardUrl($role = null) {
    if (!$role && isset($_SESSION['role'])) {
        $role = $_SESSION['role'];
    }

    switch ($role) {
        case 'mother':
            return BASE_URL . '/frontend/views/dashboard/mother.php';
        case 'professional':
            return BASE_URL . '/frontend/views/dashboard/professional.php';
        case 'admin':
            return BASE_URL . '/frontend/views/dashboard/admin.php';
        default:
            return BASE_URL . '/frontend/views/auth/login.php';
    }
}
?>
