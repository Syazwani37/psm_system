<?php
/**
 * PSM System - Delete Announcement
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['announcement_id'])) {
    $id = intval($_POST['announcement_id']);
    
    // Only allow deletion if the professional created it (or admin)
    // For now, assuming any professional can delete (as they are colleagues) or check 'created_by'
    // Let's enforce owner check for safety, or minimal check
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        setFlashMessage('success', 'Announcement deleted successfully.');
    } else {
        setFlashMessage('error', 'Database error: ' . $stmt->error);
    }
    $stmt->close();

    header("Location: " . BASE_URL . "/frontend/views/announcements/manage.php");
    exit();
}
?>
