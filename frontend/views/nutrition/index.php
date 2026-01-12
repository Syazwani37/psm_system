<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Nutrition & Exercise Plans
 * Frontend View: Nutrition Module
 */

$page_title = "Nutrition & Exercise Plans - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';
requireLogin('mother');
require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1rem;
        text-align: center;
    }

    .plan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .zen-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.02);
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        text-align: left;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .zen-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(107, 142, 130, 0.15);
    }

    .card-icon {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
    }

    .icon-nutrition { background: #FFF0E0; color: #E6A56C; }
    .icon-exercise { background: #E8F0EB; color: #6B8E82; }
    .icon-mind { background: #E3F2FD; color: #64B5F6; }

    .zen-card h3 {
        font-size: 1.4rem;
        color: #2C2C2C;
        margin-bottom: 1rem;
        font-family: 'Playfair Display', serif;
    }

    ul {
        list-style: none;
        padding: 0;
        margin-bottom: 2rem;
        flex: 1; /* Pushes button to bottom */
    }

    li {
        margin-bottom: 0.75rem;
        padding-left: 1.5rem;
        position: relative;
        color: #5C5C5C;
        font-size: 0.95rem;
    }

    li::before {
        content: "•";
        color: #B4C5BD;
        font-weight: bold;
        position: absolute;
        left: 0;
        font-size: 1.2rem;
        line-height: 1;
    }

    li strong {
        color: #4A5D53;
        font-weight: 600;
    }

    .btn-fav {
        width: 100%;
        padding: 0.75rem;
        border-radius: 12px;
        border: 1px solid #EFEBE0;
        background: transparent;
        color: #949494;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-fav:hover {
        background: #FAFAFA;
        color: #6B8E82;
        border-color: #B4C5BD;
    }

    .btn-fav.active {
        background: #FFEBEE;
        color: #E53935;
        border-color: #EF9A9A;
        border: 1px solid #EF9A9A; /* Ensure border is visible */
    }

    .btn-fav.active:hover {
        background: #FFCDD2;
    }

    /* TOAST NOTIFICATION STYLES */
    #toast-container {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
    }

    .toast-custom {
        background: #333;
        color: white;
        padding: 1rem 2rem;
        border-radius: 50px;
        margin-top: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        animation: fadeInUp 0.3s ease-out;
        font-weight: 500;
    }

    .toast-custom.success { background: #43A047; }
    .toast-custom.remove { background: #E53935; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeOutDown {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(20px); }
    }
</style>

<!-- Decoration -->
<div class="blob blob-1" style="top: -150px; left: -150px; opacity: 0.4;"></div>

<div class="page-container">
    <div class="d-flex align-items-center mb-5">
        <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="mb-0" style="font-family: 'Playfair Display', serif; color: #4A5D53;">
                <i class="fas fa-apple-alt me-2" style="color: #B4C5BD;"></i> Nutrition & Exercise Plans
            </h1>
            <p class="text-muted mb-0">Nourish your body and regain strength.</p>
        </div>
    </div>

    <div class="plan-grid">
        <!-- Plan 1 -->
        <div class="zen-card">
            <div class="card-icon icon-nutrition"><i class="fas fa-utensils"></i></div>
            <h3>Week 1 Nutrition</h3>
            <ul>
                <li><strong>Breakfast:</strong> Scrambled eggs with spinach & avocado</li>
                <li><strong>Lunch:</strong> Grilled chicken salad with mixed greens</li>
                <li><strong>Dinner:</strong> Baked salmon with quinoa & steamed veggies</li>
            </ul>
            <button class="btn-fav" data-id="nutr-w1" onclick="toggleFavorite(this)">
                <i class="far fa-heart"></i> Add to Favorites
            </button>
        </div>

        <!-- Plan 2 -->
        <div class="zen-card">
            <div class="card-icon icon-exercise"><i class="fas fa-running"></i></div>
            <h3>Week 1 Gentle Movement</h3>
            <ul>
                <li><strong>Kegels:</strong> Strengthen pelvic floor (3x daily)</li>
                <li><strong>Walking:</strong> 10-15 min gentle walk</li>
                <li><strong>Stretching:</strong> Neck & shoulder relief</li>
            </ul>
            <button class="btn-fav" data-id="exer-w1" onclick="toggleFavorite(this)">
                <i class="far fa-heart"></i> Add to Favorites
            </button>
        </div>

        <!-- Plan 3 -->
        <div class="zen-card">
            <div class="card-icon icon-nutrition"><i class="fas fa-carrot"></i></div>
            <h3>Confinement Healing Diet</h3>
            <ul>
                <li><strong>Soup:</strong> Red date & ginger tea (warming)</li>
                <li><strong>Protein:</strong> Braised chicken with sesame oil</li>
                <li><strong>Veg:</strong> Stir-fried broccoli with garlic</li>
            </ul>
            <button class="btn-fav" data-id="nutr-conf" onclick="toggleFavorite(this)">
                <i class="far fa-heart"></i> Add to Favorites
            </button>
        </div>

        <!-- Plan 4 -->
        <div class="zen-card">
            <div class="card-icon icon-exercise"><i class="fas fa-spa"></i></div>
            <h3>Post-C-Section Care</h3>
            <ul>
                <li><strong>Mobility:</strong> Get out of bed via side-roll</li>
                <li><strong>Avoid:</strong> Heavy lifting (> baby's weight)</li>
                <li><strong>Wound Care:</strong> Keep incision dry and clean</li>
            </ul>
            <button class="btn-fav" data-id="exer-csec" onclick="toggleFavorite(this)">
                <i class="far fa-heart"></i> Add to Favorites
            </button>
        </div>

        <!-- Plan 5 -->
        <div class="zen-card">
            <div class="card-icon icon-mind"><i class="fas fa-brain"></i></div>
            <h3>Mental Wellness</h3>
            <ul>
                <li><strong>Meditation:</strong> 5-min breathing exercise</li>
                <li><strong>Journaling:</strong> Write 3 things you're grateful for</li>
                <li><strong>Rest:</strong> Nap when baby naps (prioritize sleep)</li>
            </ul>
            <button class="btn-fav" data-id="mind-w1" onclick="toggleFavorite(this)">
                <i class="far fa-heart"></i> Add to Favorites
            </button>
        </div>

        <!-- Plan 6 -->
        <div class="zen-card">
            <div class="card-icon icon-exercise"><i class="fas fa-child"></i></div>
            <h3>Mom & Baby Bonding</h3>
            <ul>
                <li><strong>Skin-to-Skin:</strong> 20 mins daily for oxytocin</li>
                <li><strong>Massage:</strong> Gentle baby massage before bath</li>
                <li><strong>Talk:</strong> Narrate your day to baby</li>
            </ul>
            <button class="btn-fav" data-id="exer-bond" onclick="toggleFavorite(this)">
                <i class="far fa-heart"></i> Add to Favorites
            </button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<script>
    // Load favorites on page load
    document.addEventListener('DOMContentLoaded', () => {
        const favorites = JSON.parse(localStorage.getItem('userFavorites')) || [];
        document.querySelectorAll('.btn-fav').forEach(btn => {
            const id = btn.getAttribute('data-id');
            if (favorites.includes(id)) {
                setButtonState(btn, true);
            }
        });
    });

    function toggleFavorite(btn) {
        const id = btn.getAttribute('data-id');
        let favorites = JSON.parse(localStorage.getItem('userFavorites')) || [];
        const isFav = favorites.includes(id);

        if (isFav) {
            // Unlike
            favorites = favorites.filter(favId => favId !== id);
            localStorage.setItem('userFavorites', JSON.stringify(favorites));
            setButtonState(btn, false);
            showNotification("Removed from favorites", "remove");
        } else {
            // Like
            favorites.push(id);
            localStorage.setItem('userFavorites', JSON.stringify(favorites));
            setButtonState(btn, true);
            showNotification("Added to your favorites!", "success");
        }
    }

    function setButtonState(btn, active) {
        if (active) {
            btn.classList.add('active');
            btn.innerHTML = '<i class="fas fa-heart"></i> Added';
        } else {
            btn.classList.remove('active');
            btn.innerHTML = '<i class="far fa-heart"></i> Add to Favorites';
        }
    }

    function showNotification(message, type) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast-custom ${type}`;

        let icon = type === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-trash-alt"></i>';

        toast.innerHTML = `${icon} <span>${message}</span>`;
        container.appendChild(toast);

        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'fadeOutDown 0.3s ease-in forwards';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
</script>

<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


