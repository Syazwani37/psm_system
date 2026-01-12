<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
/**
 * PSM System - User Engagement Report
 * Frontend View: Reports Module
 */

$page_title = "User Engagement - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';

requireLogin('admin');
require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .report-card {
        background: white;
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }

    .section-title {
        font-size: 1.2rem;
        margin-top: 1.5rem;
        color: #4a5568;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.3rem;
        font-weight: 600;
    }

    .list-group-item {
        border: none;
        padding: 0.75rem 0;
        color: #4a5568;
    }

    .action-btn {
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0 0.5rem;
    }

    .btn-print { background: linear-gradient(to right, #38b2ac, #319795); color: white; }
    .btn-print:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }

    .btn-export { background: linear-gradient(to right, #805ad5, #6b46c1); color: white; }
    .btn-export:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }
</style>

<!-- Decoration -->
<div class="blob blob-1" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); top: -150px; left: -150px; opacity: 0.1;"></div>

<div class="page-container">
    <div class="d-flex align-items-center mb-5">
        <a href="<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="mb-0" style="font-family: 'Playfair Display', serif; background: linear-gradient(to right, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                User Engagement Report
            </h1>
            <p class="text-muted mb-0">Analysis of user activities and participation.</p>
        </div>
    </div>

    <!-- Report Content -->
    <div class="report-card">
        <div>
            <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
            // Fetch real user stats
            $users_res = mysqli_query($conn, "SELECT role, COUNT(*) as c FROM users GROUP BY role");
            $roles = [];
            $total_users = 0;
            while($row = mysqli_fetch_assoc($users_res)) {
                $roles[$row['role']] = $row['c'];
                $total_users += $row['c'];
            }
            
            $mothers = $roles['mother'] ?? 0;
            $profs = $roles['professional'] ?? 0;
            $admins = $roles['admin'] ?? 0;

            // Activity estimation (proxy via logs)
            $log_res = mysqli_query($conn, "SELECT COUNT(*) as c, MAX(created_at) as last_act FROM symptom_logs");
            $log_data = mysqli_fetch_assoc($log_res);
            $total_logs = $log_data['c'];
            $last_active = $log_data['last_act'] ? date('l', strtotime($log_data['last_act'])) : 'None';
            ?>

            <div class="section-title">🔍 Overview</div>
            <p class="mt-2 text-muted">This report outlines how users are interacting with the PSM System platform based on real-time database records.</p>

            <div class="section-title">📊 Key Metrics</div>
            <ul class="list-group list-group-flush mt-2">
                <li class="list-group-item"><i class="fas fa-users me-2 text-primary"></i> <strong>Total Users:</strong> <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $total_users; ?> (<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $mothers; ?> Mothers, <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $profs; ?> Professionals, <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $admins; ?> Admins)</li>
                <li class="list-group-item"><i class="fas fa-file-medical me-2 text-success"></i> <strong>Total Symptom Logs:</strong> <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $total_logs; ?> entries recorded</li>
                <li class="list-group-item"><i class="fas fa-star me-2 text-warning"></i> <strong>Most Used Feature:</strong> Symptom Logging</li>
                <li class="list-group-item"><i class="fas fa-calendar-alt me-2 text-info"></i> <strong>Last Active Day:</strong> <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $last_active; ?></li>
            </ul>

            <div class="section-title">💡 Insights & Recommendations</div>
            <ul class="list-group list-group-flush mt-2">
                <li class="list-group-item"><i class="fas fa-check me-2 text-success"></i> <strong>User Base:</strong> <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo ($mothers > $profs) ? 'Strong mother participation' : 'Balanced ecosystem'; ?></li>
                <li class="list-group-item"><i class="fas fa-check me-2 text-success"></i> <strong>Data Growth:</strong> <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo ($total_logs > 10) ? 'Healthy data accumulation' : 'Encourage more logging'; ?></li>
                <li class="list-group-item"><i class="fas fa-check me-2 text-success"></i> Highlight new resources in the Resource Library</li>
            </ul>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-center mt-4">
        <button class="action-btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
        <button class="action-btn btn-export" onclick="alert('PDF export feature coming soon')"><i class="fas fa-file-pdf"></i> Export PDF</button>
    </div>
</div>

<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


