<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Patient History
 * Frontend View: Patient History Log
 */

$page_title = "Patient History - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';

require_once BASE_PATH . '/backend/helpers/functions.php';

requireLogin('professional'); // Only professionals can view history

require_once BASE_PATH . '/backend/includes/header.php';

// Get Patient ID
$patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;

// Fetch Patient Name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$userResult = $stmt->get_result();
$patientName = ($userResult && $row = $userResult->fetch_assoc()) ? $row['name'] : 'Unknown Patient';
$stmt->close();
?>

<style>
    .page-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .history-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .status-badge {
        padding: 0.35em 0.8em;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .badge-danger { background: #FFEBEE; color: #D32F2F; }
    .badge-warning { background: #FFF3E0; color: #EF6C00; }
    .badge-success { background: #E8F5E9; color: #2E7D32; }
</style>

<div class="page-container">
    <div class="text-center mb-5">
        <a href="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/patient_management/index.php" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="fas fa-arrow-left me-2"></i> Back to Patient List
        </a>
        <h1 class="mb-2" style="font-family: 'Playfair Display', serif; color: #00695C;">
            Log History: <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo escape($patientName); ?>
        </h1>
        <p class="text-muted">Complete record of symptom checks.</p>
    </div>

    <div class="history-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #F9FAFB;">
                    <tr style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px;">
                        <th class="py-3 ps-4" style="color: #6B7280;">Date & Time</th>
                        <th class="py-3" style="color: #6B7280;">Week</th>
                        <th class="py-3" style="color: #6B7280;">Vitals</th>
                        <th class="py-3" style="color: #6B7280;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
                    $stmt = $conn->prepare("SELECT * FROM symptom_logs WHERE user_id = ? ORDER BY created_at DESC");
                    $stmt->bind_param("i", $patient_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $statusBadge = '';
                            if ($row['result_status'] == 'danger') {
                                $statusBadge = '<span class="status-badge badge-danger">Attention</span>';
                            } elseif ($row['result_status'] == 'warning') {
                                $statusBadge = '<span class="status-badge badge-warning">Monitor</span>';
                            } else {
                                $statusBadge = '<span class="status-badge badge-success">Stable</span>';
                            }
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-medium text-dark"><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo date('M d, Y', strtotime($row['created_at'])); ?></div>
                                    <div class="small text-muted"><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo date('h:i A', strtotime($row['created_at'])); ?></div>
                                </td>
                                <td>Week <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo escape($row['week_postpartum']); ?></td>
                                <td>
                                    <div><i class="fas fa-thermometer-half text-secondary me-1"></i> <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo escape($row['temperature']); ?>°C</div>
                                    <div><i class="fas fa-sad-tear text-secondary me-1"></i> Pain: <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo escape($row['pain_level']); ?>/10</div>
                                </td>
                                <td><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $statusBadge; ?></td>
                            </tr>
                            <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
                        }
                    } else {
                        echo '<tr><td colspan="4" class="text-center py-5 text-muted">No history found for this patient.</td></tr>';
                    }
                    $stmt->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>
