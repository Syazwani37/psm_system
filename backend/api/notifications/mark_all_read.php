<?php
/**
 * PSM System - Mark All Notifications as Read API
 * Marks all notifications for the user as read.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/functions.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $user_id = $_SESSION['user_id'];
    
    // Update all notifications as read for the user
    $sql = "UPDATE notifications 
            SET is_read = TRUE 
            WHERE user_id = $user_id AND is_read = FALSE";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
}
?>