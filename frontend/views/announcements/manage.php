<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
/**
 * PSM System - Manage Announcements
 * Frontend View: Announcements Module
 */

$page_title = "Manage Announcements - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';

require_once BASE_PATH . '/backend/helpers/functions.php';

requireLogin('professional');

require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .announcement-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .form-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 3rem;
    }

    .badge-info { background: #E3F2FD; color: #1565C0; }
    .badge-warning { background: #FFF3E0; color: #EF6C00; }
    .badge-danger { background: #FFEBEE; color: #C62828; }
    .badge-success { background: #E8F5E9; color: #2E7D32; }
</style>

<div class="page-container">
    <div class="d-flex align-items-center mb-5">
        <a href="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/dashboard/professional.php" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="mb-0" style="font-family: 'Playfair Display', serif; color: #00695C;">
                <i class="fas fa-bullhorn me-2"></i> Announcements
            </h1>
            <p class="text-muted mb-0">Broadcast official updates to all mothers.</p>
        </div>
    </div>

    <!-- Create Announcement Form -->
    <div class="form-card">
        <h5 class="mb-4">📢 Post New Announcement</h5>
        <form action="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/backend/api/announcements/create.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g., Flu Season Warning" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="3" placeholder="Write your message here..." required></textarea>
            </div>
            <div class="mb-4">
                <label class="form-label">Type</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" value="info" id="typeInfo" checked>
                        <label class="form-check-label" for="typeInfo"><span class="badge badge-info">Info</span></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" value="warning" id="typeWarning">
                        <label class="form-check-label" for="typeWarning"><span class="badge badge-warning">Warning</span></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" value="danger" id="typeDanger">
                        <label class="form-check-label" for="typeDanger"><span class="badge badge-danger">Urgent</span></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" value="success" id="typeSuccess">
                        <label class="form-check-label" for="typeSuccess"><span class="badge badge-success">Good News</span></label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill">
                <i class="fas fa-paper-plane me-2"></i> Post Announcement
            </button>
        </form>
    </div>

    <!-- Active Announcements List -->
    <h5 class="mb-3">Active Announcements</h5>
    <div class="d-flex flex-column gap-2">
        <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
        $sql = "SELECT * FROM announcements ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $typeClass = 'badge-info';
                if ($row['type'] == 'warning') $typeClass = 'badge-warning';
                if ($row['type'] == 'danger') $typeClass = 'badge-danger';
                if ($row['type'] == 'success') $typeClass = 'badge-success';
                ?>
                <div class="announcement-card">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $typeClass; ?> rounded-pill" style="font-size: 0.7rem;"><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo strtoupper($row['type']); ?></span>
                            <span class="text-muted small"><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></span>
                        </div>
                        <h6 class="mb-1 fw-bold"><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo escape($row['title']); ?></h6>
                        <p class="text-secondary small mb-0"><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo escape($row['message']); ?></p>
                    </div>
                    <form action="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/backend/api/announcements/delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this?');">
                        <input type="hidden" name="announcement_id" value="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $row['id']; ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
            }
        } else {
            echo '<p class="text-muted text-center py-4">No active announcements.</p>';
        }
        ?>
    </div>
</div>

<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>
