<?php
/**
 * PSM System - Mark Notification as Read API
 * Marks a specific notification as read.
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

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['notification_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing notification ID']);
    exit;
}

try {
    $user_id = $_SESSION['user_id'];
    $notification_id = (int)$data['notification_id'];
    
    // Update notification as read for the specific user
    $sql = "UPDATE notifications 
            SET is_read = TRUE 
            WHERE id = $notification_id AND user_id = $user_id";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
}
?>