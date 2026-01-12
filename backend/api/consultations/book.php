<?php
/**
 * PSM System - Book Consultation API
 * Handles saving a new consultation request to the database.
 */

header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/helpers/functions.php';

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

$user_id = $_SESSION['user_id'];
$professional_id = (int)$data['professional_id'];
$scheduled_at = mysqli_real_escape_string($conn, $data['scheduled_at']);
$reason = mysqli_real_escape_string($conn, $data['reason'] ?? 'Routine Checkup');
$status = 'pending';

// Insert into database
$sql = "INSERT INTO consultations (user_id, professional_id, scheduled_at, reason, status) 
        VALUES ($user_id, $professional_id, '$scheduled_at', '$reason', '$status')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'message' => 'Booking request sent!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}
?>
