<?php
/**
 * PSM System - Book Consultation API
 * Handles saving a new consultation request to the database.
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

if (!$data || !isset($data['professional_id']) || !isset($data['scheduled_at'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $user_id = $_SESSION['user_id'];
    $professional_id = (int)$data['professional_id'];
    $scheduled_at = mysqli_real_escape_string($conn, $data['scheduled_at']);
    $reason = mysqli_real_escape_string($conn, $data['reason'] ?? 'Routine Checkup');
    $status = 'pending';

    // Insert into database
    $sql = "INSERT INTO consultations (user_id, professional_id, scheduled_at, reason, status)
            VALUES ($user_id, $professional_id, '$scheduled_at', '$reason', '$status')";

    if (mysqli_query($conn, $sql)) {
        // Get the user's name to include in the notification
        $user_result = mysqli_query($conn, "SELECT name FROM users WHERE id = $user_id");
        $user = mysqli_fetch_assoc($user_result);
        $user_name = $user['name'];

        // Format the scheduled date for the notification
        $formatted_date = date('M j, Y \a\t g:i A', strtotime($scheduled_at));

        // Create notification for the professional
        $notification_message = "New consultation request from $user_name on $formatted_date. Reason: $reason";
        $notification_sql = "INSERT INTO notifications (user_id, message, is_read) VALUES ($professional_id, '$notification_message', FALSE)";
        mysqli_query($conn, $notification_sql);

        echo json_encode(['success' => true, 'message' => 'Booking request sent! Professional has been notified.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
}
?>
