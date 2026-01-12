<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
/**
 * PSM System - EPDS Screening
 * Frontend View: Mental Health Module
 */

$page_title = "Mental Health Screening - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';

require_once BASE_PATH . '/backend/helpers/functions.php';

requireLogin('mother');

$user_id = $_SESSION['user_id'];
$show_result = false;
$risk_level = 'Low';
$total_score = 0;
$message = "";
$alert_color = "success";
$q10_score = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $total_score = 0;
    $responses = [];

    // Calculate score
    for ($i = 1; $i <= 10; $i++) {
        $key = "q$i";
        $val = isset($_POST[$key]) ? intval($_POST[$key]) : 0;
        $total_score += $val;
        $responses[$key] = $val;
    }

    $q10_score = isset($_POST['q10']) ? intval($_POST['q10']) : 0;
    
    // Determine Risk Level
    if ($q10_score > 0) {
        $risk_level = 'High';
        $message = "You indicated thoughts of self-harm. Please seek immediate help.";
        $alert_color = "danger";
    } elseif ($total_score >= 13) {
        $risk_level = 'High';
        $message = "Your score suggests you may be experiencing significant postnatal depression.";
        $alert_color = "danger";
    } elseif ($total_score >= 10) {
        $risk_level = 'Medium';
        $message = "You are showing some signs of distress. It might be helpful to talk to someone.";
        $alert_color = "warning";
    } else {
        $message = "Your score suggests you are adjusting well.";
        $alert_color = "success";
    }

    // Save to Database
    $responses_json = json_encode($responses);
    
    $stmt = $conn->prepare("INSERT INTO epds_responses (user_id, score, risk_level, responses) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $total_score, $risk_level, $responses_json);
    
    if ($stmt->execute()) {
        $show_result = true;
    } else {
        setFlashMessage('error', 'Failed to save results. Please try again.');
    }
    $stmt->close();
}

require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    .question-block {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        border: 1px solid #eee;
    }

    .question-text {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        color: #37474F;
    }

    .options label {
        display: block;
        padding: 0.8rem 1rem;
        margin-bottom: 0.5rem;
        background: #FAFAFA;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .options label:hover {
        background: #FFF0F4; /* Light pink hover */
        border-color: #FF80AB;
    }

    .options input[type="radio"] {
        margin-right: 10px;
        accent-color: #FF80AB;
    }
    
    .options input[type="radio"]:checked + span {
        font-weight: bold;
        color: #C2185B;
    }

    /* Score Circle */
    .score-circle { 
        width: 150px; 
        height: 150px; 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        margin: 0 auto 1.5rem; 
        font-size: 3rem; 
        font-weight: 700; 
        color: white; 
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .bg-success-custom { background: linear-gradient(135deg, #66BB6A, #43A047); }
    .bg-warning-custom { background: linear-gradient(135deg, #FFA726, #FB8C00); }
    .bg-danger-custom { background: linear-gradient(135deg, #EF5350, #E53935); }
    
    .result-card {
        text-align: center;
        padding: 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
</style>

<div class="container py-5" style="max-width: 800px;">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/dashboard/mother.php" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="mb-0" style="font-family: 'Playfair Display', serif; color: #880E4F;">Mental Health Screening</h2>
    </div>

    <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; displayFlashMessage(); ?>

    <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; if ($show_result): ?>
        <!-- Result View -->
        <div class="result-card">
            <div class="score-circle bg-<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $alert_color; ?>-custom">
                <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $total_score; ?>
            </div>
            
            <h2 class="mb-3">Result: <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $risk_level; ?> Risk</h2>
            <p class="lead text-muted mb-4"><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $message; ?></p>

            <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; if ($risk_level == 'High' || $q10_score > 0): ?>
                <div class="alert alert-danger border-danger">
                    <i class="fas fa-exclamation-circle fa-lg me-2"></i> <strong>Important:</strong> Please consult a doctor immediately.<br>
                    For urgent help, call <strong>999</strong> or your local crisis line.
                </div>
                <div class="d-grid gap-2 d-sm-flex justify-content-center mt-4">
                    <a href="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/consultations/book.php" class="btn btn-danger btn-lg px-5 rounded-pill">
                        Book Consultation Now
                    </a>
                    <a href="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/resources/support.php" class="btn btn-outline-danger btn-lg px-5 rounded-pill">
                        Find Support
                    </a>
                </div>
            <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; else: ?>
                <div class="d-grid gap-2 d-sm-flex justify-content-center mt-4">
                    <a href="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/views/resources/articles.php" class="btn btn-success btn-lg px-5 rounded-pill">
                        Read Wellness Tips
                    </a>
                    <a href="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $_SERVER['PHP_SELF']; ?>" class="btn btn-outline-secondary btn-lg px-5 rounded-pill">
                        Take Test Again
                    </a>
                </div>
            <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; endif; ?>
        </div>

    <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; else: ?>
        <!-- Questionnaire Form -->
        <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #FFF0F5;">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-5">
                    <h3 class="mb-3">How are you feeling, mama?</h3>
                    <p class="text-muted">Please select the answer that comes closest to how you have felt <strong>in the past 7 days</strong>, not just today. Be honest—this is for you.</p>
                </div>

                <form method="POST">
                    
                    <!-- Q1 -->
                    <div class="question-block">
                        <div class="question-text">1. I have been able to laugh and see the funny side of things.</div>
                        <div class="options">
                            <label><input type="radio" name="q1" value="0" required> <span>As much as I always could</span></label>
                            <label><input type="radio" name="q1" value="1"> <span>Not quite so much now</span></label>
                            <label><input type="radio" name="q1" value="2"> <span>Definitely not so much now</span></label>
                            <label><input type="radio" name="q1" value="3"> <span>Not at all</span></label>
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="question-block">
                        <div class="question-text">2. I have looked forward with enjoyment to things.</div>
                        <div class="options">
                            <label><input type="radio" name="q2" value="0" required> <span>As much as I ever did</span></label>
                            <label><input type="radio" name="q2" value="1"> <span>Rather less than I used to</span></label>
                            <label><input type="radio" name="q2" value="2"> <span>Definitely less than I used to</span></label>
                            <label><input type="radio" name="q2" value="3"> <span>Hardly at all</span></label>
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div class="question-block">
                        <div class="question-text">3. I have blamed myself unnecessarily when things went wrong.</div>
                        <div class="options">
                            <label><input type="radio" name="q3" value="3" required> <span>Yes, most of the time</span></label>
                            <label><input type="radio" name="q3" value="2"> <span>Yes, some of the time</span></label>
                            <label><input type="radio" name="q3" value="1"> <span>Not very often</span></label>
                            <label><input type="radio" name="q3" value="0"> <span>No, never</span></label>
                        </div>
                    </div>

                    <!-- Q4 -->
                    <div class="question-block">
                        <div class="question-text">4. I have been anxious or worried for no good reason.</div>
                        <div class="options">
                            <label><input type="radio" name="q4" value="0" required> <span>No, not at all</span></label>
                            <label><input type="radio" name="q4" value="1"> <span>Hardly ever</span></label>
                            <label><input type="radio" name="q4" value="2"> <span>Yes, sometimes</span></label>
                            <label><input type="radio" name="q4" value="3"> <span>Yes, very often</span></label>
                        </div>
                    </div>

                    <!-- Q5 -->
                    <div class="question-block">
                        <div class="question-text">5. I have felt scared or panicky for no very good reason.</div>
                        <div class="options">
                            <label><input type="radio" name="q5" value="3" required> <span>Yes, quite a lot</span></label>
                            <label><input type="radio" name="q5" value="2"> <span>Yes, sometimes</span></label>
                            <label><input type="radio" name="q5" value="1"> <span>No, not much</span></label>
                            <label><input type="radio" name="q5" value="0"> <span>No, not at all</span></label>
                        </div>
                    </div>

                    <!-- Q6 -->
                    <div class="question-block">
                        <div class="question-text">6. Things have been getting on top of me.</div>
                        <div class="options">
                            <label><input type="radio" name="q6" value="3" required> <span>Yes, most of the time I haven't been able to cope</span></label>
                            <label><input type="radio" name="q6" value="2"> <span>Yes, sometimes I haven't been coping as well as usual</span></label>
                            <label><input type="radio" name="q6" value="1"> <span>No, most of the time I have coped quite well</span></label>
                            <label><input type="radio" name="q6" value="0"> <span>No, I have been coping as well as ever</span></label>
                        </div>
                    </div>

                    <!-- Q7 -->
                    <div class="question-block">
                        <div class="question-text">7. I have been so unhappy that I have had difficulty sleeping.</div>
                        <div class="options">
                            <label><input type="radio" name="q7" value="3" required> <span>Yes, most of the time</span></label>
                            <label><input type="radio" name="q7" value="2"> <span>Yes, sometimes</span></label>
                            <label><input type="radio" name="q7" value="1"> <span>Not very often</span></label>
                            <label><input type="radio" name="q7" value="0"> <span>No, not at all</span></label>
                        </div>
                    </div>

                    <!-- Q8 -->
                    <div class="question-block">
                        <div class="question-text">8. I have felt sad or miserable.</div>
                        <div class="options">
                            <label><input type="radio" name="q8" value="3" required> <span>Yes, most of the time</span></label>
                            <label><input type="radio" name="q8" value="2"> <span>Yes, quite often</span></label>
                            <label><input type="radio" name="q8" value="1"> <span>Not very often</span></label>
                            <label><input type="radio" name="q8" value="0"> <span>No, not at all</span></label>
                        </div>
                    </div>

                    <!-- Q9 -->
                    <div class="question-block">
                        <div class="question-text">9. I have been so unhappy that I have been crying.</div>
                        <div class="options">
                            <label><input type="radio" name="q9" value="3" required> <span>Yes, most of the time</span></label>
                            <label><input type="radio" name="q9" value="2"> <span>Yes, quite often</span></label>
                            <label><input type="radio" name="q9" value="1"> <span>Only occasionally</span></label>
                            <label><input type="radio" name="q9" value="0"> <span>No, never</span></label>
                        </div>
                    </div>

                    <!-- Q10 -->
                    <div class="question-block border-danger border-2">
                        <div class="question-text text-danger">10. The thought of harming myself has occurred to me.</div>
                        <div class="options">
                            <label><input type="radio" name="q10" value="3" required> <span>Yes, quite often</span></label>
                            <label><input type="radio" name="q10" value="2"> <span>Sometimes</span></label>
                            <label><input type="radio" name="q10" value="1"> <span>Hardly ever</span></label>
                            <label><input type="radio" name="q10" value="0"> <span>Never</span></label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold text-uppercase letter-spacing-1" 
                            style="background: linear-gradient(135deg, #FF80AB, #F48FB1); border: none;">
                        See My Results <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; endif; ?>
</div>

<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


