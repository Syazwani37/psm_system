<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Create Announcement
 * Backend API
 */

require_once '../../config/database.php';
require_once '../../helpers/functions.php';
require_once '../../includes/auth_check.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Only Professionals
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'professional') {
    http_response_code(403);
    die('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'], $_POST['message'], $_POST['type'])) {
    $title = sanitize($_POST['title']);
    $message = sanitize($_POST['message']); // Allow basic formatting if needed, but sanitize effectively
    $type = sanitize($_POST['type']);
    $created_by = $_SESSION['user_id'];

    if (empty($title) || empty($message)) {
        setFlashMessage('error', 'Title and message are required.');
        header("Location: " . BASE_URL . "/frontend/views/announcements/manage.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO announcements (title, message, created_by, type, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssis", $title, $message, $created_by, $type);

    if ($stmt->execute()) {
        setFlashMessage('success', 'Announcement posted successfully.');
    } else {
        setFlashMessage('error', 'Database error: ' . $stmt->error);
    }
    $stmt->close();

    header("Location: " . BASE_URL . "/frontend/views/announcements/manage.php");
    exit();
}
?>
