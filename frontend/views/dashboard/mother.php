<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Mother Dashboard
 * Frontend View: Dashboard Module
 */

$page_title = "Mother Dashboard - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';
require_once BASE_PATH . '/backend/helpers/functions.php';

// Require mother role
requireLogin('mother');

require_once BASE_PATH . '/backend/includes/header.php';

$userName = getUserName();
?>

<style>
    .dashboard-wrapper {
        position: relative;
        z-index: 2;
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

    /* Fixed Blob Styles */
    .blob {
        position: fixed;
        border-radius: 50%;
        filter: blur(80px);
        z-index: 0;
        pointer-events: none;
    }

    .blob-1 {
        width: 300px;
        height: 300px;
        top: -100px;
        left: -100px;
    }

    .blob-2 {
        width: 400px;
        height: 400px;
        bottom: -100px;
        right: -100px;
    }
</style>

<!-- Decorative Blobs -->
<div class="blob blob-1" style="background: var(--secondary-light); opacity: 0.5;"></div>
<div class="blob blob-2" style="background: var(--primary-light); opacity: 0.4;"></div>

<div class="dashboard-wrapper">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center py-3 mb-3">
        <div></div> <!-- Empty div to push logout button to the right -->
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
        <div class="d-flex gap-3 align-items-center flex-wrap">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: #FFE0E9; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                👩
            </div>
            <div>
                <h2 class="mb-1" style="color: var(--primary-dark);">
                    Welcome, <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo escape($userName); ?>! 👋
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
    <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/symptom_checker/index.php" class="feature-card mb-4" style="border: 1px solid #FFEBEE;">
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
            <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/recovery/index.php" class="feature-card">
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
            <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/nutrition/index.php" class="feature-card">
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
            <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/consultations/book.php" class="feature-card">
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
            <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/community/index.php" class="feature-card">
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
            <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/resources/articles.php" class="feature-card">
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
            <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/mental_health/epds_screening.php" class="feature-card">
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
            <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/baby_growth/index.php" class="feature-card">
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
            <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/journal/index.php" class="feature-card">
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
            <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
            // Include tips data
            $tips_file = BASE_PATH . '/backend/data/tips_data.php';
            if (file_exists($tips_file)) {
                require_once $tips_file;
                
                // Shuffle and pick 3
                if (isset($daily_tips_data) && is_array($daily_tips_data)) {
                    shuffle($daily_tips_data); 
                    $todays_tips = array_slice($daily_tips_data, 0, 3);
                    
                    foreach ($todays_tips as $tip) {
                        ?>
                        <div class="col-md-4">
                            <div class="d-flex gap-3 align-items-start h-100 p-2 rounded-3 hover-bg" style="transition: background 0.2s;">
                                <div style="background: <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo $tip['color_bg']; ?>; padding: 0.6rem; border-radius: 12px; color: <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo $tip['color_text']; ?>; min-width: 45px; text-align: center;">
                                    <i class="fas <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo $tip['icon']; ?>"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark"><?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo $tip['title']; ?></h6>
                                    <p class="text-secondary small mb-0" style="font-size: 0.85rem; line-height: 1.4;"><?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo $tip['text']; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
                    }
                }
            } else {
                echo '<p class="text-muted">Stay hydrated and rested!</p>';
            }
            ?>
        </div>
    </div>
</div>

<script>
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
            // Show a simple success message
            alert('All notifications marked as read');
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

function updateNotificationCount() {
    // Reload the page to update the notification count in the header
    location.reload();
}
</script>

<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>
