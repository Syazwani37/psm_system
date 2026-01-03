<?php
/**
 * PSM System - Recovery Trends Report
 * Frontend View: Reports Module
 */

$page_title = "Recovery Trends - PSM System";
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/includes/auth_check.php';
requireLogin('admin');
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
        text-align: center;
    }

    .report-content {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.02);
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        text-align: left;
        margin-bottom: 2rem;
    }

    .report-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #F3E5F5;
    }

    .report-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .report-label {
        font-weight: 600;
        font-size: 1.1rem;
        color: #6A1B9A;
    }

    .report-value {
        font-size: 1.1rem;
        color: #4A148C;
        background: #F3E5F5;
        padding: 0.25rem 1rem;
        border-radius: 50px;
        font-weight: 500;
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

    .print-btn { background: #6A1B9A; color: white; }
    .print-btn:hover { background: #4A148C; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(106, 27, 154, 0.2); }

    .export-btn { background: white; color: #6A1B9A; border: 1px solid #E1BEE7; }
    .export-btn:hover { background: #F3E5F5; transform: translateY(-2px); }
</style>

<!-- Decoration -->
<div class="blob blob-1" style="background: linear-gradient(135deg, #F3E5F5 0%, #E1BEE7 100%); top: -150px; right: -150px; width: 400px; height: 400px; opacity: 0.5;"></div>

<div class="page-container">
    <div class="text-center mb-5">
        <a href="<?php echo BASE_URL; ?>/frontend/views/dashboard/admin.php" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
        <h1 class="mb-2" style="font-family: 'Playfair Display', serif; color: #4A148C;">
            <i class="fas fa-chart-line me-2" style="color: #BA68C8;"></i> Recovery Trends
        </h1>
        <p class="text-muted">Postpartum recovery progress statistics for this month.</p>
    </div>

    <!-- Report Content -->
    <div class="report-content">
        <div class="report-item">
            <span class="report-label">Week 1 Check-up Rate</span>
            <span class="report-value">92%</span>
        </div>

        <div class="report-item">
            <span class="report-label">Pelvic Floor Exercise Starts</span>
            <span class="report-value">76%</span>
        </div>

        <div class="report-item">
            <span class="report-label">Nutrition Plan Adherence (4 Weeks)</span>
            <span class="report-value">68%</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-center">
        <button class="action-btn print-btn" onclick="window.print()"><i class="fas fa-print"></i> Print Report</button>
        <button class="action-btn export-btn" onclick="exportToPDF()"><i class="fas fa-file-pdf"></i> Export PDF</button>
    </div>
</div>

<script>
    function exportToPDF() {
        alert("This feature will generate a downloadable PDF of the report.");
    }
</script>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/includes/footer.php'; ?>

