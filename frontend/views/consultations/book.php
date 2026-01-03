<?php
/**
 * PSM System - Book Consultation
 * Frontend View: Consultations Module
 */

$page_title = "Book Consultation - PSM System";
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/includes/auth_check.php';
requireLogin('mother');
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
        text-align: center;
    }

    .doctor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .doctor-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.02);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .doctor-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(107, 142, 130, 0.15);
    }

    .doctor-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #E8F0EB;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #6B8E82;
    }

    .doctor-name {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2C2C2C;
        margin-bottom: 0.25rem;
    }

    .doctor-role {
        font-size: 0.9rem;
        color: #949494;
        margin-bottom: 1.5rem;
    }

    .book-btn {
        background: #8DA399;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 500;
        cursor: pointer;
        width: 100%;
        transition: 0.2s;
    }

    .book-btn:hover {
        background: #6B8E82;
        box-shadow: 0 5px 15px rgba(107, 142, 130, 0.3);
        transform: translateY(-2px);
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        visibility: hidden;
        opacity: 0;
        transition: all 0.3s ease;
        backdrop-filter: blur(4px);
    }

    .modal-overlay.active {
        visibility: visible;
        opacity: 1;
    }

    .modal-card {
        background: white;
        padding: 2.5rem;
        border-radius: 24px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        transform: translateY(20px);
        transition: transform 0.3s ease;
        text-align: left;
    }

    .modal-overlay.active .modal-card {
        transform: translateY(0);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #4A5D53;
        font-family: 'Playfair Display', serif;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #999;
        cursor: pointer;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #555;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem;
        border: 1px solid #DDD;
        border-radius: 12px;
        background: #FAFAFA;
        font-family: inherit;
    }

    /* Toast Notification */
    #toast-container {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2000;
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
<div class="blob blob-2" style="top: -100px; right: -100px; opacity: 0.4;"></div>

<div class="page-container">
    <div class="text-center mb-5">
        <a href="<?php echo BASE_URL; ?>/frontend/views/dashboard/mother.php" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
        <h1 class="mb-2" style="font-family: 'Playfair Display', serif; color: #4A5D53;">
            <i class="fas fa-user-md me-2" style="color: #B4C5BD;"></i> Expert Consultations
        </h1>
        <p class="text-muted">Connect with caring professionals for your journey.</p>
    </div>

    <div class="doctor-grid">
        <!-- Doctor 1 -->
        <div class="doctor-card">
            <div class="doctor-avatar">👩‍⚕️</div>
            <div class="doctor-name">Dr. Hanan</div>
            <div class="doctor-role">OB-GYN Specialist</div>
            <button class="book-btn" onclick="openBooking('Dr. Hanan')">Book Session</button>
        </div>

        <!-- Doctor 2 -->
        <div class="doctor-card">
            <div class="doctor-avatar">👩‍⚕️</div>
            <div class="doctor-name">Dr. Izzah</div>
            <div class="doctor-role">Physiotherapist</div>
            <button class="book-btn" onclick="openBooking('Dr. Izzah')">Book Session</button>
        </div>

        <!-- Doctor 3 -->
        <div class="doctor-card">
            <div class="doctor-avatar">👩‍💼</div>
            <div class="doctor-name">Dr. Fara</div>
            <div class="doctor-role">Mental Health Counselor</div>
            <button class="book-btn" onclick="openBooking('Dr. Fara')">Book Session</button>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal-overlay" id="bookingModal">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-title">Book Consultation</div>
            <button class="close-btn" onclick="closeBooking()">&times;</button>
        </div>

        <form onsubmit="handleBookingSubmit(event)">
            <input type="hidden" id="doctorNameInput">

            <p style="margin-bottom: 1.5rem; color: #666;">Booking with: <strong id="doctorNameDisplay"></strong></p>

            <div class="form-group">
                <label class="form-label">Preferred Date</label>
                <input type="date" id="dateInput" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Preferred Time</label>
                <input type="time" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Reason for Consultation</label>
                <input type="text" class="form-control" placeholder="e.g. Checkup, Pain relief..." required>
            </div>

            <button type="submit" class="book-btn" style="margin-top: 1rem;">Confirm Booking</button>
        </form>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<script>
    function openBooking(doctorName) {
        document.getElementById('doctorNameInput').value = doctorName;
        document.getElementById('doctorNameDisplay').textContent = doctorName;
        // Set min date to today
        document.getElementById('dateInput').min = new Date().toISOString().split('T')[0];
        document.getElementById('bookingModal').classList.add('active');
    }

    function closeBooking() {
        document.getElementById('bookingModal').classList.remove('active');
    }

    function handleBookingSubmit(event) {
        event.preventDefault();
        const doctor = document.getElementById('doctorNameInput').value;
        const date = document.getElementById('dateInput').value;

        // Store in localStorage for Professional Dashboard to pick up
        // In a real app, this would be an AJAX POST to backend/api/consultations/create.php
        const newBooking = {
            doctor: doctor,
            date: date, // Simplified
            patientName: "<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Mother'; ?>", // Use PHP session name if available
            status: 'pending'
        };

        let bookings = JSON.parse(localStorage.getItem('expertBookings')) || [];
        bookings.push(newBooking);
        localStorage.setItem('expertBookings', JSON.stringify(bookings));

        // Close Modal
        closeBooking();

        // Show Notification
        showNotification(`✅ Booking request sent to ${doctor}!`);
    }

    function showNotification(message) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = 'toast-custom success';

        toast.innerHTML = `<i class="fas fa-check-circle"></i> <span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'fadeOutDown 0.3s ease-in forwards';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
</script>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/includes/footer.php'; ?>

