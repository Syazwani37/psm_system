<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
/**
 * PSM System - Baby Growth Tracker
 * Frontend View: Baby Growth Module
 */

$page_title = "Baby Growth Tracker - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';

require_once BASE_PATH . '/backend/helpers/functions.php';

requireLogin('mother');

$user_id = $_SESSION['user_id'];
$msg = "";

// Handle Form Submission (Add Growth Record)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_growth'])) {
    $date = $_POST['date'];
    $weight = floatval($_POST['weight']);
    $height = floatval($_POST['height']);
    $head = floatval($_POST['head']);

    $stmt = $conn->prepare("INSERT INTO baby_growth (user_id, weight_kg, height_cm, head_circ_cm, recorded_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iddds", $user_id, $weight, $height, $head, $date);
    if($stmt->execute()) {
        setFlashMessage('success', 'Record added successfully!');
    } else {
        setFlashMessage('error', 'Error adding record.');
    }
    $stmt->close();
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Milestone Toggle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_milestone'])) {
    $m_id = $_POST['milestone_id'];
    $m_name = $_POST['milestone_name'];
    
    // Check if exists
    $check = mysqli_query($conn, "SELECT id FROM baby_milestones WHERE user_id = $user_id AND milestone_id = '$m_id'");
    if (mysqli_num_rows($check) > 0) {
        // Toggle: Delete if exists
        mysqli_query($conn, "DELETE FROM baby_milestones WHERE user_id = $user_id AND milestone_id = '$m_id'");
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO baby_milestones (user_id, milestone_id, milestone_name, is_achieved, achieved_at) VALUES (?, ?, ?, 1, NOW())");
        $stmt->bind_param("iss", $user_id, $m_id, $m_name);
        $stmt->execute();
        $stmt->close();
    }
    
    // Refresh to show changes (prevents resubmission)
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit();
}

// Fetch Growth Data for Chart
$dates = [];
$weights = [];
$heights = [];

$query = "SELECT * FROM baby_growth WHERE user_id = $user_id ORDER BY recorded_at ASC";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($result)) {
    $dates[] = date('M d', strtotime($row['recorded_at']));
    $weights[] = $row['weight_kg'];
    $heights[] = $row['height_cm'];
}

// Fetch Achieved Milestones
$achieved = [];
$m_result = mysqli_query($conn, "SELECT milestone_id FROM baby_milestones WHERE user_id = $user_id");
while($row = mysqli_fetch_assoc($m_result)) {
    $achieved[] = $row['milestone_id'];
}

require_once BASE_PATH . '/backend/includes/header.php';
?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .milestone-item { 
        display: flex; 
        align-items: center; 
        padding: 1rem; 
        border-bottom: 1px solid #f0f0f0; 
        transition: background-color 0.2s;
    }
    .milestone-item:hover { background-color: #F3E5F5; }
    .milestone-item:last-child { border-bottom: none; }
    
    .milestone-check { 
        margin-right: 1rem; 
        width: 22px; 
        height: 22px; 
        cursor: pointer; 
        accent-color: var(--primary-color); 
    }
    
    .milestone-label { 
        cursor: pointer; 
        flex-grow: 1; 
        font-weight: 500; 
        color: #555;
    }
    
    .milestone-label.done { 
        text-decoration: line-through; 
        color: #aaa; 
    }
    
    .card-header-custom {
        border-bottom: 1px solid #eee;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }
</style>

<div class="container py-5" style="max-width: 1000px;">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="mb-0" style="font-family: 'Playfair Display', serif; color: #4A148C;">Baby Growth & Milestones</h2>
    </div>

    <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; displayFlashMessage(); ?>

    <div class="row g-4">
        <!-- Growth Entry Form -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0 fw-bold" style="color: #7E57C2;">
                            <i class="fas fa-weight-hanging me-2"></i> Add Measurement
                        </h5>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="add_growth" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Date</label>
                            <input type="date" name="date" class="form-control" required value="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Weight (kg)</label>
                                <input type="number" step="0.01" name="weight" class="form-control" placeholder="e.g. 3.5">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Height (cm)</label>
                                <input type="number" step="0.1" name="height" class="form-control" placeholder="e.g. 50">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Head Circumference (cm)</label>
                            <input type="number" step="0.1" name="head" class="form-control" placeholder="e.g. 35">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill" 
                                style="background-color: #7E57C2; border-color: #7E57C2;">
                            Save Record
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Milestones -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0 fw-bold" style="color: #AB47BC;">
                            <i class="fas fa-flag me-2"></i> Milestones
                        </h5>
                    </div>
                    
                    <div style="max-height: 320px; overflow-y: auto;">
                        <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
                        $milestones_list = [
                            '1m_smile' => 'Smiles at people',
                            '1m_head' => 'Can hold head up briefly',
                            '2m_eye' => 'Follows objects with eyes',
                            '2m_sound' => 'Coos and makes sounds',
                            '4m_roll' => 'Rolls over (tummy to back)',
                            '6m_sit' => 'Sits without support',
                            '9m_crawl' => 'Starts crawling',
                            '12m_walk' => 'First steps'
                        ];
                        
                        foreach($milestones_list as $id => $name): 
                            $is_done = in_array($id, $achieved);
                        ?>
                        <div class="milestone-item">
                            <form method="POST" class="d-flex w-100 align-items-center m-0">
                                <input type="hidden" name="toggle_milestone" value="1">
                                <input type="hidden" name="milestone_id" value="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $id; ?>">
                                <input type="hidden" name="milestone_name" value="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $name; ?>">
                                <input type="checkbox" class="milestone-check" onchange="this.form.submit()" <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $is_done ? 'checked' : ''; ?>>
                                <span class="milestone-label <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $is_done ? 'done' : ''; ?>" 
                                      onclick="this.previousElementSibling.click()">
                                    <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $name; ?>
                                </span>
                            </form>
                        </div>
                        <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Growth Chart -->
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0 fw-bold" style="color: #26C6DA;">
                            <i class="fas fa-chart-line me-2"></i> Growth Chart
                        </h5>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('growthChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo json_encode($dates); ?>,
            datasets: [{
                label: 'Weight (kg)',
                data: <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo json_encode($weights); ?>,
                borderColor: '#9575CD',
                backgroundColor: 'rgba(149, 117, 205, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Height (cm)',
                data: <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo json_encode($heights); ?>,
                borderColor: '#26C6DA',
                borderDash: [5, 5],
                fill: false,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: false }
            },
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


