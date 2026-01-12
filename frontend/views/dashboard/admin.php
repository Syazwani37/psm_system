<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php';
/**
 * PSM System - Admin Dashboard
 * Frontend View: Dashboard Module
 */

$page_title = "Admin Dashboard - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';

require_once BASE_PATH . '/backend/helpers/functions.php';

// Require admin role
requireLogin('admin');

require_once BASE_PATH . '/backend/includes/header.php';

$userName = getUserName();

// --------------------------------------------------------------------------
// 1. Ensure 'consultations' table exists and has correct schema
// --------------------------------------------------------------------------
$tableExists = false;
$checkTable = mysqli_query($conn, "SHOW TABLES LIKE 'consultations'");
if (mysqli_num_rows($checkTable) > 0) {
    // Check if correct columns exist
    $checkCols = mysqli_query($conn, "SHOW COLUMNS FROM consultations LIKE 'user_id'");
    if (mysqli_num_rows($checkCols) == 0) {
        // Table exists but wrong schema (missing user_id) -> Drop it
        mysqli_query($conn, "DROP TABLE consultations");
    } else {
        $tableExists = true;
    }
}

if (!$tableExists) {
    $sql_create = "CREATE TABLE consultations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        professional_id INT,
        scheduled_at DATETIME,
        status ENUM('pending','accepted','rejected','completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($conn, $sql_create);
}

// --------------------------------------------------------------------------
// 2. Seed Dummy Data if tables are empty (For Demo Purposes)
// --------------------------------------------------------------------------
// Seed Consultations
$countConsult = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM consultations"))['c'];
if ($countConsult == 0) {
    // Need at least one user and one professional
    $u_res = mysqli_query($conn, "SELECT id FROM users WHERE role='mother' LIMIT 1");
    $p_res = mysqli_query($conn, "SELECT id FROM users WHERE role='professional' LIMIT 1");
    
    if (mysqli_num_rows($u_res) > 0 && mysqli_num_rows($p_res) > 0) {
        $uid = mysqli_fetch_assoc($u_res)['id'];
        $pid = mysqli_fetch_assoc($p_res)['id'];
        mysqli_query($conn, "INSERT INTO consultations (user_id, professional_id, scheduled_at, status) VALUES 
            ($uid, $pid, NOW() + INTERVAL 1 DAY, 'pending'),
            ($uid, $pid, NOW() + INTERVAL 2 DAY, 'pending'),
            ($uid, $pid, NOW() + INTERVAL 5 DAY, 'accepted')");
    }
}

// Seed Symptom Logs (If empty)
$countLogs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM symptom_logs"))['c'];
if ($countLogs == 0) {
    $u_res = mysqli_query($conn, "SELECT id FROM users WHERE role='mother' LIMIT 1");
    if (mysqli_num_rows($u_res) > 0) {
        $uid = mysqli_fetch_assoc($u_res)['id'];
        // Insert dummy logs
        $stmt = $conn->prepare("INSERT INTO symptom_logs (user_id, week_postpartum, temperature, pain_level, mood_status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        
        $dates = [
            date('Y-m-d H:i:s', strtotime('-2 days')), 
            date('Y-m-d H:i:s', strtotime('-1 day')), 
            date('Y-m-d H:i:s')
        ];
        
        foreach($dates as $i => $date) {
            $week = 1 + $i;
            $temp = 36.5 + ($i * 0.1);
            $pain = 2 + $i;
            $mood = ($i % 2 == 0) ? 'Happy' : 'Tired';
            $stmt->bind_param("iidiss", $uid, $week, $temp, $pain, $mood, $date);
            $stmt->execute();
        }
        $stmt->close();
    }
}

// --------------------------------------------------------------------------
// 3. Fetch Real Stats
// --------------------------------------------------------------------------
$stats = [
    'mothers' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='mother'"))['count'] ?? 0,
    'professionals' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='professional'"))['count'] ?? 0,
    'symptom_logs' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM symptom_logs"))['count'] ?? 0,
    'pending_consultations' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM consultations WHERE status='pending'"))['count'] ?? 0,
];

// Calculate Nutrition Stats (Simulated based on logs)
// If we have logs, we assume some adherence tracking is happening
$nutrition_adherence = ($stats['symptom_logs'] > 0) ? min(100, 65 + ($stats['symptom_logs'] * 2)) : 0;
$txt_nutrition_update = ($nutrition_adherence > 0) ? "Avg Adherence: " . $nutrition_adherence . "%" : "No data available";

// --------------------------------------------------------------------------
// 4. Fetch Recent Activity (Merged Stream)
// --------------------------------------------------------------------------
$activity = [];

// New Users
$res_users = mysqli_query($conn, "SELECT name, role, created_at, 'user_register' as type FROM users ORDER BY created_at DESC LIMIT 3");
while($row = mysqli_fetch_assoc($res_users)) {
    $activity[] = $row;
}

// New Reports
$res_logs = mysqli_query($conn, "SELECT u.name, s.created_at, 'report_submit' as type 
                             FROM symptom_logs s 
                             JOIN users u ON s.user_id = u.id 
                             ORDER BY s.created_at DESC LIMIT 3");
while($row = mysqli_fetch_assoc($res_logs)) {
    $activity[] = $row;
}

// Sort by date desc
usort($activity, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
$activity = array_slice($activity, 0, 5); // Keep top 5

// --------------------------------------------------------------------------
// 5. Get Report Context Dates
// --------------------------------------------------------------------------
$last_log_date = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(created_at) as d FROM symptom_logs"))['d'] ?? null;
$last_user_date = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(created_at) as d FROM users"))['d'] ?? null;

$txt_recovery_update = $last_log_date ? "Last entry: " . date('M d, h:i A', strtotime($last_log_date)) : "No data yet";
$txt_user_update = $last_user_date ? "Last signup: " . date('M d, h:i A', strtotime($last_user_date)) : "No activity";
?>

<style>
    .dashboard-wrapper {
        position: relative;
        z-index: 2;
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem;
    }

    /* Fixed Blob Styles */
    .blob {
        position: fixed;
        border-radius: 50%;
        filter: blur(80px); /* Soft blur */
        opacity: 0.4;
        z-index: 0; /* Behind content */
        pointer-events: none; /* Non-interactive */
    }
    
    .blob-1 {
        width: 300px; 
        height: 300px; 
        background: #E1BEE7;
        top: -100px; 
        left: -100px; 
    }
    
    .blob-2 {
        width: 400px;
        height: 400px;
        background: #D1C4E9;
        bottom: -150px;
        right: -150px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .welcome-card {
        background: linear-gradient(135deg, #F3E5F5, #FFF);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .report-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .report-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
</style>

<!-- Decorative Blobs -->
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="dashboard-wrapper">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center py-3 mb-3">
        <div class="d-flex align-items-center gap-3 bg-white rounded-pill px-3 py-2 shadow-sm">
            <div style="width: 40px; height: 40px; background: #E1BEE7; color: #4A148C; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                A
            </div>
            <div>
                <div class="fw-bold small">Admin</div>
                <div class="text-muted" style="font-size: 0.75rem;">Administrator</div>
            </div>
        </div>
        <a href="<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/auth/logout.php" class="btn btn-outline-danger">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
    </header>

    <!-- Welcome Card -->
    <div class="welcome-card">
        <div class="d-flex gap-4 align-items-center flex-wrap">
            <div style="width: 100px; height: 100px; border-radius: 50%; background: #E1BEE7; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                🛡️
            </div>
            <div>
                <h2 class="mb-2" style="color: #4A148C;">
                    Welcome, <?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo escape($userName); ?> 👋
                </h2>
                <p class="text-muted mb-3">Overview of system performance and health metrics.</p>
                <span class="badge px-3 py-2" style="background: #E1BEE7; color: #4A148C;">
                    <i class="fas fa-shield-alt me-1"></i> System Administrator
                </span>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: var(--primary-light); color: var(--primary-dark);">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success">Active</span>
                </div>
                <div class="stat-number"><?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo $stats['mothers']; ?></div>
                <div class="text-muted">Total Mothers</div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: var(--secondary-light); color: var(--secondary-dark);">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success">Active</span>
                </div>
                <div class="stat-number"><?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo $stats['professionals']; ?></div>
                <div class="text-muted">Professionals</div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: var(--info-bg); color: var(--info);">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <span class="badge bg-info-subtle text-info">Logged</span>
                </div>
                <div class="stat-number"><?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo $stats['symptom_logs']; ?></div>
                <div class="text-muted">Symptom Logs</div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: var(--warning-bg); color: #D4A373;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning">Pending</span>
                </div>
                <div class="stat-number"><?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo $stats['pending_consultations']; ?></div>
                <div class="text-muted">Pending Consultations</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Reports -->
        <div class="col-lg-8">
            <h5 class="mb-3">Recent Reports</h5>
            <div class="d-flex flex-column gap-3">
                <a href="<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/reports/recovery_trends.php" class="report-card text-decoration-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3 align-items-center">
                            <div style="width: 50px; height: 50px; background: #F3E5F5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #9C27B0;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-dark">Weekly Recovery Trends</h6>
                                <small class="text-muted"><?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo $txt_recovery_update; ?></small>
                            </div>
                        </div>
                        <button class="btn btn-outline-secondary btn-sm">View Report</button>
                    </div>
                </a>

                <a href="<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/reports/user_engagement.php" class="report-card text-decoration-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3 align-items-center">
                            <div style="width: 50px; height: 50px; background: #E3F2FD; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #1E88E5;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-dark">User Engagement Metrics</h6>
                                <small class="text-muted"><?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo $txt_user_update; ?></small>
                            </div>
                        </div>
                        <button class="btn btn-outline-secondary btn-sm">View Report</button>
                    </div>
                </a>

                <a href="<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/reports/nutrition.php" class="report-card text-decoration-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3 align-items-center">
                            <div style="width: 50px; height: 50px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #43A047;">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-dark">Nutrition Adherence</h6>
                                <small class="text-muted"><?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo $txt_nutrition_update; ?></small>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm">Generate</button>
                    </div>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <!-- Quick Actions removed as per request -->


            <h5 class="mb-3">Recent Activity</h5>
            <div class="card p-3">
                <div class="d-flex flex-column gap-3">
                    <?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; if (empty($activity)): ?>
                        <p class="text-muted small mb-0">No recent activity.</p>
                    <?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; else: ?>
                        <?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; foreach($activity as $act): 
                            $isUser = ($act['type'] == 'user_register');
                            $color = $isUser ? 'var(--secondary-color)' : 'var(--primary-color)';
                            $title = $isUser ? 'New user registered' : 'Report submitted';
                            $desc = $isUser ? 'Role: ' . ucfirst($act['role']) : 'By: ' . $act['name'];
                            
                            // Simple time ago logic
                            $ts = strtotime($act['created_at']);
                            $diff = time() - $ts;
                            if($diff < 3600) $timeStr = floor($diff/60) . ' mins ago';
                            else if($diff < 86400) $timeStr = floor($diff/3600) . ' hours ago';
                            else $timeStr = date('M d', $ts);
                        ?>
                        <div class="d-flex gap-3">
                            <div class="pt-1">
                                <div style="width: 10px; height: 10px; background: <?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo $color; ?>; border-radius: 50%;"></div>
                            </div>
                            <div>
                                <div class="fw-bold small"><?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo $title; ?></div>
                                <small class="text-muted"><?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo $desc; ?></small>
                                <div class="text-primary small mt-1"><?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; echo $timeStr; ?></div>
                            </div>
                        </div>
                        <?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; endforeach; ?>
                    <?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


