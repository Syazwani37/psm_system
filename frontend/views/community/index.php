<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
/**
 * PSM System - Community Support
 * Frontend View: Community Module
 */

$page_title = "Mom's Circle - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';
// Allow both mother and professional roles
requireLogin(); 
require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
        text-align: center;
    }

    .chat-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.02);
        border-radius: 24px;
        padding: 0;
        box-shadow: 0 10px 40px rgba(107, 142, 130, 0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 600px;
        text-align: left;
    }

    .chat-header {
        padding: 1.5rem;
        border-bottom: 1px solid #F0F0F0;
        background: #FAFAFA;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .chat-header .icon {
        width: 40px;
        height: 40px;
        background: #E8F0EB;
        color: #6B8E82;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chat-header h2 {
        font-size: 1.25rem;
        color: #2C2C2C;
        margin: 0;
        font-weight: 600;
        font-family: 'Playfair Display', serif;
    }

    .messages {
        flex: 1;
        padding: 2rem;
        overflow-y: auto;
        background: white;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message {
        max-width: 80%;
        padding: 1rem 1.25rem;
        border-radius: 16px;
        font-size: 0.95rem;
        line-height: 1.5;
        position: relative;
    }

    /* Incoming Message */
    .message:not(.user) {
        background: #F8F8F8;
        color: #5C5C5C;
        align-self: flex-start;
        border-bottom-left-radius: 4px;
    }

    /* User Message */
    .message.user {
        background: #E8F0EB;
        color: #2F5D48;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }

    .message.admin {
        background: #FFF8F1;
        color: #8C6B5D;
        border: 1px solid #EFEBE0;
        align-self: center;
        border-radius: 12px;
        font-size: 0.9rem;
        margin: 1rem 0;
    }

    .chat-input-area {
        padding: 1.5rem;
        background: #FAFAFA;
        border-top: 1px solid #F0F0F0;
    }

    .chat-input-form {
        display: flex;
        gap: 1rem;
    }

    .chat-input {
        flex: 1;
        padding: 1rem 1.5rem;
        border-radius: 50px;
        border: 1px solid #E0E0E0;
        background: white;
        font-family: 'Inter', sans-serif;
        font-size: 1rem;
        outline: none;
        transition: 0.2s;
    }

    .chat-input:focus {
        border-color: #8DA399;
        box-shadow: 0 0 0 3px rgba(141, 163, 153, 0.1);
    }

    .send-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #8DA399;
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: 0.2s;
    }

    .send-btn:hover {
        background: #6B8E82;
        transform: scale(1.05);
    }
</style>

<!-- Decoration -->
<div class="blob blob-1" style="top: -200px; left: -200px; opacity: 0.3;"></div>

<div class="page-container">
    <div class="d-flex align-items-center mb-5">
        <a href="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="mb-0" style="font-family: 'Playfair Display', serif; color: #4A5D53;">
                <i class="fas fa-heart me-2" style="color: #E6B8B8;"></i> Mom's Circle
            </h1>
            <p class="text-muted mb-0">A safe, judgment-free space to share your journey.</p>
        </div>
    </div>

    <!-- Official Announcements -->
    <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
     // Ensure DB is available
    $ann_sql = "SELECT title, message, type, created_at FROM announcements ORDER BY created_at DESC LIMIT 3";
    $ann_result = $conn->query($ann_sql);
    
    if ($ann_result && $ann_result->num_rows > 0) {
        ?>
        <div class="mb-4 text-start">
            <h5 class="mb-3 text-secondary"><i class="fas fa-bullhorn me-2"></i>Official Updates</h5>
            <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
            while ($ann = $ann_result->fetch_assoc()) {
                $alertClass = 'alert-info';
                $icon = 'fa-info-circle';
                
                if ($ann['type'] == 'warning') { $alertClass = 'alert-warning'; $icon = 'fa-exclamation-triangle'; }
                if ($ann['type'] == 'danger') { $alertClass = 'alert-danger'; $icon = 'fa-exclamation-circle'; }
                if ($ann['type'] == 'success') { $alertClass = 'alert-success'; $icon = 'fa-check-circle'; }
                ?>
                <div class="alert <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $alertClass; ?> border-0 rounded-3 shadow-sm mb-2 d-flex align-items-start gap-3" role="alert">
                    <i class="fas <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $icon; ?> mt-1 fa-lg"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1"><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo escape($ann['title']); ?></h6>
                        <p class="mb-0 small opacity-75"><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo escape($ann['message']); ?></p>
                        <small class="text-muted" style="font-size: 0.7em;"><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo date('M d, h:i A', strtotime($ann['created_at'])); ?></small>
                    </div>
                </div>
                <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
            }
            ?>
        </div>
        <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
    }
    ?>

    <div class="chat-card">
        <div class="chat-header">
            <div class="icon"><i class="fas fa-users"></i></div>
            <div>
                <h2>General Support</h2>
            </div>
        </div>

        <div class="messages" id="chat-messages">
            <div class="message admin"><i class="fas fa-shield-alt me-2"></i> Welcome! This is a safe space for sharing and support.</div>
            <div class="message">Hi everyone! Just wanted to share my postpartum experience 😊</div>
            <div class="message user">Same here! I'm 4 weeks postpartum and loving this space.</div>
            <div class="message">Has anyone tried the new yoga routine in the Recovery Tracker? It's amazing! 🧘‍♀️</div>
            <div class="message user">Yes! It really helped with my back pain. Highly recommend it.</div>
            <div class="message">I'm struggling a bit with sleep schedules. Any tips? 😴</div>
            <div class="message user">Try the "Rest Well" tip from the dashboard - sleep when baby sleeps! It's a lifesaver.</div>
            <div class="message">Thanks! needed to hear that today. 💕</div>
        </div>

        <div class="chat-input-area">
            <form class="chat-input-form" onsubmit="sendMessage(event)">
                <input type="text" id="chat-input" class="chat-input" placeholder="Type a message..." required autocomplete="off">
                <button type="submit" class="send-btn"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>

<script>
    function sendMessage(event) {
        event.preventDefault();
        const input = document.getElementById('chat-input');
        const messageText = input.value.trim();
        if (!messageText) return;

        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', 'user');
        messageDiv.textContent = messageText;

        const container = document.getElementById('chat-messages');
        container.appendChild(messageDiv);
        input.value = '';

        // Auto-scroll to latest message
        container.scrollTop = container.scrollHeight;
    }
</script>

<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


