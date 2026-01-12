<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
/**
 * PSM System - Save Professional Action on Symptom Log
 * API Endpoint: POST /backend/api/symptom_checker/save_log_action.php
 */

header('Content-Type: application/json');


require_once BASE_PATH . '/backend/includes/auth_check.php';

// Ensure user is logged in as professional
if (!isLoggedIn() || $_SESSION['user']['role'] !== 'professional') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['log_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$log_id = intval($data['log_id']);
$notes = isset($data['notes']) ? trim($data['notes']) : '';
$action_status = isset($data['action_status']) ? $data['action_status'] : 'reviewed';
$professional_id = $_SESSION['user']['id'];

// Validate action_status
$valid_statuses = ['pending', 'reviewed', 'follow_up_required'];
if (!in_array($action_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid action status']);
    exit;
}

// Update the symptom log
$sql = "UPDATE symptom_logs SET 
        professional_notes = ?, 
        reviewed_by = ?, 
        reviewed_at = NOW(), 
        action_status = ? 
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'sisi', $notes, $professional_id, $action_status, $log_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'success' => true, 
        'message' => 'Action saved successfully',
        'data' => [
            'log_id' => $log_id,
            'action_status' => $action_status,
            'reviewed_at' => date('Y-m-d H:i:s')
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
?>
