<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Recovery Tracker
 * Frontend View: Recovery Module
 */

$page_title = "Recovery Tracker - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';
requireLogin('mother');
require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .milestone-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.02);
        border-radius: 20px;
        padding: 1.5rem 2rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-align: left;
    }

    .milestone-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(107, 142, 130, 0.15);
    }

    .milestone-content h3 {
        font-size: 1.1rem;
        color: #2C2C2C;
        margin-bottom: 0.25rem;
    }

    .milestone-content p {
        color: #949494;
        font-size: 0.9rem;
        margin-bottom: 0;
    }

    /* Checkbox Styling */
    .status-check {
        width: 24px;
        height: 24px;
        border: 2px solid #B4C5BD;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: 0.2s;
        flex-shrink: 0;
        margin-left: 1rem;
    }

    .milestone-card.completed .status-check {
        background: #8DA399;
        border-color: #8DA399;
    }

    .milestone-card.completed h3 {
        text-decoration: line-through;
        color: #B4C5BD;
    }
    
    .progress-wrapper {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }
    
    .progress-bar-bg {
        background: #F0F0F0;
        height: 10px;
        border-radius: 10px;
        margin-top: 10px;
        overflow: hidden;
    }
    
    .progress-bar-fill {
        background: #8DA399;
        height: 100%;
        width: 0%;
        transition: width 0.5s ease;
    }
</style>

<!-- Decoration -->
<div class="blob blob-2" style="top: -100px; right: -100px; opacity: 0.4;"></div>

<div class="page-container">
    <!-- Header -->
    <div class="d-flex align-items-center mb-5">
        <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="mb-0" style="font-family: 'Playfair Display', serif; color: #4A5D53;">Recovery Tracker</h1>
            <p class="text-muted mb-0">Small steps every day lead to a healthy recovery.</p>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="progress-wrapper">
        <h5 class="mb-1 text-secondary">Your Progress</h5>
        <h2 class="mb-0" style="color: #8DA399;" id="progressText">0%</h2>
        <div class="progress-bar-bg">
            <div class="progress-bar-fill" id="progressBar"></div>
        </div>
    </div>

    <!-- Milestone Cards -->
    <div id="milestones" class="milestone-cards">
        <!-- Dynamic content inserted by JS -->
    </div>
</div>

<script>
    const milestones = [
        {
            title: "Week 1: Postnatal Check-In",
            description: "Complete your first postpartum check-up with your OB-GYN."
        },
        {
            title: "Week 2: Pelvic Floor Exercises",
            description: "Start gentle pelvic floor exercises to support recovery."
        },
        {
            title: "Week 3: Nutrition Plan Started",
            description: "Follow your personalized nutrition plan for energy and healing."
        },
        {
            title: "Week 4: Emotional Wellness Assessment",
            description: "Check in on your emotional health and well-being."
        },
        {
            title: "Week 5: Walking Routine",
            description: "Start short daily walks to improve circulation and mood."
        },
        {
            title: "Week 6: Sleep & Rest Strategy",
            description: "Implement a rest strategy to manage fatigue and sleep deprivation."
        }
    ];

    const container = document.getElementById('milestones');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');

    function render() {
        container.innerHTML = '';
        let completedCount = 0;

        milestones.forEach((milestone, index) => {
            const card = document.createElement('div');
            // Using a unique key prefix per user would be better, but sticking to legacy key for now
            // or adding 'psm_' prefix to be cleaner. Let's use simple key.
            const isCompleted = localStorage.getItem(`milestone_${index}`) === 'completed';
            
            if (isCompleted) completedCount++;

            card.className = `milestone-card ${isCompleted ? 'completed' : ''}`;
            card.onclick = () => toggleStatus(card, index);

            card.innerHTML = `
                <div class="milestone-content">
                    <h3>${milestone.title}</h3>
                    <p>${milestone.description}</p>
                </div>
                <div class="status-check">
                    ${isCompleted ? '<i class="fas fa-check" style="font-size: 0.8rem;"></i>' : ''}
                </div>
            `;

            container.appendChild(card);
        });
        
        // Update progress
        const percent = Math.round((completedCount / milestones.length) * 100);
        progressBar.style.width = `${percent}%`;
        progressText.innerText = `${percent}%`;
    }

    function toggleStatus(card, index) {
        const isComplete = card.classList.contains('completed');
        
        if (!isComplete) {
            localStorage.setItem(`milestone_${index}`, 'completed');
        } else {
            localStorage.removeItem(`milestone_${index}`);
        }
        render(); // Re-render to update UI and Progress
    }
    
    // Initial Render
    render();
</script>

<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


