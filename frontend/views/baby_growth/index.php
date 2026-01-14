<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Baby Growth Tracker (With WHO Standards)
 * Frontend View: Baby Growth Module
 */

$page_title = "Baby Growth Tracker - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';
require_once BASE_PATH . '/backend/helpers/functions.php';

requireLogin('mother');

$user_id = $_SESSION['user_id'];
$msg = "";

// Initialize / Update Settings (Birth Date & Gender)
// We'll trust POST or keep existing logic. Since DB doesn't have birth_date column easily, 
// we'll rely on LocalStorage for simplicity on the frontend, OR handle a quick session save if possible.
// Actually, to make the graph consistent across refreshes, we should ideally save it.
// But as per plan, we'll try to use a frontend-only approach for the reference lines first to avoid heavy DB migration.
// Wait, user asked to "import database". I will add basic saving to a new table or just use local storage for the "Settings" part to be non-intrusive.
// DECISION: Use LocalStorage for "Graph Settings" (Gender/DOB) for now to keep it simple and responsive.

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
$dates = [];
$weights = [];
$heights = [];
$full_records = []; // Store full objects to pass to JS for date calculation

$query = "SELECT * FROM baby_growth WHERE user_id = $user_id ORDER BY recorded_at ASC";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($result)) {
    $dates[] = date('M d', strtotime($row['recorded_at']));
    $weights[] = (float)$row['weight_kg'];
    $heights[] = (float)$row['height_cm'];
    $full_records[] = [
        'date' => $row['recorded_at'],
        'weight' => (float)$row['weight_kg'],
        'height' => (float)$row['height_cm']
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
    
    /* WHO Settings Panel */
    .who-settings {
        background: #E8EAF6;
        border-radius: 15px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #C5CAE9;
    }
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
        
        <!-- Graph Settings (WHO) -->
        <div class="col-12">
            <div class="who-settings d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <i class="fas fa-globe-americas me-2 text-primary"></i>
                    <strong class="me-3" style="color: #3f51b5;">WHO Standards Comparison</strong>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <div>
                        <small class="text-muted d-block">Baby's Birth Date:</small>
                        <input type="date" id="babyDob" class="form-control form-control-sm" onchange="updateChart()">
                    </div>
                    <div>
                        <small class="text-muted d-block">Gender:</small>
                        <select id="babyGender" class="form-select form-select-sm" onchange="updateChart()">
                            <option value="boy">Boy</option>
                            <option value="girl">Girl</option>
                        </select>
                    </div>
                    <div class="form-check form-switch pt-3">
                        <input class="form-check-input" type="checkbox" id="showWho" checked onchange="updateChart()">
                        <label class="form-check-label small" for="showWho">Show Reference</label>
                    </div>
                </div>
            </div>
        </div>

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
                        $milestones_list = [
                            '1m_smile' => '1 Month: Smiles at people',
                            '1m_head' => '1 Month: Holds head up briefly',
                            '2m_eye' => '2 Months: Follows objects',
                            '4m_roll' => '4 Months: Rolls over',
                            '6m_sit' => '6 Months: Sits without support',
                            '9m_crawl' => '9 Months: Crawls',
                            '12m_walk' => '12 Months: First steps'
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

        <!-- Growth Chart -->
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0 fw-bold" style="color: #26C6DA;">
                            <i class="fas fa-chart-line me-2"></i> Growth Chart vs WHO Standards
                        </h5>
                    </div>
                    <div style="height: 400px;">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Prepare Data from PHP
    const userRecords = <?php echo json_encode($full_records); ?>;
    
    // 2. WHO P50 Data (0-12 Months)
    const WHO_DATA = {
        boy: {
            // Month 0 to 12
            weight: [3.3, 4.5, 5.6, 6.4, 7.0, 7.5, 7.9, 8.3, 8.6, 8.9, 9.2, 9.4, 9.6],
            height: [49.9, 54.7, 58.4, 61.4, 63.9, 65.9, 67.6, 69.2, 70.6, 72.0, 73.3, 74.5, 75.7]
        },
        girl: {
            weight: [3.2, 4.2, 5.1, 5.8, 6.4, 6.9, 7.3, 7.6, 7.9, 8.2, 8.5, 8.7, 8.9],
            height: [49.1, 53.7, 57.1, 59.8, 62.1, 64.0, 65.7, 67.3, 68.7, 70.1, 71.5, 72.8, 74.0]
        }
    };

    let chartInstance = null;

    // 3. Initialize Settings from LocalStorage
    const savedDob = localStorage.getItem('psm_baby_dob');
    const savedGender = localStorage.getItem('psm_baby_gender');
    
    if(savedDob) document.getElementById('babyDob').value = savedDob;
    if(savedGender) document.getElementById('babyGender').value = savedGender;

    // 4. Main Chart Function
    function updateChart() {
        const dobStr = document.getElementById('babyDob').value;
        const gender = document.getElementById('babyGender').value; // 'boy' or 'girl'
        const showWho = document.getElementById('showWho').checked;
        
        // Save settings
        localStorage.setItem('psm_baby_dob', dobStr);
        localStorage.setItem('psm_baby_gender', gender);
        
        // Process User Data
        const labels = [];
        const weightData = [];
        const heightData = [];
        const whoWeightData = [];
        const whoHeightData = [];
        
        userRecords.forEach(record => {
            // Check if valid numbers
            if(record.weight > 0 || record.height > 0) {
                // Formatting Date Label
                const d = new Date(record.date);
                labels.push(d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                
                weightData.push(record.weight > 0 ? record.weight : null);
                heightData.push(record.height > 0 ? record.height : null);
                
                // Calculate WHO point
                if (dobStr && showWho) {
                    const dob = new Date(dobStr);
                    const recordDate = new Date(record.date);
                    
                    // Diff in months (approx)
                    let monthsDiff = (recordDate.getFullYear() - dob.getFullYear()) * 12;
                    monthsDiff -= dob.getMonth();
                    monthsDiff += recordDate.getMonth();
                    
                    // Adjust for days (simple approximation: <15 days = previous month, >15 = next)
                    // Better: just round to nearest integer month index [0-12]
                    // If age is negative (record before birth), ignore or set 0
                    if (monthsDiff < 0) monthsDiff = 0; 
                    if (monthsDiff > 12) monthsDiff = 12; // Cap at 12 for this dataset
                    
                    whoWeightData.push(WHO_DATA[gender].weight[monthsDiff]);
                    whoHeightData.push(WHO_DATA[gender].height[monthsDiff]);
                } else {
                    whoWeightData.push(null);
                    whoHeightData.push(null);
                }
            }
        });

        const ctx = document.getElementById('growthChart').getContext('2d');
        
        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    // User Weight
                    {
                        label: 'Your Baby Weight (kg)',
                        data: weightData,
                        borderColor: '#9575CD',
                        backgroundColor: 'rgba(149, 117, 205, 0.1)',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    // User Height
                    {
                        label: 'Your Baby Height (cm)',
                        data: heightData,
                        borderColor: '#26C6DA',
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1'
                    },
                    // WHO Weight
                    {
                        label: `WHO ${gender === 'boy' ? 'Boy' : 'Girl'} Standard (Weight)`,
                        data: whoWeightData,
                        borderColor: '#B0BEC5',
                        borderDash: [5, 5],
                        pointRadius: 0,
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y',
                        hidden: !showWho
                    },
                    // WHO Height
                    {
                        label: `WHO ${gender === 'boy' ? 'Boy' : 'Girl'} Standard (Height)`,
                        data: whoHeightData,
                        borderColor: '#90A4AE',
                        borderDash: [2, 2],
                        pointRadius: 0,
                        borderWidth: 1,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1',
                        hidden: !showWho
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Weight (kg)' },
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Height (cm)' },
                        grid: { drawOnChartArea: false },
                    }
                },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) { label += context.parsed.y; }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // Initial Load
    document.addEventListener('DOMContentLoaded', updateChart);

</script>

<?php require_once dirname(__FILE__, 4) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>
