<?php
/**
 * PSM System - Admin Dashboard
 * Frontend View: Dashboard Module
 */

$page_title = "Admin Dashboard - PSM System";
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/helpers/functions.php';

// Require admin role
requireLogin('admin');

require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/header.php';

$userName = getUserName();

// Get statistics from database
$stats = [
    'mothers' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='mother'"))['count'] ?? 0,
    'professionals' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='professional'"))['count'] ?? 0,
    'symptom_logs' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM symptom_logs"))['count'] ?? 0,
];
?>

<style>
    .dashboard-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .welcome-card {
        background: linear-gradient(135deg, #F3E5F5, #FFF);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .report-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .report-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
</style>

<!-- Decorative Blobs -->
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="dashboard-wrapper">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center py-3 mb-3">
        <div class="d-flex align-items-center gap-3 bg-white rounded-pill px-3 py-2 shadow-sm">
            <div style="width: 40px; height: 40px; background: #E1BEE7; color: #4A148C; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                A
            </div>
            <div>
                <div class="fw-bold small">Admin</div>
                <div class="text-muted" style="font-size: 0.75rem;">Administrator</div>
            </div>
        </div>
        <a href="<?php echo BASE_URL; ?>/frontend/views/auth/logout.php" class="btn btn-outline-danger">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
    </header>

    <!-- Welcome Card -->
    <div class="welcome-card">
        <div class="d-flex gap-4 align-items-center flex-wrap">
            <div style="width: 100px; height: 100px; border-radius: 50%; background: #E1BEE7; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                🛡️
            </div>
            <div>
                <h2 class="mb-2" style="color: #4A148C;">
                    Welcome, <?php echo escape($userName); ?> 👋
                </h2>
                <p class="text-muted mb-3">Overview of system performance and health metrics.</p>
                <span class="badge px-3 py-2" style="background: #E1BEE7; color: #4A148C;">
                    <i class="fas fa-shield-alt me-1"></i> System Administrator
                </span>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: var(--primary-light); color: var(--primary-dark);">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success">Active</span>
                </div>
                <div class="stat-number"><?php echo $stats['mothers']; ?></div>
                <div class="text-muted">Total Mothers</div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: var(--secondary-light); color: var(--secondary-dark);">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success">Active</span>
                </div>
                <div class="stat-number"><?php echo $stats['professionals']; ?></div>
                <div class="text-muted">Professionals</div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: var(--info-bg); color: var(--info);">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <span class="badge bg-info-subtle text-info">Logged</span>
                </div>
                <div class="stat-number"><?php echo $stats['symptom_logs']; ?></div>
                <div class="text-muted">Symptom Logs</div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: var(--warning-bg); color: #D4A373;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning">Pending</span>
                </div>
                <div class="stat-number">-</div>
                <div class="text-muted">Pending Consultations</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Reports -->
        <div class="col-lg-8">
            <h5 class="mb-3">Recent Reports</h5>
            <div class="d-flex flex-column gap-3">
                <a href="<?php echo BASE_URL; ?>/frontend/views/reports/recovery_trends.php" class="report-card text-decoration-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3 align-items-center">
                            <div style="width: 50px; height: 50px; background: #F3E5F5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #9C27B0;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-dark">Weekly Recovery Trends</h6>
                                <small class="text-muted">Updated today</small>
                            </div>
                        </div>
                        <button class="btn btn-outline-secondary btn-sm">View Report</button>
                    </div>
                </a>

                <a href="<?php echo BASE_URL; ?>/frontend/views/reports/user_engagement.php" class="report-card text-decoration-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3 align-items-center">
                            <div style="width: 50px; height: 50px; background: #E3F2FD; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #1E88E5;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-dark">User Engagement Metrics</h6>
                                <small class="text-muted">Updated yesterday</small>
                            </div>
                        </div>
                        <button class="btn btn-outline-secondary btn-sm">View Report</button>
                    </div>
                </a>

                <a href="<?php echo BASE_URL; ?>/frontend/views/reports/nutrition.php" class="report-card text-decoration-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3 align-items-center">
                            <div style="width: 50px; height: 50px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #43A047;">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-dark">Nutrition Adherence</h6>
                                <small class="text-muted">Generate new report</small>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm">Generate</button>
                    </div>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <h5 class="mb-3">Quick Actions</h5>
            <div class="card p-3 mb-4">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-3 align-items-center p-2 rounded" style="cursor: pointer;" 
                         onmouseover="this.style.background='#F5F5F5'" onmouseout="this.style.background='transparent'">
                        <i class="fas fa-file-export" style="color: var(--primary-color);"></i>
                        <div>
                            <div class="fw-bold small">Export Report</div>
                            <small class="text-muted">Download as PDF</small>
                        </div>
                    </div>
                    <div class="d-flex gap-3 align-items-center p-2 rounded" style="cursor: pointer;"
                         onmouseover="this.style.background='#F5F5F5'" onmouseout="this.style.background='transparent'">
                        <i class="fas fa-user-plus" style="color: var(--primary-color);"></i>
                        <div>
                            <div class="fw-bold small">Add User</div>
                            <small class="text-muted">Register new user</small>
                        </div>
                    </div>
                    <div class="d-flex gap-3 align-items-center p-2 rounded" style="cursor: pointer;"
                         onmouseover="this.style.background='#F5F5F5'" onmouseout="this.style.background='transparent'">
                        <i class="fas fa-cog" style="color: var(--primary-color);"></i>
                        <div>
                            <div class="fw-bold small">System Settings</div>
                            <small class="text-muted">Configure account</small>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Recent Activity</h5>
            <div class="card p-3">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-3">
                        <div class="pt-1">
                            <div style="width: 10px; height: 10px; background: var(--secondary-color); border-radius: 50%;"></div>
                        </div>
                        <div>
                            <div class="fw-bold small">New user registered</div>
                            <small class="text-muted">Recent activity</small>
                            <div class="text-primary small mt-1">Just now</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="pt-1">
                            <div style="width: 10px; height: 10px; background: var(--primary-color); border-radius: 50%;"></div>
                        </div>
                        <div>
                            <div class="fw-bold small">Report submitted</div>
                            <small class="text-muted">Weekly report generated</small>
                            <div class="text-primary small mt-1">1 hour ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/footer.php'; ?>


