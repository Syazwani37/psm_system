<?php
/**
 * PSM System - Patient Management
 * Frontend View: Patient Management Module
 */

$page_title = "Patient Management - PSM System";
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/helpers/functions.php';

requireLogin('professional');

require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .patient-card {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
        border-left: 5px solid transparent;
    }

    .patient-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 150, 136, 0.1);
    }
    
    .patient-card.status-danger { border-left-color: #EF5350; }
    .patient-card.status-warning { border-left-color: #FFA726; }
    .patient-card.status-safe { border-left-color: #66BB6A; }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-warning { background: #FFF3E0; color: #EF6C00; }
    .badge-danger { background: #FFEBEE; color: #C62828; }
    .badge-success { background: #E8F5E9; color: #2E7D32; }
</style>

<!-- Bloom Decoration -->
<div class="blob blob-1" style="background: linear-gradient(135deg, #E0F2F1 0%, #B2DFDB 100%); top: -100px; left: -100px; opacity: 0.5;"></div>

<div class="page-container">
    <div class="text-center mb-5">
        <a href="<?php echo BASE_URL; ?>/frontend/views/dashboard/professional.php" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
        <h1 class="mb-2" style="font-family: 'Playfair Display', serif; color: #00695C;">
            <i class="fas fa-users-cog me-2" style="color: #80CBC4;"></i> Patient Management
        </h1>
        <p class="text-muted">Monitor patient health status and alerts.</p>
    </div>

    <div class="d-flex flex-column gap-3">
        <?php
        // Fetch latest log for each patient using correlated subquery logic (or MAX ID logic)
        $sql = "SELECT s.*, u.name as patient_name 
                FROM symptom_logs s 
                JOIN users u ON s.user_id = u.id 
                WHERE s.id IN (
                    SELECT MAX(id) 
                    FROM symptom_logs 
                    GROUP BY user_id
                )
                ORDER BY s.created_at DESC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $statusBadge = '';
                $message = '';
                $cardStatusClass = '';

                if ($row['result_status'] == 'danger') {
                    $statusBadge = '<span class="status-badge badge-danger">Needs Attention</span>';
                    $message = "Patient reported critical symptoms (Fever: " . escape($row['temperature']) . "°C, Pain: " . escape($row['pain_level']) . "). Immediate follow-up recommended.";
                    $cardStatusClass = 'status-danger';
                } elseif ($row['result_status'] == 'warning') {
                    $statusBadge = '<span class="status-badge badge-warning">Monitoring</span>';
                    $message = "Patient reported mild concerns. Monitor closely.";
                    $cardStatusClass = 'status-warning';
                } else {
                    $statusBadge = '<span class="status-badge badge-success">Stable</span>';
                    $message = "Symptoms within normal range. Patient is advised to continue regular monitoring and self-care.";
                    $cardStatusClass = 'status-safe';
                }
        ?>
            <div class="patient-card <?php echo $cardStatusClass; ?>">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0 fw-bold" style="color: #00796B;">
                        <i class="fas fa-user-circle me-2"></i> <?php echo escape($row['patient_name']); ?>
                    </h5>
                    <?php echo $statusBadge; ?>
                </div>
                
                <p class="text-secondary mb-2">
                    <strong class="text-dark">Week <?php echo escape($row['week_postpartum']); ?>:</strong> 
                    <?php echo escape($message); ?>
                </p>
                
                <small class="text-muted">
                    <i class="far fa-clock me-1"></i> Last Updated: <?php echo formatDateTime($row['created_at']); ?>
                </small>
                
                <div class="mt-3 pt-3 border-top d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-outline-primary rounded-pill">View History</a>
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill">Contact Patient</a>
                </div>
            </div>
        <?php
            }
        } else {
            echo '
            <div class="text-center py-5 text-muted">
                <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                <p>No patient records found.</p>
            </div>';
        }
        ?>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/footer.php'; ?>


