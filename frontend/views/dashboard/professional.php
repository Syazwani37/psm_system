<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
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
$profId = $_SESSION['user_id'];

// Fetch recent symptom logs with reviewer info
// Logs query removed
$result_logs = null;

// --------------------------------------------------------------------------
// Chart Data: Symptom Logs by Risk Level (all patients)
// --------------------------------------------------------------------------
$chart_risk_labels = ['Low', 'Medium', 'High'];
$chart_risk_data = [
    mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM symptom_logs WHERE result_status = 'low'"))['c'] ?? 0,
    mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM symptom_logs WHERE result_status = 'medium'"))['c'] ?? 0,
    mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM symptom_logs WHERE result_status = 'high'"))['c'] ?? 0,
];

// --------------------------------------------------------------------------
// Chart Data: My Consultations (as professional)
// --------------------------------------------------------------------------
$chart_consult_labels = ['Pending', 'Accepted', 'Completed'];
$chart_consult_data = [
    mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM consultations WHERE professional_id = $profId AND status = 'pending'"))['c'] ?? 0,
    mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM consultations WHERE professional_id = $profId AND status = 'accepted'"))['c'] ?? 0,
    mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM consultations WHERE professional_id = $profId AND status = 'completed'"))['c'] ?? 0,
];

// --------------------------------------------------------------------------
// Chart Data: New Patients (last 7 days)
// --------------------------------------------------------------------------
$chart_patient_labels = [];
$chart_patient_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_patient_labels[] = date('M d', strtotime($date));
    $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='mother' AND DATE(created_at) = '$date'"))['c'] ?? 0;
    $chart_patient_data[] = (int)$count;
}
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

    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
    }
    .chart-card h6 {
        color: #00695C;
        margin-bottom: 1rem;
        font-weight: 600;
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
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo getInitials($userName); ?>
                </div>
                <div>
                    <div class="fw-bold small"><?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo escape($userName); ?></div>
                    <div class="text-muted" style="font-size: 0.75rem;">Healthcare Specialist</div>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Notifications Bell -->
            <div class="position-relative">
                <button class="btn btn-light position-relative" id="notificationBell" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bell fa-lg"></i>
                    <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; $unread_count = getUnreadNotificationCount(); if ($unread_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo $unread_count; ?>
                        </span>
                    <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationBell" id="notificationDropdown">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><a class="dropdown-item" href="#" id="markAllRead">Mark all as read</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li id="notificationsList">
                        <div class="text-center py-2 text-muted">Loading notifications...</div>
                    </li>
                </ul>
            </div>

            <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/auth/logout.php" class="btn btn-outline-danger">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>
        </div>
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
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo escape($userName); ?> 👋
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
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/patient_management/index.php" class="feature-card d-block">
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
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/dashboard/analytics.php" class="feature-card d-block">
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
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/resources/library.php" class="feature-card d-block">
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
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/announcements/manage.php" class="feature-card d-block">
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
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/consultations/manage.php" class="feature-card d-block">
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

    <!-- Analytics Charts -->
    <h5 class="mb-3 mt-4"><i class="fas fa-chart-bar me-2 text-secondary"></i>Quick Analytics</h5>
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="chart-card">
                <h6><i class="fas fa-heartbeat me-2"></i>Patient Risk Distribution</h6>
                <div style="height: 150px; position: relative;">
                     <canvas id="riskDistChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card">
                <h6><i class="fas fa-calendar-check me-2"></i>My Consultations</h6>
                <div style="height: 150px; position: relative;">
                    <canvas id="myConsultChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card">
                <h6><i class="fas fa-user-plus me-2"></i>New Mothers (7 Days)</h6>
                <div style="height: 150px; position: relative;">
                    <canvas id="newPatientsChart"></canvas>
                </div>
            </div>
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
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/backend/api/symptom_checker/save_log_action.php', {
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

// Notification functionality
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();

    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);

    // Mark all as read
    document.getElementById('markAllRead').addEventListener('click', function(e) {
        e.preventDefault();
        markAllNotificationsAsRead();
    });
});

function loadNotifications() {
    fetch('<?php echo BASE_URL; ?>/backend/api/notifications/get_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayNotifications(data.notifications);
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
}

function displayNotifications(notifications) {
    const notificationsList = document.getElementById('notificationsList');

    if (notifications.length === 0) {
        notificationsList.innerHTML = '<div class="text-center py-2 text-muted">No notifications</div>';
        return;
    }

    let html = '';
    notifications.forEach(notification => {
        const formattedDate = new Date(notification.created_at).toLocaleString();
        html += `
            <a class="dropdown-item notification-item ${!notification.is_read ? 'bg-light' : ''}" href="#" data-id="${notification.id}">
                <div class="d-flex justify-content-between">
                    <div>${notification.message}</div>
                    ${!notification.is_read ? '<span class="badge bg-danger">New</span>' : ''}
                </div>
                <small class="text-muted">${formattedDate}</small>
            </a>
        `;
    });

    notificationsList.innerHTML = html;

    // Add event listeners to notification items
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const notificationId = this.getAttribute('data-id');
            markNotificationAsRead(notificationId, this);
        });
    });
}

function markNotificationAsRead(notificationId, element) {
    fetch('<?php echo BASE_URL; ?>/backend/api/notifications/mark_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ notification_id: parseInt(notificationId) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the 'bg-light' class to indicate it's read
            element.classList.remove('bg-light');
            // Remove the 'New' badge
            const badge = element.querySelector('.badge');
            if (badge) {
                badge.remove();
            }
            // Update the notification count in the header
            updateNotificationCount();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function markAllNotificationsAsRead() {
    fetch('<?php echo BASE_URL; ?>/backend/api/notifications/mark_all_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload notifications to reflect changes
            loadNotifications();
            // Update the notification count in the header
            updateNotificationCount();
            showToast('All notifications marked as read', 'success');
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

function updateNotificationCount() {
    // Reload the page to update the notification count in the header
    // This is a simple solution - in a production app, you might want to update it via AJAX
    location.reload();
}

// --------------------------------------------------------------------------
// Chart.js Initialization for Professional Dashboard
// --------------------------------------------------------------------------
// Risk Distribution Doughnut
const riskCtx = document.getElementById('riskDistChart');
if (riskCtx) {
    new Chart(riskCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($chart_risk_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($chart_risk_data); ?>,
                backgroundColor: ['#4CAF50', '#FFC107', '#F44336'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
}

// My Consultations Bar
const consultCtx = document.getElementById('myConsultChart');
if (consultCtx) {
    new Chart(consultCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_consult_labels); ?>,
            datasets: [{
                label: 'Count',
                data: <?php echo json_encode($chart_consult_data); ?>,
                backgroundColor: ['#FFC107', '#4CAF50', '#2196F3'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
}

// New Patients Line
const patientCtx = document.getElementById('newPatientsChart');
if (patientCtx) {
    new Chart(patientCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_patient_labels); ?>,
            datasets: [{
                label: 'New',
                data: <?php echo json_encode($chart_patient_data); ?>,
                borderColor: '#009688',
                backgroundColor: 'rgba(0, 150, 136, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#009688',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
}
</script>

<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>
