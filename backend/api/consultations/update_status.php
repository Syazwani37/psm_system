<?php
/**
 * PSM System - Update Consultation Status API
 * Handles accepting, rejecting, or rescheduling a consultation.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

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

try {
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
        // Fetch patient ID to send notification
        $query_patient = mysqli_query($conn, "SELECT user_id, scheduled_at FROM consultations WHERE id = $consultation_id");
        if ($patient = mysqli_fetch_assoc($query_patient)) {
            $patient_id = $patient['user_id'];
            $notif_message = "";
            
            if ($status === 'accepted') {
                $notif_message = "Your consultation request has been accepted by the professional.";
            } elseif ($status === 'rescheduled') {
                $new_time = date('M j, g:i A', strtotime($scheduled_at));
                $notif_message = "Your consultation has been rescheduled to $new_time.";
            }

            if ($notif_message) {
                // Insert notification
                $notif_sql = "INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($patient_id, '$notif_message', 0, NOW())";
                mysqli_query($conn, $notif_sql);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Consultation updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . mysqli_error($conn)]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
}
?>
