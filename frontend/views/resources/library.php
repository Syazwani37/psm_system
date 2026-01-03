<?php
/**
 * PSM System - Resource Library
 * Frontend View: Resources Module (Professional)
 */

$page_title = "Resource Library - PSM System";
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/auth_check.php';
requireLogin(); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
        text-align: center;
    }

    .resource-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .resource-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.02);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        text-align: left;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .resource-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 150, 136, 0.15);
        border-color: #B2DFDB;
    }

    .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .res-pdf { background: #FFEBEE; color: #D32F2F; }
    .res-doc { background: #E3F2FD; color: #1976D2; }
    .res-vid { background: #E0F2F1; color: #00695C; }

    .resource-title {
        font-weight: 600;
        color: #263238;
        font-size: 1.15rem;
        line-height: 1.3;
        font-family: 'Playfair Display', serif;
    }

    .resource-desc {
        font-size: 0.95rem;
        color: #78909C;
        line-height: 1.5;
        flex: 1;
    }

    .download-btn {
        width: 100%;
        padding: 0.75rem;
        background: #FDFBF7;
        color: #00796B;
        border: 1px solid #B2DFDB;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .download-btn:hover {
        background: #009688;
        border-color: #009688;
        color: white;
    }
</style>

<!-- Decoration -->
<div class="blob blob-1" style="background: linear-gradient(135deg, #E0F2F1 0%, #B2DFDB 100%); top: -150px; right: -150px; width: 400px; height: 400px; opacity: 0.5;"></div>

<div class="page-container">
    <div class="text-center mb-5">
        <?php if ($_SESSION['role'] === 'professional'): ?>
            <a href="<?php echo BASE_URL; ?>/frontend/views/dashboard/professional.php" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
            </a>
        <?php else: ?>
             <a href="<?php echo BASE_URL; ?>/frontend/views/dashboard/mother.php" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
            </a>
        <?php endif; ?>
        
        <h1 class="mb-2" style="font-family: 'Playfair Display', serif; color: #00695C;">
            <i class="fas fa-folder-open me-2" style="color: #80CBC4;"></i> Resource Library
        </h1>
        <p class="text-muted">Access and contribute to recovery resources and protocols.</p>
    </div>

    <div class="resource-grid">
        <!-- Resource Item 1 -->
        <div class="resource-card">
            <div class="icon-box res-pdf"><i class="fas fa-file-pdf"></i></div>
            <div class="resource-title">Postpartum Recovery Guide</div>
            <div class="resource-desc">Full PDF guide for mother recovery timelines and milestones.</div>
            <a href="<?php echo BASE_URL; ?>/frontend/assets/docs/postpartum_recovery_guide.pdf" class="download-btn" download>
                <i class="fas fa-download"></i> Download
            </a>
        </div>

        <!-- Resource Item 2 -->
        <div class="resource-card">
            <div class="icon-box res-vid"><i class="fas fa-video"></i></div>
            <div class="resource-title">Gentle Movement Routines</div>
            <div class="resource-desc">Video protocols for early and late postnatal movement.</div>
            <a href="<?php echo BASE_URL; ?>/frontend/assets/docs/exercise_protocols.pdf" class="download-btn" download>
                <i class="fas fa-download"></i> Download
            </a>
        </div>

        <!-- Resource Item 3 -->
        <div class="resource-card">
            <div class="icon-box res-doc"><i class="fas fa-file-word"></i></div>
            <div class="resource-title">Nutrition Plan Template</div>
            <div class="resource-desc">Customizable meal plan templates for weeks 1–6.</div>
            <a href="<?php echo BASE_URL; ?>/frontend/assets/docs/nutrition_plan_template.docx" class="download-btn" download>
                <i class="fas fa-download"></i> Download
            </a>
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/footer.php'; ?>
