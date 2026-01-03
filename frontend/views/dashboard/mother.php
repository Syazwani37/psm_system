<?php
/**
 * PSM System - Mother Dashboard
 * Frontend View: Dashboard Module
 */

$page_title = "Mother Dashboard - PSM System";
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/helpers/functions.php';

// Require mother role
requireLogin('mother');

require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/includes/header.php';

$userName = getUserName();
?>

<style>
    .dashboard-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 1rem;
    }

    .feature-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        border: 1px solid #F5F5F5;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        color: inherit;
    }

    .feature-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .welcome-card {
        background: linear-gradient(135deg, #FFF0F5, #FFF);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
</style>

<!-- Decorative Blobs -->
<div class="blob blob-1" style="background: var(--secondary-light); opacity: 0.5;"></div>
<div class="blob blob-2" style="background: var(--primary-light); opacity: 0.4;"></div>

<div class="dashboard-wrapper">
    <!-- Header -->
    <header class="d-flex justify-content-end align-items-center py-3 mb-3">
        <a href="<?php echo BASE_URL; ?>/frontend/views/auth/logout.php" class="btn btn-outline-danger">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
    </header>

    <!-- Welcome Card -->
    <div class="welcome-card">
        <div class="d-flex gap-3 align-items-center flex-wrap">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: #FFE0E9; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                👩
            </div>
            <div>
                <h2 class="mb-1" style="color: var(--primary-dark);">
                    Welcome, <?php echo escape($userName); ?>! 👋
                </h2>
                <p class="text-muted mb-2">Continue your recovery journey with us.</p>
                <span class="badge" style="background: #F8BBD0; color: #880E4F;">
                    <i class="fas fa-star me-1"></i> Registered Mother
                </span>
            </div>
        </div>
    </div>

    <!-- Main Features Header -->
    <div class="d-flex align-items-center gap-2 mb-4">
        <i class="fas fa-th-large text-secondary"></i>
        <h4 class="mb-0 text-secondary">Main Features</h4>
    </div>

    <!-- Primary Feature: Symptom Checker -->
    <a href="<?php echo BASE_URL; ?>/frontend/views/symptom_checker/index.php" class="feature-card mb-4" style="border: 1px solid #FFEBEE;">
        <div class="d-flex align-items-center gap-3">
            <div class="feature-icon" style="background: #FFEBEE;">
                <i class="fas fa-stethoscope fa-lg" style="color: #D32F2F;"></i>
            </div>
            <div>
                <h5 class="mb-1" style="color: #D32F2F;">Symptom Checker</h5>
                <p class="text-muted small mb-1">Feeling unwell? Check your symptoms quickly with our guide.</p>
                <span style="color: #D32F2F; font-weight: 600; font-size: 0.9rem;">
                    Start Symptom Checker <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </div>
        </div>
    </a>

    <!-- Secondary Features Grid -->
    <div class="row g-3 mb-4">
        <!-- Recovery Tracker -->
        <div class="col-6 col-md-4 col-lg-3">
            <a href="<?php echo BASE_URL; ?>/frontend/views/recovery/index.php" class="feature-card">
                <div class="feature-icon" style="background: #E8F5E9;">
                    <i class="fas fa-chart-line" style="color: #2E7D32;"></i>
                </div>
                <h6 class="mb-1">Recovery Tracker</h6>
                <p class="text-muted small mb-2">Track progress.</p>
                <span style="color: #66BB6A; font-weight: 600; font-size: 0.85rem;">
                    Go <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Nutrition Plans -->
        <div class="col-6 col-md-4 col-lg-3">
            <a href="<?php echo BASE_URL; ?>/frontend/views/nutrition/index.php" class="feature-card">
                <div class="feature-icon" style="background: #FFF3E0;">
                    <i class="fas fa-apple-alt" style="color: #EF6C00;"></i>
                </div>
                <h6 class="mb-1">Nutrition Plans</h6>
                <p class="text-muted small mb-2">Meal/exercise.</p>
                <span style="color: #FFA726; font-weight: 600; font-size: 0.85rem;">
                    View <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Consultations -->
        <div class="col-6 col-md-4 col-lg-3">
            <a href="<?php echo BASE_URL; ?>/frontend/views/consultations/book.php" class="feature-card">
                <div class="feature-icon" style="background: #E3F2FD;">
                    <i class="fas fa-user-md" style="color: #1565C0;"></i>
                </div>
                <h6 class="mb-1">Consultations</h6>
                <p class="text-muted small mb-2">Expert advice.</p>
                <span style="color: #42A5F5; font-weight: 600; font-size: 0.85rem;">
                    Book <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Community -->
        <div class="col-6 col-md-4 col-lg-3">
            <a href="<?php echo BASE_URL; ?>/frontend/views/community/index.php" class="feature-card">
                <div class="feature-icon" style="background: #F3E5F5;">
                    <i class="fas fa-users" style="color: #7B1FA2;"></i>
                </div>
                <h6 class="mb-1">Community</h6>
                <p class="text-muted small mb-2">Moms forum.</p>
                <span style="color: #AB47BC; font-weight: 600; font-size: 0.85rem;">
                    Join <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Articles & Tips -->
        <div class="col-6 col-md-4 col-lg-3">
            <a href="<?php echo BASE_URL; ?>/frontend/views/resources/articles.php" class="feature-card">
                <div class="feature-icon" style="background: #FFF8E1;">
                    <i class="fas fa-book-open" style="color: #FF8F00;"></i>
                </div>
                <h6 class="mb-1">Articles & Tips</h6>
                <p class="text-muted small mb-2">Postcare tips.</p>
                <span style="color: #FFA000; font-weight: 600; font-size: 0.85rem;">
                    Read <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Mental Check -->
        <div class="col-6 col-md-4 col-lg-3">
            <a href="<?php echo BASE_URL; ?>/frontend/views/mental_health/epds_screening.php" class="feature-card">
                <div class="feature-icon" style="background: #FCE4EC;">
                    <i class="fas fa-heartbeat" style="color: #F06292;"></i>
                </div>
                <h6 class="mb-1">Mental Check</h6>
                <p class="text-muted small mb-2">Screen your mood.</p>
                <span style="color: #EC407A; font-weight: 600; font-size: 0.85rem;">
                    Start <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Baby Growth -->
        <div class="col-6 col-md-4 col-lg-3">
            <a href="<?php echo BASE_URL; ?>/frontend/views/baby_growth/index.php" class="feature-card">
                <div class="feature-icon" style="background: #EDE7F6;">
                    <i class="fas fa-baby" style="color: #7E57C2;"></i>
                </div>
                <h6 class="mb-1">Baby Growth</h6>
                <p class="text-muted small mb-2">Track milestones.</p>
                <span style="color: #7E57C2; font-weight: 600; font-size: 0.85rem;">
                    Track <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Mom's Journal -->
        <div class="col-6 col-md-4 col-lg-3">
            <a href="<?php echo BASE_URL; ?>/frontend/views/journal/index.php" class="feature-card">
                <div class="feature-icon" style="background: #FFF3E0;">
                    <i class="fas fa-journal-whills" style="color: #FF9800;"></i>
                </div>
                <h6 class="mb-1">Mom's Journal</h6>
                <p class="text-muted small mb-2">Private notes.</p>
                <span style="color: #FF9800; font-weight: 600; font-size: 0.85rem;">
                    Write <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>
    </div>

    <!-- Daily Tips Section -->
    <div class="card p-4" style="background: rgba(255,255,255,0.9);">
        <h5 class="mb-3">
            <i class="fas fa-lightbulb text-warning me-2"></i>Daily Tips
        </h5>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="d-flex gap-3 align-items-start">
                    <div style="background: #E1F5FE; padding: 0.5rem; border-radius: 10px; color: #0288D1;">
                        <i class="fas fa-glass-water"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Hydrate</h6>
                        <p class="text-muted small mb-0">Drink 8 glasses of water.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-3 align-items-start">
                    <div style="background: #F3E5F5; padding: 0.5rem; border-radius: 10px; color: #8E24AA;">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Rest Well</h6>
                        <p class="text-muted small mb-0">Sleep when your baby sleeps.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-3 align-items-start">
                    <div style="background: #E8F5E9; padding: 0.5rem; border-radius: 10px; color: #388E3C;">
                        <i class="fas fa-walking"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Move</h6>
                        <p class="text-muted small mb-0">Take a light 10 min walk.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/includes/footer.php'; ?>

