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
        display: none; /* Initially hidden, shown by JavaScript */
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

    .result-low { background: #F1F8F5; color: #2F5D48; }
    .result-medium { background: #FFF8F1; color: #9C4221; }
    .result-high { background: #FEF2F2; color: #991B1B; }
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

        <form id="symptomForm">
            <div class="form-grid">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">
                        <i class="fas fa-calendar-week me-2 text-primary"></i>Weeks Postpartum
                    </label>
                    <input type="number" id="week" class="form-control" min="0" max="52"
                           placeholder="e.g. 2" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">
                        <i class="fas fa-thermometer-half me-2 text-primary"></i>Temperature (°C)
                    </label>
                    <input type="number" id="temp" class="form-control" step="0.1" min="0"
                           placeholder="e.g. 36.5" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Pain Level</label>
                    <select id="pain" class="form-select" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="no">No Pain</option>
                        <option value="yes">Yes, Mild</option>
                        <option value="severe">Yes, Severe</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Wound Condition</label>
                    <select id="wound" class="form-select" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="yes">Healed / Dry</option>
                        <option value="no">Red / Swollen / Wet</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Bleeding</label>
                    <select id="bleeding" class="form-select" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="none">None / Light Spotting</option>
                        <option value="light">Moderate</option>
                        <option value="heavy">Heavy (Soaking pads)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Mood</label>
                    <select id="mood" class="form-select" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="happy">Calm / Happy</option>
                        <option value="sad">Sad / Anxious</option>
                        <option value="very sad">Overwhelmed / Hopeless</option>
                    </select>
                </div>

                <div class="mb-3" style="grid-column: 1 / -1;">
                    <label class="form-label fw-bold small text-muted">
                        <i class="fas fa-notes-medical me-2 text-primary"></i>Specific Symptoms (Select all that apply)
                    </label>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="breast_pain" id="sym_breast">
                                <label class="form-check-label" for="sym_breast">Breast Pain / Lumps</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="severe_headache" id="sym_head">
                                <label class="form-check-label" for="sym_head">Severe Headache</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="vision_problems" id="sym_vision">
                                <label class="form-check-label" for="sym_vision">Blurred Vision / Spots</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="difficulty_breathing" id="sym_breath">
                                <label class="form-check-label" for="sym_breath">Difficulty Breathing</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="severe_chest_pain" id="sym_chest">
                                <label class="form-check-label" for="sym_chest">Chest Pain</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="foul_smelling_discharge" id="sym_smell">
                                <label class="form-check-label" for="sym_smell">Foul Smelling Discharge</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="dizziness" id="sym_dizz">
                                <label class="form-check-label" for="sym_dizz">Dizziness / Fainting</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="sleep_difficulties" id="sym_sleep">
                                <label class="form-check-label" for="sym_sleep">Sleep Difficulties (Insomnia)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="increased_heart_rate" id="sym_heart">
                                <label class="form-check-label" for="sym_heart">Fast Heart Rate / Palpitations</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="mild_fatigue" id="sym_fatigue">
                                <label class="form-check-label" for="sym_fatigue">General Fatigue / Tiredness</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Area -->
            <div id="resultArea">
                <div id="resultCard" class="result-card">
                    <div id="resultIcon" style="font-size: 2.5rem; margin-bottom: 1rem;"></div>
                    <h3 id="resultTitle" style="margin-bottom: 0.5rem; font-family: 'Playfair Display', serif;"></h3>
                    <p id="resultText"></p>
                    <div id="resultRecommendation" class="mt-3"></div>
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
    document.getElementById('symptomForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form values
        const week = document.getElementById('week').value;
        const temp = parseFloat(document.getElementById('temp').value);
        const pain = document.getElementById('pain').value;
        const wound = document.getElementById('wound').value;
        const bleeding = document.getElementById('bleeding').value;
        const mood = document.getElementById('mood').value;

        // Prepare symptoms array based on selections
        const symptoms = [];

        if (pain !== 'no') symptoms.push(pain === 'severe' ? 'severe_pain' : 'mild_pain');
        if (wound === 'no') symptoms.push('wound_issues');
        if (bleeding === 'heavy') symptoms.push('heavy_bleeding');
        if (bleeding === 'light') symptoms.push('moderate_bleeding');
        if (mood === 'sad') symptoms.push('mood_changes');
        if (mood === 'very sad') symptoms.push('severe_mood_changes');
        if (temp > 38.0) symptoms.push('fever');

        // Add checked specific symptoms
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
            symptoms.push(checkbox.value);
        });

        // Prepare data for API
        const data = {
            week_postpartum: parseInt(week),
            temperature: temp,
            pain_level: pain === 'severe' ? 8 : (pain === 'yes' ? 5 : 0),
            bleeding_status: bleeding,
            wound_condition: wound,
            mood_status: mood,
            symptoms: symptoms
        };

        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Assessing...';
        submitBtn.disabled = true;

        // Call the decision tree API
        fetch('<?php echo BASE_URL; ?>/backend/api/symptom_checker/assess.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                displayAssessmentResult(result.assessment);
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while assessing symptoms.');
        })
        .finally(() => {
            // Restore button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    function displayAssessmentResult(assessment) {
        const resultCard = document.getElementById("resultCard");
        const resultIcon = document.getElementById("resultIcon");
        const resultTitle = document.getElementById("resultTitle");
        const resultText = document.getElementById("resultText");
        const resultRecommendation = document.getElementById("resultRecommendation");

        // Set result based on risk level
        let title = "";
        let message = "";
        let icon = "";
        let cardClass = "result-card ";

        switch(assessment.risk_level) {
            case 'high':
                title = "Immediate Attention Needed";
                message = "Your symptoms indicate a potentially serious condition.";
                icon = "<i class='fas fa-exclamation-triangle'></i>";
                cardClass += "result-high";
                break;
            case 'medium':
                title = "Medical Consultation Recommended";
                message = "Your symptoms warrant professional evaluation.";
                icon = "<i class='fas fa-exclamation-circle'></i>";
                cardClass += "result-medium";
                break;
            case 'low':
            default:
                title = "Recovery on Track";
                message = "Your symptoms appear to be within normal recovery parameters.";
                icon = "<i class='fas fa-check-circle'></i>";
                cardClass += "result-low";
                break;
        }

        // Update the display
        resultCard.className = cardClass;
        resultIcon.innerHTML = icon;
        resultTitle.innerText = title;
        resultText.innerText = message;

        // Add specific recommendation
        resultRecommendation.innerHTML = `
            <div class="alert mt-3" style="background: rgba(255,255,255,0.7); border-radius: 12px; padding: 1rem;">
                <strong>Recommendation:</strong> ${assessment.recommendation}
                ${assessment.action === 'emergency_consultation' ?
                    '<div class="mt-2"><a href="<?php echo BASE_URL; ?>/frontend/views/consultations/book.php" class="btn btn-danger btn-sm">Book Emergency Consultation</a></div>' :
                    assessment.action === 'schedule_consultation' ?
                    '<div class="mt-2"><a href="<?php echo BASE_URL; ?>/frontend/views/consultations/book.php" class="btn btn-warning btn-sm">Schedule Consultation</a></div>' :
                    '<div class="mt-2"><a href="<?php echo BASE_URL; ?>/frontend/views/consultations/book.php" class="btn btn-primary btn-sm">Book Consultation</a></div>'}
            </div>
        `;

        // Show result area and scroll to it
        document.getElementById("resultArea").style.display = "block";
        setTimeout(() => {
            document.getElementById("resultArea").scrollIntoView({ behavior: 'smooth' });
        }, 100);
    }
</script>

<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


