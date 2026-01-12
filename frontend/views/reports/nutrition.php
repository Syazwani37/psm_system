<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
/**
 * PSM System - Nutrition Adherence Report
 * Frontend View: Reports Module
 */

$page_title = "Nutrition Adherence - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';

requireLogin('admin');
require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
        text-align: center;
    }

    .report-card {
        background: white;
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.02);
        text-align: left;
    }

    .section-title {
        font-size: 1.25rem;
        margin-top: 1.5rem;
        color: #6A1B9A;
        border-bottom: 2px solid #F3E5F5;
        padding-bottom: 0.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-family: 'Playfair Display', serif;
    }

    .section-title:first-child {
        margin-top: 0;
    }

    .report-list {
        margin-top: 1rem;
        padding-left: 0;
        list-style: none;
    }

    .report-list li {
        margin-bottom: 0.75rem;
        color: #5C5C5C;
        display: flex;
        gap: 0.75rem;
    }

    .report-list li i {
        color: #BA68C8;
        margin-top: 5px;
    }

    .percentage-bar {
        margin-top: 1.5rem;
        height: 24px;
        width: 100%;
        background-color: #F3E5F5;
        border-radius: 50px;
        overflow: hidden;
        position: relative;
    }

    .percentage-fill {
        height: 100%;
        width: 76%;
        background: linear-gradient(to right, #BA68C8, #8E24AA);
        border-radius: 50px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 1rem;
        color: white;
        font-size: 0.85rem;
        font-weight: 600;
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

    .btn-print { background: #6A1B9A; color: white; }
    .btn-print:hover { background: #4A148C; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(106, 27, 154, 0.2); }

    .btn-export { background: white; color: #6A1B9A; border: 1px solid #E1BEE7; }
    .btn-export:hover { background: #F3E5F5; transform: translateY(-2px); }
</style>

<!-- Decoration -->
<div class="blob blob-1" style="background: linear-gradient(135deg, #F3E5F5 0%, #E1BEE7 100%); top: -150px; left: -150px; width: 400px; height: 400px; opacity: 0.5;"></div>

<div class="page-container">
    <div class="text-center mb-5">
        <a href="<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/dashboard/admin.php" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
        <h1 class="mb-2" style="font-family: 'Playfair Display', serif; color: #4A148C;">
            <i class="fas fa-apple-alt me-2" style="color: #BA68C8;"></i> Nutrition Report
        </h1>
        <p class="text-muted">Tracking adherence to postpartum nutrition plans.</p>
    </div>

    <div class="report-card">
        <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
        // Calculate "Adherence" based on symptom log consistency
        // Logic: More logs = Higher adherence score (Gamified metric)
        $q = mysqli_query($conn, "SELECT COUNT(*) as c FROM symptom_logs");
        $count = mysqli_fetch_assoc($q)['c'];
        
        // Simulating adherence percentage: Base 50% + (2% per log), max 100%
        $adherence = min(100, 50 + ($count * 2));
        
        // Dynamic Week Logic
        $week_res = mysqli_query($conn, "SELECT week_postpartum, COUNT(*) as c FROM symptom_logs GROUP BY week_postpartum ORDER BY c DESC LIMIT 1");
        $best_week = mysqli_fetch_assoc($week_res);
        $best_week_num = $best_week ? $best_week['week_postpartum'] : 1;
        $best_week_val = $best_week ? round(($best_week['c'] / max(1, $count)) * 100) : 0;
        ?>

        <div class="section-title"><i class="fas fa-info-circle"></i> Overview</div>
        <p style="margin-top: 0.5rem; color: #78909C; line-height: 1.6;">This report tracks adherence based on the volume and consistency of symptom logs submitted by mothers.</p>

        <div class="section-title"><i class="fas fa-chart-bar"></i> Adherence Statistics</div>
        <ul class="report-list">
            <li><i class="fas fa-check"></i> <strong>Calculated Adherence:</strong> <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $adherence; ?>% (Based on log volume)</li>
            <li><i class="fas fa-check"></i> <strong>Most Active Period:</strong> Week <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $best_week_num; ?> Postpartum (<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $best_week_val; ?>% of total logs)</li>
            <li><i class="fas fa-check"></i> <strong>Total Data Points:</strong> <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $count; ?> logs analyzed</li>
        </ul>

        <div class="percentage-bar">
            <div class="percentage-fill" style="width: <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $adherence; ?>%;"><?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $adherence; ?>% Avg</div>
        </div>

        <div class="section-title"><i class="fas fa-lightbulb"></i> Recommendations</div>
        <ul class="report-list">
            <li><i class="fas fa-check"></i> <strong>Status:</strong> <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo ($adherence > 70) ? 'Excellent engagement levels.' : 'Encourage more daily logging.'; ?></li>
            <li><i class="fas fa-check"></i> Introduce weekly nutrition reminders via email</li>
            <li><i class="fas fa-check"></i> Integrate meal plan tracking into Recovery Tracker</li>
            <li><i class="fas fa-check"></i> Provide incentives for full-week adherence</li>
        </ul>
    </div>

    <div class="d-flex justify-content-center mt-4">
        <button class="action-btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
        <button class="action-btn btn-export" onclick="alert('PDF export feature coming soon')"><i class="fas fa-file-pdf"></i> Export PDF</button>
    </div>
</div>

<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


