<?php
/**
 * PSM System - Mom's Journal
 * Frontend View: Journal Module
 */

$page_title = "Mom's Journal - PSM System";
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/helpers/functions.php';

requireLogin('mother');

$user_id = $_SESSION['user_id'];

// Handle Journal Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mood = $_POST['mood'] ?? 'happy';
    $entry = $_POST['entry'] ?? '';
    
    if (!empty($entry)) {
        $stmt = $conn->prepare("INSERT INTO moms_journal (user_id, mood, entry_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $mood, $entry);
        
        if ($stmt->execute()) {
            setFlashMessage('success', 'Journal entry saved successfully!');
        } else {
            setFlashMessage('error', 'Failed to save entry.');
        }
        $stmt->close();
        
        // Refresh to prevent resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch Entries
$entries = [];
$result = mysqli_query($conn, "SELECT * FROM moms_journal WHERE user_id = $user_id ORDER BY created_at DESC");
while($row = mysqli_fetch_assoc($result)) {
    $entries[] = $row;
}

// Simple Mood Analytics
$mood_counts = [];
foreach($entries as $e) {
    // Check if mood key exists, default null handling
    $m = $e['mood'] ?? 'unknown';
    if(!isset($mood_counts[$m])) $mood_counts[$m] = 0;
    $mood_counts[$m]++;
}
arsort($mood_counts);
$dominant_mood = !empty($mood_counts) ? array_key_first($mood_counts) : null;

require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/header.php';
?>

<style>
    /* Journal Specific Styles */
    .mood-selector { 
        display: flex; 
        justify-content: space-between; 
        margin-bottom: 1.5rem; 
        gap: 0.5rem;
    }
    
    .mood-option { 
        text-align: center; 
        cursor: pointer; 
        transition: transform 0.2s; 
        flex: 1;
        background: #F8F9FA;
        border-radius: 12px;
        padding: 0.5rem;
        border: 2px solid transparent;
    }
    
    .mood-option:hover { 
        transform: translateY(-3px); 
        background: #FFF3E0;
    }
    
    .mood-option input { display: none; }
    
    .mood-option i { 
        font-size: 2rem; 
        color: #BDBDBD; 
        display: block; 
        margin-bottom: 0.5rem; 
        transition: color 0.2s;
    }
    
    .mood-option input:checked + i { color: #FFB74D; }
    .mood-option input:checked ~ .mood-label { color: #F57C00; font-weight: bold; }
    .mood-option:has(input:checked) { 
        border-color: #FFB74D; 
        background: #FFF8E1;
    }
    
    .mood-label { 
        font-size: 0.8rem; 
        color: #757575;
    }
    
    .summary-banner { 
        background: linear-gradient(135deg, #FFB74D, #FFA726); 
        color: white; 
        padding: 2rem; 
        border-radius: 20px; 
        margin-bottom: 2rem; 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        box-shadow: 0 10px 20px rgba(255, 152, 0, 0.2);
    }
    
    .entry-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .entry-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-soft);
    }
    
    .entry-card.mood-happy { border-left-color: #FFB74D; }
    .entry-card.mood-sad { border-left-color: #90A4AE; }
    .entry-card.mood-anxious { border-left-color: #E57373; }
    .entry-card.mood-calm { border-left-color: #81C784; }
    .entry-card.mood-tired { border-left-color: #BA68C8; }
</style>

<div class="container py-5" style="max-width: 800px;">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="<?php echo BASE_URL; ?>/frontend/views/dashboard/mother.php" class="btn btn-outline-secondary btn-sm me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="mb-0" style="font-family: 'Playfair Display', serif;">My Journal</h2>
    </div>

    <?php displayFlashMessage(); ?>

    <!-- Analytics Summary -->
    <?php if($dominant_mood): ?>
    <div class="summary-banner">
        <div>
            <div style="font-size: 1rem; opacity: 0.9;">Your Dominant Mood</div>
            <div style="font-size: 2.5rem; font-weight: 700; line-height: 1.2;">
                <?php echo ucfirst($dominant_mood); ?>
            </div>
            <div class="mt-2 text-white-50 small">Based on your recent entries</div>
        </div>
        <i class="fas fa-chart-pie" style="font-size: 4rem; opacity: 0.3;"></i>
    </div>
    <?php endif; ?>

    <!-- New Entry Form -->
    <div class="card border-0 shadow-sm mb-5" style="border-radius: 20px;">
        <div class="card-body p-4">
            <h5 class="card-title mb-4">How are you feeling today?</h5>
            
            <form method="POST">
                <div class="mood-selector">
                    <label class="mood-option">
                        <input type="radio" name="mood" value="happy" required>
                        <i class="fas fa-smile"></i>
                        <span class="mood-label">Happy</span>
                    </label>
                    <label class="mood-option">
                        <input type="radio" name="mood" value="calm">
                        <i class="fas fa-meh"></i>
                        <span class="mood-label">Calm</span>
                    </label>
                    <label class="mood-option">
                        <input type="radio" name="mood" value="tired">
                        <i class="fas fa-tired"></i>
                        <span class="mood-label">Tired</span>
                    </label>
                    <label class="mood-option">
                        <input type="radio" name="mood" value="sad">
                        <i class="fas fa-frown"></i>
                        <span class="mood-label">Sad</span>
                    </label>
                    <label class="mood-option">
                        <input type="radio" name="mood" value="anxious">
                        <i class="fas fa-grimace"></i>
                        <span class="mood-label">Anxious</span>
                    </label>
                </div>
                
                <div class="mb-3">
                    <textarea name="entry" class="form-control bg-light border-0" 
                              rows="4" 
                              placeholder="Write your thoughts here... this space is just for you." 
                              style="border-radius: 15px; padding: 1rem; resize: none;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-warning text-white w-100 py-2 rounded-pill fw-bold">
                    Save Entry <i class="fas fa-pen-fancy ms-2"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- History -->
    <h5 class="mb-3 ps-2">Recent Entries</h5>
    
    <?php if(empty($entries)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-book-open fa-3x mb-3 opacity-25"></i>
            <p>No journal entries yet. Start writing today!</p>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-3">
            <?php foreach($entries as $e): ?>
                <?php 
                $mood = $e['mood'] ?? 'happy';
                $icons = [
                    'happy' => 'fa-smile text-warning', 'calm' => 'fa-meh text-success', 
                    'tired' => 'fa-tired text-secondary', 'sad' => 'fa-frown text-primary', 
                    'anxious' => 'fa-grimace text-danger'
                ];
                $iconClass = $icons[$mood] ?? 'fa-circle text-muted';
                ?>
                <div class="card border-0 shadow-sm entry-card mood-<?php echo escape($mood); ?>" style="border-radius: 16px;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <small class="text-muted fw-bold">
                                <?php echo formatDateTime($e['created_at']); ?>
                            </small>
                            <i class="fas <?php echo $iconClass; ?> fa-lg" title="<?php echo ucfirst($mood); ?>"></i>
                        </div>
                        <p class="card-text mb-0" style="white-space: pre-wrap; color: #555;"><?php echo escape($e['entry_text']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/psm_system/backend/includes/footer.php'; ?>
