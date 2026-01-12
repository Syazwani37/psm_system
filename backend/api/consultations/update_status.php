<?php
/**
 * PSM System - Update Consultation Status API
 * Handles accepting, rejecting, or rescheduling a consultation.
 */

header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professional') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['consultation_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$consultation_id = (int)$data['consultation_id'];
$status = mysqli_real_escape_string($conn, $data['status']);
$professional_id = $_SESSION['user_id'];

// Initial SQL
$sql = "UPDATE consultations SET status = '$status'";

// If rescheduling, update scheduled_at
if (isset($data['scheduled_at'])) {
    $scheduled_at = mysqli_real_escape_string($conn, $data['scheduled_at']);
    $sql .= ", scheduled_at = '$scheduled_at'";
}

$sql .= " WHERE id = $consultation_id AND professional_id = $professional_id";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'message' => 'Consultation updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}
?>
