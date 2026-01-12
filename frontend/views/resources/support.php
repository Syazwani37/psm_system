<?php
/**
 * PSM System - Support Resources
 * Frontend View: Resources Module
 */

$page_title = "Support Resources - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';
requireLogin('mother');
require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
        text-align: center;
    }

    .faq-section {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .faq-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.02);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
    }

    .faq-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(107, 142, 130, 0.1);
    }

    .faq-question {
        font-weight: 600;
        font-size: 1.1rem;
        color: #2C2C2C;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .faq-question i { color: #8DA399; }

    .faq-answer {
        font-size: 0.95rem;
        color: #5C5C5C;
        line-height: 1.6;
        margin-left: 2rem;
    }

    .contact-card {
        background: #E8F0EB;
        border-radius: 24px;
        padding: 2.5rem;
        text-align: center;
        color: #2F5D48;
    }

    .contact-card h2 {
        color: #2F5D48;
        margin-bottom: 1.5rem;
        font-family: 'Playfair Display', serif;
    }

    .contact-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        max-width: 500px;
        margin: 0 auto;
    }

    .contact-form input,
    .contact-form textarea {
        width: 100%;
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid #D1DBD6;
        font-size: 1rem;
        background: rgba(255, 255, 255, 0.8);
        font-family: 'Inter', sans-serif;
    }

    .contact-form input:focus,
    .contact-form textarea:focus {
        outline: none;
        border-color: #8DA399;
        background: white;
    }

    .contact-form button {
        padding: 1rem;
        background: #4A5D53;
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 0.5rem;
    }

    .contact-form button:hover {
        background: #2C3E36;
        transform: translateY(-2px);
    }
</style>

<!-- Decoration -->
<div class="blob blob-1" style="top: -150px; left: -150px; opacity: 0.4;"></div>

<div class="page-container">
    <div class="d-flex align-items-center mb-5">
        <a href="<?php echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="mb-0" style="font-family: 'Playfair Display', serif; color: #4A5D53;">
                <i class="fas fa-question-circle me-2" style="color: #B4C5BD;"></i> Support Center
            </h1>
            <p class="text-muted mb-0">We are here to help you.</p>
        </div>
    </div>

    <!-- Frequently Asked Questions -->
    <div class="faq-section">
        <div class="faq-card">
            <div class="faq-question"><i class="fas fa-chart-line"></i> How do I track my recovery progress?</div>
            <p class="faq-answer">Use the "Recovery Tracker" card from your dashboard to update your milestones and view your progress over time.</p>
        </div>

        <div class="faq-card">
            <div class="faq-question"><i class="fas fa-key"></i> Can I change my password?</div>
            <p class="faq-answer">Yes! This feature will be available in your Profile settings soon.</p>
        </div>

        <div class="faq-card">
            <div class="faq-question"><i class="fas fa-users"></i> How do I join the community forum?</div>
            <p class="faq-answer">Click on the "Mom's Circle" feature card from your dashboard to chat with other mothers.</p>
        </div>
    </div>

    <!-- Contact Support Form -->
    <div class="contact-card">
        <h2>Contact Our Team</h2>
        <form class="contact-form" onsubmit="alert('Message sent! We will get back to you shortly.'); return false;">
            <input type="text" placeholder="Your Name" required />
            <input type="email" placeholder="Email Address" required />
            <textarea rows="4" placeholder="Your Message" required></textarea>
            <button type="submit">Send Message</button>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/backend/includes/footer.php'; ?>


