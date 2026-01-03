<?php
/**
 * PSM System - Professional Dashboard
 * Frontend View: Dashboard Module
 */

$page_title = "Professional Dashboard - PSM System";
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/helpers/functions.php';

// Require professional role
requireLogin('professional');

require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/header.php';

$userName = getUserName();

// Fetch recent symptom logs
$sql_logs = "SELECT s.*, u.name as patient_name 
             FROM symptom_logs s 
             JOIN users u ON s.user_id = u.id 
             ORDER BY s.created_at DESC LIMIT 5";
$result_logs = mysqli_query($conn, $sql_logs);
?>

<style>
    .dashboard-wrapper {
        max-width: 1100px;
        margin: 0 auto;
        padding: 1.5rem;
    }

    .feature-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        color: inherit;
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .welcome-card {
        background: linear-gradient(135deg, #E0F2F1, #FFF);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .log-card {
        background: #FAFAFA;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid rgba(0,0,0,0.05);
    }
</style>

<!-- Decorative Blobs -->
<div class="blob blob-1" style="background: var(--info); opacity: 0.3; width: 500px; height: 500px;"></div>
<div class="blob blob-2" style="background: var(--primary-light); opacity: 0.3;"></div>

<div class="dashboard-wrapper">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center py-3 mb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2 bg-white rounded-pill px-3 py-2 shadow-sm">
                <div style="width: 40px; height: 40px; background: #E0F7FA; color: #006064; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                    <?php echo getInitials($userName); ?>
                </div>
                <div>
                    <div class="fw-bold small"><?php echo escape($userName); ?></div>
                    <div class="text-muted" style="font-size: 0.75rem;">Healthcare Specialist</div>
                </div>
            </div>
        </div>
        <a href="<?php echo BASE_URL; ?>/frontend/views/auth/logout.php" class="btn btn-outline-danger">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
    </header>

    <!-- Welcome Card -->
    <div class="welcome-card">
        <div class="d-flex gap-4 align-items-center flex-wrap">
            <div style="width: 100px; height: 100px; border-radius: 50%; background: #B2DFDB; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                👩‍⚕️
            </div>
            <div>
                <h2 class="mb-2" style="color: #00695C;">
                    Welcome, <?php echo escape($userName); ?> 👋
                </h2>
                <p class="text-muted mb-3">Manage your patients and monitor their recovery progress.</p>
                <span class="badge px-3 py-2" style="background: #B2DFDB; color: #004D40;">
                    <i class="fas fa-user-md me-1"></i> Healthcare Specialist
                </span>
            </div>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="row g-4 mb-4">
        <!-- Patient Management -->
        <div class="col-md-6 col-lg-4">
            <a href="<?php echo BASE_URL; ?>/frontend/views/patient_management/index.php" class="feature-card d-block">
                <div class="feature-icon" style="background: #E3F2FD;">
                    <i class="fas fa-users-cog fa-lg" style="color: #1565C0;"></i>
                </div>
                <h5 class="mb-2">Patient Management</h5>
                <p class="text-muted small mb-3">View and manage your patients' progress and recovery plans.</p>
                <span style="color: #1565C0; font-weight: 600;">
                    Manage Patients <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Analytics -->
        <div class="col-md-6 col-lg-4">
            <a href="<?php echo BASE_URL; ?>/frontend/views/dashboard/analytics.php" class="feature-card d-block">
                <div class="feature-icon" style="background: #E0F7FA;">
                    <i class="fas fa-chart-pie fa-lg" style="color: #006064;"></i>
                </div>
                <h5 class="mb-2">Analytics Dashboard</h5>
                <p class="text-muted small mb-3">Access recovery data and clinical trends.</p>
                <span style="color: #006064; font-weight: 600;">
                    View Analytics <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Resource Library -->
        <div class="col-md-6 col-lg-4">
            <a href="<?php echo BASE_URL; ?>/frontend/views/resources/library.php" class="feature-card d-block">
                <div class="feature-icon" style="background: #FFF3E0;">
                    <i class="fas fa-book-medical fa-lg" style="color: #EF6C00;"></i>
                </div>
                <h5 class="mb-2">Resource Library</h5>
                <p class="text-muted small mb-3">Upload and manage expert recovery guides.</p>
                <span style="color: #EF6C00; font-weight: 600;">
                    Manage Resources <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Communication -->
        <div class="col-md-6 col-lg-4">
            <a href="<?php echo BASE_URL; ?>/frontend/views/community/index.php" class="feature-card d-block">
                <div class="feature-icon" style="background: #F3E5F5;">
                    <i class="fas fa-comments fa-lg" style="color: #7B1FA2;"></i>
                </div>
                <h5 class="mb-2">Communication</h5>
                <p class="text-muted small mb-3">Secure messaging with mothers and staff.</p>
                <span style="color: #7B1FA2; font-weight: 600;">
                    Open Messenger <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Consultation Requests -->
        <div class="col-md-6 col-lg-4">
            <a href="<?php echo BASE_URL; ?>/frontend/views/consultations/manage.php" class="feature-card d-block">
                <div class="feature-icon" style="background: #FFEBEE;">
                    <i class="fas fa-calendar-check fa-lg" style="color: #C62828;"></i>
                </div>
                <h5 class="mb-2">Consultation Requests</h5>
                <p class="text-muted small mb-3">Review and manage pending booking requests.</p>
                <span style="color: #C62828; font-weight: 600;">
                    View Requests <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>
    </div>

    <!-- Recent Symptom Logs -->
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">
                <i class="fas fa-clipboard-list me-2" style="color: var(--primary-color);"></i>
                Recent Symptom Logs
            </h5>
            <a href="<?php echo BASE_URL; ?>/frontend/views/patient_management/index.php" class="btn btn-outline-secondary btn-sm">View All</a>
        </div>

        <div class="d-flex flex-column gap-3">
            <?php if (mysqli_num_rows($result_logs) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_logs)): ?>
                    <?php
                    $statusClass = '';
                    $statusText = '';
                    
                    if ($row['result_status'] == 'danger') {
                        $statusClass = 'background: #FFEBEE; color: #C62828;';
                        $statusText = 'Needs Attention';
                    } elseif ($row['result_status'] == 'warning') {
                        $statusClass = 'background: #E0F7FA; color: #006064;';
                        $statusText = 'Monitoring';
                    } else {
                        $statusClass = 'background: #E8F5E9; color: #2E7D32;';
                        $statusText = 'Stable';
                    }
                    
                    $initials = getInitials($row['patient_name']);
                    ?>
                    <div class="log-card d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3 align-items-center">
                            <div style="width: 40px; height: 40px; background: #E0F2F1; color: #00695C; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                <?php echo $initials; ?>
                            </div>
                            <div>
                                <div class="fw-bold"><?php echo escape($row['patient_name']); ?></div>
                                <div class="text-muted small">
                                    Week <?php echo $row['week_postpartum']; ?> - Temp: <?php echo $row['temperature']; ?>°C
                                </div>
                            </div>
                        </div>
                        <span class="badge rounded-pill" style="<?php echo $statusClass; ?>">
                            <?php echo $statusText; ?>
                        </span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-muted py-4">No symptom logs found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/footer.php'; ?>


