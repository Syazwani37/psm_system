<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Symptom Checker
 * Frontend View: Symptom Checker Module
 */

$page_title = "Symptom Checker - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';

require_once BASE_PATH . '/backend/helpers/functions.php';

// Check login (Mother only)
requireLogin('mother');

$user_id = $_SESSION['user_id'];
$message = "";
$save_status = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $week = mysqli_real_escape_string($conn, $_POST['week']);
    $temp = floatval($_POST['temp']);
    $pain = mysqli_real_escape_string($conn, $_POST['pain']);
    $wound = mysqli_real_escape_string($conn, $_POST['wound']);
    $bleeding = mysqli_real_escape_string($conn, $_POST['bleeding']);
    $mood = mysqli_real_escape_string($conn, $_POST['mood']);

    if ($week < 0 || $temp < 0) {
        $save_status = "error";
        setFlashMessage('error', 'Values cannot be negative.');
    } else {
        // Logic for Status
        $status = 'safe';
        if ($temp > 38 || $bleeding === 'heavy' || $pain === 'severe' || $wound === 'no' || $mood === 'very sad') {
            $status = 'danger';
        } elseif ($mood === 'sad' || $pain === 'yes') {
            $status = 'warning';
        }

        $sql = "INSERT INTO symptom_logs (user_id, week_postpartum, temperature, pain_level, wound_condition, bleeding_status, mood_status, result_status)
                VALUES ('$user_id', '$week', '$temp', '$pain', '$wound', '$bleeding', '$mood', '$status')";

        if (mysqli_query($conn, $sql)) {
            $save_status = "success";
            // We don't redirect immediately because we want to show the result card
        } else {
            $save_status = "error";
            setFlashMessage('error', 'Failed to save log: ' . mysqli_error($conn));
        }
    }
}

require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    /* Zen Garden Styles Specific to Symptom Checker */
    .zen-wrapper {
        display: flex;
        justify-content: center;
        padding: 2rem 1rem;
    }

    .zen-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.02);
        box-shadow: 0 20px 60px -20px rgba(107, 142, 130, 0.15);
        border-radius: 32px;
        padding: 3rem;
        width: 100%;
        max-width: 800px;
        position: relative;
    }

    .zen-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .zen-icon {
        width: 80px;
        height: 80px;
        background: #E8F0EB;
        color: #6B8E82;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .zen-card {
            padding: 1.5rem;
        }
    }

    /* Result Card Styles */
    #resultArea {
        margin-top: 2rem;
        /* display: none; Managed by PHP/JS */
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .result-card {
        padding: 2rem;
        border-radius: 20px;
        text-align: center;
    }

    .result-safe { background: #F1F8F5; color: #2F5D48; }
    .result-warning { background: #FFF8F1; color: #9C4221; }
    .result-danger { background: #FEF2F2; color: #991B1B; }
</style>

<!-- Decorative Blob -->
<div class="blob blob-1" style="width: 500px; height: 500px; top: -100px; left: -100px; opacity: 0.5;"></div>

<div class="zen-wrapper">
    <div class="zen-card">
        <!-- Back Button -->
        <div class="d-flex align-items-center mb-4">
            <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="mb-0" style="font-family: 'Playfair Display', serif; color: #4A5D53;">Symptom Checker</h1>
                <p class="text-muted mb-0">Answer a few questions to check your recovery status.</p>
            </div>
        </div>

        <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; displayFlashMessage(); ?>

        <form method="POST" action="">
            <div class="form-grid">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">
                        <i class="fas fa-calendar-week me-2 text-primary"></i>Weeks Postpartum
                    </label>
                    <input type="number" name="week" class="form-control" min="0" max="52" 
                           placeholder="e.g. 2" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">
                        <i class="fas fa-thermometer-half me-2 text-primary"></i>Temperature (°C)
                    </label>
                    <input type="number" name="temp" class="form-control" step="0.1" min="0" 
                           placeholder="e.g. 36.5" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Pain Level</label>
                    <select name="pain" class="form-select" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="no">No Pain</option>
                        <option value="yes">Yes, Mild</option>
                        <option value="severe">Yes, Severe</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Wound Condition</label>
                    <select name="wound" class="form-select" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="yes">Healed / Dry</option>
                        <option value="no">Red / Swollen / Wet</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Bleeding</label>
                    <select name="bleeding" class="form-select" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="none">None / Light Spotting</option>
                        <option value="light">Moderate</option>
                        <option value="heavy">Heavy (Soaking pads)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Mood</label>
                    <select name="mood" class="form-select" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="happy">Calm / Happy</option>
                        <option value="sad">Sad / Anxious</option>
                        <option value="very sad">Overwhelmed / Hopeless</option>
                    </select>
                </div>
            </div>

            <!-- Result Area -->
            <div id="resultArea" style="display: <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo ($save_status == 'success') ? 'block' : 'none'; ?>;">
                <div id="resultCard" class="result-card">
                    <div id="resultIcon" style="font-size: 2.5rem; margin-bottom: 1rem;"></div>
                    <h3 id="resultTitle" style="margin-bottom: 0.5rem; font-family: 'Playfair Display', serif;"></h3>
                    <p id="resultText"></p>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">
                    Check Now & Save <i class="fas fa-check-circle ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; if ($save_status == 'success'): ?>
    const temp = <?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo $temp; ?>;
    const bleeding = "<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo $bleeding; ?>";
    const pain = "<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo $pain; ?>";
    const wound = "<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo $wound; ?>";
    const mood = "<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo $mood; ?>";

    const resultCard = document.getElementById("resultCard");
    const resultIcon = document.getElementById("resultIcon");
    const resultTitle = document.getElementById("resultTitle");
    const resultText = document.getElementById("resultText");

    let status = 'safe';
    let title = "All Looks Good";
    let message = "Your recovery appears to be on track. Continue your healthy habits!";
    let icon = "<i class='fas fa-check-circle'></i>";
    let cardClass = "result-card result-safe";

    if (temp > 38 || bleeding === 'heavy' || pain === 'severe' || wound === 'no' || mood === 'very sad') {
        status = 'danger';
        title = "Attention Needed";
        message = "Some symptoms require medical attention. Please consult a doctor immediately.";
        icon = "<i class='fas fa-exclamation-circle'></i>";
        cardClass = "result-card result-danger";
    } else if (mood === 'sad' || pain === 'yes') {
        status = 'warning';
        title = "Monitor Closely";
        message = "Keep an eye on these symptoms. Take rest and stay hydrated.";
        icon = "<i class='fas fa-info-circle'></i>";
        cardClass = "result-card result-warning";
    }

    resultCard.className = cardClass;
    resultIcon.innerHTML = icon;
    resultTitle.innerText = title;
    resultText.innerText = message;
    
    // Scroll to result
    setTimeout(() => {
        document.getElementById("resultArea").scrollIntoView({ behavior: 'smooth' });
    }, 100);
<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; endif; ?>
</script>

<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


