<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Baby Growth Tracker (3 Separate Charts: Weight, Height, Head Circ)
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
        mysqli_query($conn, "DELETE FROM baby_milestones WHERE user_id = $user_id AND milestone_id = '$m_id'");
    } else {
        $stmt = $conn->prepare("INSERT INTO baby_milestones (user_id, milestone_id, milestone_name, is_achieved, achieved_at) VALUES (?, ?, ?, 1, NOW())");
        $stmt->bind_param("iss", $user_id, $m_id, $m_name);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit();
}

// Fetch Growth Data for Chart
$full_records = []; 
$query = "SELECT * FROM baby_growth WHERE user_id = $user_id ORDER BY recorded_at ASC";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($result)) {
    $full_records[] = [
        'date' => $row['recorded_at'],
        'weight' => (float)$row['weight_kg'],
        'height' => (float)$row['height_cm'],
        'head' => (float)$row['head_circ_cm']
    ];
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
    .milestone-item { display: flex; align-items: center; padding: 1rem; border-bottom: 1px solid #f0f0f0; transition: background-color 0.2s; }
    .milestone-item:hover { background-color: #F3E5F5; }
    .milestone-check { margin-right: 1rem; width: 22px; height: 22px; cursor: pointer; accent-color: var(--primary-color); }
    .milestone-label { cursor: pointer; flex-grow: 1; font-weight: 500; color: #555; }
    .milestone-label.done { text-decoration: line-through; color: #aaa; }
    .card-header-custom { border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1.5rem; }
    .who-settings { background: #E8EAF6; border-radius: 15px; padding: 1rem; margin-bottom: 1.5rem; border: 1px solid #C5CAE9; }
</style>

<div class="container py-5" style="max-width: 1000px;">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="<?php require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="mb-0" style="font-family: 'Playfair Display', serif; color: #4A148C;">Baby Growth & WHO Standards</h2>
    </div>

    <?php require_once dirname(__FILE__, 4) . '/backend/config/database.php'; displayFlashMessage(); ?>

    <div class="row g-4">
        
        <!-- WHO Settings -->
        <div class="col-12">
            <div class="who-settings d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <i class="fas fa-globe-americas me-2 text-primary"></i>
                    <strong class="me-3" style="color: #3f51b5;">WHO Standards Settings</strong>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <div>
                        <small class="text-muted d-block">Baby's Birth Date:</small>
                        <input type="date" id="babyDob" class="form-control form-control-sm" onchange="updateCharts()">
                    </div>
                    <div>
                        <small class="text-muted d-block">Gender:</small>
                        <select id="babyGender" class="form-select form-select-sm" onchange="updateCharts()">
                            <option value="boy">Boy</option>
                            <option value="girl">Girl</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forms & Milestones -->
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
                            <input type="date" name="date" class="form-control" required value="<?php require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo date('Y-m-d'); ?>">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Weight (kg)</label>
                                <input type="number" step="0.01" name="weight" class="form-control" placeholder="3.5">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Height (cm)</label>
                                <input type="number" step="0.1" name="height" class="form-control" placeholder="50.0">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Head Circ. (cm)</label>
                            <input type="number" step="0.1" name="head" class="form-control" placeholder="35.0">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill" style="background-color: #7E57C2; border-color: #7E57C2;">Save Record</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0 fw-bold" style="color: #AB47BC;">
                            <i class="fas fa-flag me-2"></i> Developmental Milestones
                        </h5>
                    </div>
                    
                    <div class="alert alert-light border-0 shadow-sm mb-3">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Note: Every baby develops at their own pace. These are general ranges.</small>
                    </div>

                    <div style="max-height: 320px; overflow-y: auto;">
                        <?php
                        $milestones_list = [
                            '1_2m_smile' => '1-2 Months: Smiles at people',
                            '1_2m_head' => '1-2 Months: Holds head up briefly',
                            '2_3m_eye' => '2-3 Months: Follows objects with eyes',
                            '3_5m_roll' => '3-5 Months: Rolls over (tummy to back)',
                            '5_7m_sit' => '5-7 Months: Sits without support',
                            '7_10m_crawl' => '7-10 Months: Starts crawling',
                            '10_15m_walk' => '10-15 Months: First steps'
                        ];
                        foreach($milestones_list as $id => $name): 
                            $is_done = in_array($id, $achieved);
                        ?>
                        <div class="milestone-item">
                            <form method="POST" class="d-flex w-100 align-items-center m-0">
                                <input type="hidden" name="toggle_milestone" value="1">
                                <input type="hidden" name="milestone_id" value="<?php echo $id; ?>">
                                <input type="hidden" name="milestone_name" value="<?php echo $name; ?>">
                                <input type="checkbox" class="milestone-check" onchange="this.form.submit()" <?php echo $is_done ? 'checked' : ''; ?>>
                                <span class="milestone-label <?php echo $is_done ? 'done' : ''; ?>" onclick="this.previousElementSibling.click()"><?php echo $name; ?></span>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW: CHART 1 (WEIGHT) -->
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="card-header-custom d-flex justify-content-between">
                        <h5 class="mb-0 fw-bold" style="color: #9575CD;">
                            <i class="fas fa-weight me-2"></i> Weight Chart (kg)
                        </h5>
                        <small class="text-muted">Standards: WHO Reference</small>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="weightChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW: CHART 2 (HEIGHT) -->
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="card-header-custom d-flex justify-content-between">
                        <h5 class="mb-0 fw-bold" style="color: #26C6DA;">
                            <i class="fas fa-ruler-vertical me-2"></i> Height Chart (cm)
                        </h5>
                        <small class="text-muted">Standards: WHO Reference</small>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="heightChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

         <!-- NEW: CHART 3 (HEAD CIRCUMFERENCE) -->
         <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="card-header-custom d-flex justify-content-between">
                        <h5 class="mb-0 fw-bold" style="color: #F06292;">
                            <i class="fas fa-smile me-2"></i> Head Circumference (cm)
                        </h5>
                        <small class="text-muted">Standards: WHO Reference</small>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="headChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const userRecords = <?php echo json_encode($full_records); ?>;
    
    // WHO P50 Data (0-12 Months)
    const WHO_DATA = {
        boy: {
            weight: [3.3, 4.5, 5.6, 6.4, 7.0, 7.5, 7.9, 8.3, 8.6, 8.9, 9.2, 9.4, 9.6],
            height: [49.9, 54.7, 58.4, 61.4, 63.9, 65.9, 67.6, 69.2, 70.6, 72.0, 73.3, 74.5, 75.7],
            head: [34.5, 36.7, 38.5, 40.5, 41.6, 42.6, 43.3, 44.0, 44.5, 45.0, 45.4, 45.8, 46.1]
        },
        girl: {
            weight: [3.2, 4.2, 5.1, 5.8, 6.4, 6.9, 7.3, 7.6, 7.9, 8.2, 8.5, 8.7, 8.9],
            height: [49.1, 53.7, 57.1, 59.8, 62.1, 64.0, 65.7, 67.3, 68.7, 70.1, 71.5, 72.8, 74.0],
            head: [33.9, 35.9, 37.7, 39.5, 40.6, 41.5, 42.2, 42.9, 43.4, 43.8, 44.3, 44.6, 44.9]
        }
    };

    let weightChartInstance = null;
    let heightChartInstance = null;
    let headChartInstance = null;

    // Load Settings
    const savedDob = localStorage.getItem('psm_baby_dob');
    const savedGender = localStorage.getItem('psm_baby_gender');
    
    if(savedDob) document.getElementById('babyDob').value = savedDob;
    if(savedGender) document.getElementById('babyGender').value = savedGender;

    function updateCharts() {
        const dobStr = document.getElementById('babyDob').value;
        const gender = document.getElementById('babyGender').value;
        
        localStorage.setItem('psm_baby_dob', dobStr);
        localStorage.setItem('psm_baby_gender', gender);
        
        // Prepare WHO Standard Datasets (0 to 12 months)
        const whoLabels = Array.from({length: 13}, (_, i) => i); // [0, 1, ... 12]
        const whoWeightData = WHO_DATA[gender].weight;
        const whoHeightData = WHO_DATA[gender].height;
        const whoHeadData = WHO_DATA[gender].head;

        // Prepare User Data (Calculated as Age in Months)
        const userWeightData = [];
        const userHeightData = [];
        const userHeadData = [];
        
        userRecords.forEach(record => {
            if (!dobStr) return; // Cannot map without DOB
            
            const dob = new Date(dobStr);
            const recordDate = new Date(record.date);
            const oneMonthMs = 1000 * 60 * 60 * 24 * 30.4375; // Average month length
            
            let ageInMonths = (recordDate - dob) / oneMonthMs;
            if (ageInMonths < 0) ageInMonths = 0;
            // Allow plotting slightly beyond 12m, but focus is 0-12
            
            if (record.weight > 0) userWeightData.push({ x: ageInMonths, y: record.weight, date: record.date });
            if (record.height > 0) userHeightData.push({ x: ageInMonths, y: record.height, date: record.date });
            if (record.head > 0) userHeadData.push({ x: ageInMonths, y: record.head, date: record.date });
        });

        // Common Options
        const commonOptions = (title) => ({
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'nearest',
                intersect: false,
            },
            scales: {
                x: {
                    type: 'linear',
                    title: { display: true, text: 'Age (Months)' },
                    min: 0,
                    max: 12,
                    ticks: { stepSize: 1 }
                },
                y: {
                    title: { display: true, text: title }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y;
                            }
                            if (context.raw && context.raw.date) {
                                label += ` (Recorded: ${context.raw.date})`;
                            }
                            return label;
                        }
                    }
                }
            }
        });

        // Helper to Create Chart
        const createChart = (ctxId, instance, label, userData, whoData, color, title) => {
            const ctx = document.getElementById(ctxId).getContext('2d');
            if (instance) instance.destroy();

            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: whoLabels, // Acts as the basis for the line charts
                    datasets: [
                        {
                            label: label,
                            data: userData, // Array of {x, y} objects
                            borderColor: color,
                            backgroundColor: color,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            showLine: false, // User data is points only
                        },
                        {
                            label: `WHO ${gender === 'boy' ? 'Boy' : 'Girl'} Standard`,
                            data: whoData.map((val, idx) => ({x: idx, y: val})), // Map to x,y for consistency
                            borderColor: '#B0BEC5',
                            borderDash: [5, 5],
                            borderWidth: 2,
                            pointRadius: 0,
                            fill: false,
                            tension: 0.4
                        }
                    ]
                },
                options: commonOptions(title)
            });
        };

        weightChartInstance = createChart('weightChart', weightChartInstance, 'Weight (kg)', userWeightData, whoWeightData, '#9575CD', 'Weight (kg)');
        heightChartInstance = createChart('heightChart', heightChartInstance, 'Height (cm)', userHeightData, whoHeightData, '#26C6DA', 'Height (cm)');
        headChartInstance = createChart('headChart', headChartInstance, 'Head Circ. (cm)', userHeadData, whoHeadData, '#F06292', 'Head Circ. (cm)');
    }

    document.addEventListener('DOMContentLoaded', updateCharts);
</script>

<?php require_once dirname(__FILE__, 4) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>
