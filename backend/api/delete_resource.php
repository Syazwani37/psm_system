<?php
/**
 * PSM System - Delete Resource Handler
 * Backend API for deleting resources
 */

require_once '../config/database.php';
require_once '../helpers/functions.php';
require_once '../includes/auth_check.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Only Professionals can delete
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'professional') {
    http_response_code(403);
    die('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resource_id'])) {
    $id = intval($_POST['resource_id']);
    
    // 1. Get file path
    $stmt = $conn->prepare("SELECT file_path FROM resources WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $filePathRelative = $row['file_path'];
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/psm_system' . $filePathRelative;
        
        // 2. Delete file from server
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        
        // 3. Delete record from database
        $delStmt = $conn->prepare("DELETE FROM resources WHERE id = ?");
        $delStmt->bind_param("i", $id);
        
        if ($delStmt->execute()) {
            setFlashMessage('success', 'Resource deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete database record.');
        }
        $delStmt->close();
    } else {
        setFlashMessage('error', 'Resource not found.');
    }
    $stmt->close();
    
    header("Location: " . BASE_URL . "/frontend/views/resources/library.php");
    exit();
}
?>
