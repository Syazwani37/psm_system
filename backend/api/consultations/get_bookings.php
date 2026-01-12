<?php
/**
 * PSM System - Get Professional Bookings API
 * Fetches consultations assigned to the logged-in professional.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professional') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $professional_id = $_SESSION['user_id'];

    // Fetch consultations with patient name
    $sql = "SELECT c.*, u.name as patient_name 
            FROM consultations c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.professional_id = $professional_id 
            ORDER BY c.scheduled_at ASC";

    $result = mysqli_query($conn, $sql);
    $bookings = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $bookings[] = $row;
        }
        echo json_encode(['success' => true, 'bookings' => $bookings]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Query failed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
}
?>
