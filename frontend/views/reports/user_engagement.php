<?php
/**
 * PSM System - User Engagement Report
 * Frontend View: Reports Module
 */

$page_title = "User Engagement - PSM System";
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/auth_check.php';
requireLogin('admin');
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/header.php';
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
    <div class="text-center mb-5">
        <a href="<?php echo BASE_URL; ?>/frontend/views/dashboard/admin.php" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
        <h1 class="mb-2" style="font-family: 'Playfair Display', serif; background: linear-gradient(to right, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            User Engagement Report
        </h1>
    </div>

    <!-- Report Content -->
    <div class="report-card">
        <div>
            <div class="section-title">🔍 Overview</div>
            <p class="mt-2 text-muted">This report outlines how users are interacting with the PSM System platform.</p>

            <div class="section-title">📊 Key Metrics</div>
            <ul class="list-group list-group-flush mt-2">
                <li class="list-group-item"><i class="fas fa-users me-2 text-primary"></i> <strong>Total Users:</strong> 1,240 (1,000 Mothers, 200 Professionals, 10 Admins)</li>
                <li class="list-group-item"><i class="fas fa-calendar-day me-2 text-success"></i> <strong>Daily Active Users:</strong> Average 420 users per day</li>
                <li class="list-group-item"><i class="fas fa-star me-2 text-warning"></i> <strong>Most Used Feature:</strong> Recovery Tracker — used by 87% of mothers</li>
                <li class="list-group-item"><i class="fas fa-user-clock me-2 text-secondary"></i> <strong>Least Engaged Role:</strong> Admins — only 3 active monthly</li>
                <li class="list-group-item"><i class="fas fa-calendar-alt me-2 text-info"></i> <strong>Top Day of Activity:</strong> Wednesday (22% of weekly logins)</li>
            </ul>

            <div class="section-title">💡 Insights & Recommendations</div>
            <ul class="list-group list-group-flush mt-2">
                <li class="list-group-item"><i class="fas fa-check me-2 text-success"></i> Send monthly summaries and reminders to admins</li>
                <li class="list-group-item"><i class="fas fa-check me-2 text-success"></i> Enhance tracker notifications for mothers</li>
                <li class="list-group-item"><i class="fas fa-check me-2 text-success"></i> Encourage healthcare professional–mother follow-ups</li>
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

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/footer.php'; ?>
