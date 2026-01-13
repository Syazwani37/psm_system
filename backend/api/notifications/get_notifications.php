<?php
/**
 * PSM System - Get Notifications API
 * Fetches notifications for the logged-in user.
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
    
    // Fetch notifications for the user, ordered by newest first
    $sql = "SELECT id, message, is_read, created_at 
            FROM notifications 
            WHERE user_id = $user_id 
            ORDER BY created_at DESC 
            LIMIT 20"; // Limit to last 20 notifications
    
    $result = mysqli_query($conn, $sql);
    $notifications = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
    
    echo json_encode([
        'success' => true, 
        'notifications' => $notifications
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
}
?>