<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
/**
 * PSM System - Analytics Dashboard
 * Frontend View: Dashboard Module
 */

$page_title = "Analytics - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';


requireLogin('professional');

// Fetch Analytics Data
$sql_counts = "SELECT result_status, COUNT(*) as count FROM symptom_logs GROUP BY result_status";
$result_counts = mysqli_query($conn, $sql_counts);

$safe_count = 0;
$warning_count = 0;
$danger_count = 0;

while ($row = mysqli_fetch_assoc($result_counts)) {
    if ($row['result_status'] == 'safe') $safe_count = $row['count'];
    if ($row['result_status'] == 'warning') $warning_count = $row['count'];
    if ($row['result_status'] == 'danger') $danger_count = $row['count'];
}

require_once BASE_PATH . '/backend/includes/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .analytics-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .analytics-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #A4B494, #6B8E6B);
    }

    .progress-bar-custom {
        height: 12px;
        background: #EDF1EA;
        border-radius: 12px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #A4B494, #6B8E6B);
        border-radius: 12px;
        transition: width 1.5s ease;
    }
</style>

<div class="container py-5" style="max-width: 900px;">
    <div class="d-flex align-items-center mb-5">
        <a href="<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="mb-0" style="font-family: 'Playfair Display', serif;">Analytics Dashboard</h1>
            <p class="text-muted mb-0">Cultivating insights for better recovery and wellness.</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recovery Growth -->
        <div class="col-md-6">
            <div class="analytics-card">
                <h5 class="mb-3" style="font-family: 'Playfair Display', serif;">Recovery Growth</h5>
                <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: 82%"></div>
                </div>
                <small class="text-muted mt-2 d-block">Week 1 to Week 4: +82% Physical Activity</small>
            </div>
        </div>

        <!-- Nutrition Balance -->
        <div class="col-md-6">
            <div class="analytics-card">
                <h5 class="mb-3" style="font-family: 'Playfair Display', serif;">Nutrition Balance</h5>
                <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: 76%"></div>
                </div>
                <small class="text-muted mt-2 d-block">76% Adherence to Meal Plans</small>
            </div>
        </div>

        <!-- Wellness Factors -->
        <div class="col-md-6">
            <div class="analytics-card">
                <h5 class="mb-3" style="font-family: 'Playfair Display', serif;">Wellness Factors</h5>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span>Anxiety Level</span>
                        <strong class="text-success">Low (38%)</strong>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span>Fatigue Score</span>
                        <strong class="text-warning">Medium (42%)</strong>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span>Sleep Quality</span>
                        <strong class="text-success">Improving (80%)</strong>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Patient Health Chart -->
        <div class="col-md-6">
            <div class="analytics-card">
                <h5 class="mb-3" style="font-family: 'Playfair Display', serif;">Patient Health Overview</h5>
                <canvas id="symptomChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('symptomChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Stable', 'Monitoring', 'Critical'],
            datasets: [{
                label: 'Patients',
                data: [<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $safe_count; ?>, <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $warning_count; ?>, <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; echo $danger_count; ?>],
                backgroundColor: ['#6B8E6B', '#E8D5B5', '#C62828'],
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>

<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


