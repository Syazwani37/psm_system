<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
/**
 * PSM System - Professional Dashboard
 * Frontend View: Dashboard Module
 */

$page_title = "Professional Dashboard - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';

require_once BASE_PATH . '/backend/helpers/functions.php';

// Require professional role
requireLogin('professional');

require_once BASE_PATH . '/backend/includes/header.php';

$userName = getUserName();

// Fetch recent symptom logs with reviewer info
// Logs query removed
$result_logs = null;
?>

<style>
    .dashboard-wrapper {
        position: relative;
        z-index: 2;
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
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .log-card:hover {
        background: #F0F7F6;
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 150, 136, 0.1);
    }

    .log-card.reviewed {
        border-left: 3px solid #4CAF50;
    }

    .log-card.follow-up {
        border-left: 3px solid #FF9800;
    }

    .action-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .action-modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin: 1.5rem 0;
    }

    .detail-item {
        background: #F5F5F5;
        padding: 0.75rem;
        border-radius: 10px;
    }

    .detail-label {
        font-size: 0.75rem;
        color: #757575;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        font-weight: 600;
        color: #333;
    }

    .action-btn-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .action-btn {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        border: 2px solid;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .action-btn.reviewed {
        border-color: #4CAF50;
        color: #4CAF50;
    }

    .action-btn.reviewed:hover, .action-btn.reviewed.active {
        background: #4CAF50;
        color: white;
    }

    .action-btn.follow-up {
        border-color: #FF9800;
        color: #FF9800;
    }

    .action-btn.follow-up:hover, .action-btn.follow-up.active {
        background: #FF9800;
        color: white;
    }

    .reviewed-badge {
        font-size: 0.7rem;
        background: #E8F5E9;
        color: #2E7D32;
        padding: 2px 8px;
        border-radius: 10px;
        margin-left: 8px;
    }

    /* Fixed Blob Styles for Z-Index Management */
    .blob {
        position: fixed;
        border-radius: 50%;
        filter: blur(80px);
        z-index: 0; /* Behind content */
        pointer-events: none;
    }

    .blob-1 {
        top: -150px;
        left: -150px;
    }

    .blob-2 {
        bottom: -150px;
        right: -150px;
        width: 400px;
        height: 400px;
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
                    <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo getInitials($userName); ?>
                </div>
                <div>
                    <div class="fw-bold small"><?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo escape($userName); ?></div>
                    <div class="text-muted" style="font-size: 0.75rem;">Healthcare Specialist</div>
                </div>
            </div>
        </div>
        <a href="<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/auth/logout.php" class="btn btn-outline-danger">
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
                    Welcome, <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo escape($userName); ?> 👋
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
            <a href="<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/patient_management/index.php" class="feature-card d-block">
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
            <a href="<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/dashboard/analytics.php" class="feature-card d-block">
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
            <a href="<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/resources/library.php" class="feature-card d-block">
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
            <a href="<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/announcements/manage.php" class="feature-card d-block">
                <div class="feature-icon" style="background: #F3E5F5;">
                    <i class="fas fa-bullhorn fa-lg" style="color: #7B1FA2;"></i>
                </div>
                <h5 class="mb-2">Announcements</h5>
                <p class="text-muted small mb-3">Broadcast updates to all mothers.</p>
                <span style="color: #7B1FA2; font-weight: 600;">
                    Manage Alerts <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </a>
        </div>

        <!-- Consultation Requests -->
        <div class="col-md-6 col-lg-4">
            <a href="<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/consultations/manage.php" class="feature-card d-block">
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

    <!-- Recent Symptom Logs Section Removed as per request -->
</div>

<!-- Action Modal -->
<div id="logActionModal" class="action-modal" onclick="closeModalOnBackdrop(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0" id="modalPatientName">Patient Name</h5>
            <button onclick="closeLogModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #999;">&times;</button>
        </div>
        
        <div id="modalStatusBadge" class="mb-3"></div>
        
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Week Postpartum</div>
                <div class="detail-value" id="modalWeek">-</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Temperature</div>
                <div class="detail-value" id="modalTemp">-</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Pain Level</div>
                <div class="detail-value" id="modalPain">-</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Wound Condition</div>
                <div class="detail-value" id="modalWound">-</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Bleeding</div>
                <div class="detail-value" id="modalBleeding">-</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Mood</div>
                <div class="detail-value" id="modalMood">-</div>
            </div>
        </div>
        
        <div id="reviewerInfo" class="mb-3" style="display: none;">
            <small class="text-muted"><i class="fas fa-check-circle text-success me-1"></i> Reviewed by <span id="reviewerName"></span></small>
        </div>
        
        <hr>
        
        <h6 class="mb-3"><i class="fas fa-tasks me-2"></i>Take Action</h6>
        
        <div class="action-btn-group">
            <button type="button" class="action-btn reviewed" id="btnReviewed" onclick="setActionStatus('reviewed')">
                <i class="fas fa-check me-1"></i> Mark Reviewed
            </button>
            <button type="button" class="action-btn follow-up" id="btnFollowUp" onclick="setActionStatus('follow_up_required')">
                <i class="fas fa-exclamation-triangle me-1"></i> Requires Follow-up
            </button>
        </div>
        
        <div class="mb-3">
            <label class="form-label small text-muted">Professional Notes</label>
            <textarea id="professionalNotes" class="form-control" rows="3" placeholder="Add notes about this patient's condition..."></textarea>
        </div>
        
        <input type="hidden" id="currentLogId" value="">
        <input type="hidden" id="currentActionStatus" value="">
        
        <div class="d-flex gap-2">
            <button onclick="saveLogAction()" class="btn btn-primary flex-grow-1">
                <i class="fas fa-save me-1"></i> Save Action
            </button>
            <button onclick="closeLogModal()" class="btn btn-outline-secondary">Cancel</button>
        </div>
    </div>
</div>

<script>
let currentLogId = null;
let currentActionStatus = 'reviewed';

function openLogModal(logId) {
    const card = document.querySelector(`[data-log-id="${logId}"]`);
    if (!card) return;
    
    currentLogId = logId;
    
    // Populate modal with data from card
    document.getElementById('modalPatientName').textContent = card.dataset.patient;
    document.getElementById('modalWeek').textContent = 'Week ' + card.dataset.week;
    document.getElementById('modalTemp').textContent = card.dataset.temp + '°C';
    document.getElementById('modalPain').textContent = card.dataset.pain;
    document.getElementById('modalWound').textContent = card.dataset.wound;
    document.getElementById('modalBleeding').textContent = card.dataset.bleeding;
    document.getElementById('modalMood').textContent = card.dataset.mood;
    document.getElementById('professionalNotes').value = card.dataset.notes || '';
    document.getElementById('currentLogId').value = logId;
    
    // Set status badge
    const status = card.dataset.status;
    let badgeHtml = '';
    if (status === 'danger') {
        badgeHtml = '<span class="badge" style="background: #FFEBEE; color: #C62828;">⚠ Needs Attention</span>';
    } else if (status === 'warning') {
        badgeHtml = '<span class="badge" style="background: #FFF3E0; color: #EF6C00;">👀 Monitoring</span>';
    } else {
        badgeHtml = '<span class="badge" style="background: #E8F5E9; color: #2E7D32;">✓ Stable</span>';
    }
    document.getElementById('modalStatusBadge').innerHTML = badgeHtml;
    
    // Show reviewer info if already reviewed
    const reviewer = card.dataset.reviewer;
    if (reviewer) {
        document.getElementById('reviewerInfo').style.display = 'block';
        document.getElementById('reviewerName').textContent = reviewer;
    } else {
        document.getElementById('reviewerInfo').style.display = 'none';
    }
    
    // Set active button based on current action status
    const actionStatus = card.dataset.actionStatus;
    setActionStatus(actionStatus === 'pending' ? 'reviewed' : actionStatus);
    
    // Show modal
    document.getElementById('logActionModal').classList.add('show');
}

function closeLogModal() {
    document.getElementById('logActionModal').classList.remove('show');
    currentLogId = null;
}

function closeModalOnBackdrop(event) {
    if (event.target.id === 'logActionModal') {
        closeLogModal();
    }
}

function setActionStatus(status) {
    currentActionStatus = status;
    document.getElementById('currentActionStatus').value = status;
    
    // Update button states
    document.getElementById('btnReviewed').classList.remove('active');
    document.getElementById('btnFollowUp').classList.remove('active');
    
    if (status === 'reviewed') {
        document.getElementById('btnReviewed').classList.add('active');
    } else if (status === 'follow_up_required') {
        document.getElementById('btnFollowUp').classList.add('active');
    }
}

function saveLogAction() {
    const logId = document.getElementById('currentLogId').value;
    const notes = document.getElementById('professionalNotes').value;
    const actionStatus = currentActionStatus;
    
    if (!logId) {
        alert('Error: No log selected');
        return;
    }
    
    fetch('<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/backend/api/symptom_checker/save_log_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            log_id: parseInt(logId),
            notes: notes,
            action_status: actionStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the card visually
            const card = document.querySelector(`[data-log-id="${logId}"]`);
            if (card) {
                card.dataset.notes = notes;
                card.dataset.actionStatus = actionStatus;
                card.classList.remove('reviewed', 'follow-up');
                if (actionStatus === 'reviewed') card.classList.add('reviewed');
                if (actionStatus === 'follow_up_required') card.classList.add('follow-up');
                
                // Update badge in card
                const nameDiv = card.querySelector('.fw-bold');
                let badge = nameDiv.querySelector('.reviewed-badge');
                if (badge) badge.remove();
                
                if (actionStatus === 'reviewed') {
                    nameDiv.innerHTML += '<span class="reviewed-badge">✓ Reviewed</span>';
                } else if (actionStatus === 'follow_up_required') {
                    nameDiv.innerHTML += '<span class="reviewed-badge" style="background: #FFF3E0; color: #EF6C00;">⚠ Follow-up</span>';
                }
            }
            
            closeLogModal();
            
            // Show success toast
            showToast('Action saved successfully!', 'success');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save action. Please try again.');
    });
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        z-index: 2000;
        animation: slideUp 0.3s ease;
    `;
    toast.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>

<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>
