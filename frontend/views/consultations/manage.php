<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Manage Consultations
 * Frontend View: Consultations Module (Professional)
 */

$page_title = "Manage Consultations - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';
requireLogin('professional');
require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .booking-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.5rem;
        transition: all 0.3s ease;
        border-left: 5px solid #DDD;
    }

    .booking-card.pending { border-left-color: #FFA726; }
    .booking-card.accepted { border-left-color: #66BB6A; }
    .booking-card.rescheduled { border-left-color: #42A5F5; }

    .booking-info { flex: 1; }

    .booking-date {
        font-weight: 700;
        color: #333;
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
    }

    .booking-details {
        color: #666;
        font-size: 0.95rem;
    }

    .booking-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-accept { background: #E8F5E9; color: #2E7D32; }
    .btn-accept:hover { background: #C8E6C9; }

    .btn-reschedule { background: #E3F2FD; color: #1565C0; }
    .btn-reschedule:hover { background: #BBDEFB; }

    .empty-state {
        text-align: center;
        padding: 4rem;
        color: #999;
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
        max-width: 450px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #555; }
    .form-control { width: 100%; padding: 0.8rem; border: 1px solid #DDD; border-radius: 12px; }
</style>

<!-- Decoration -->
<div class="blob blob-1" style="top: -100px; right: -100px; opacity: 0.3; background: #B2DFDB;"></div>

<div class="page-container">
    <div class="d-flex align-items-center mb-5">
        <a href="<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="mb-0" style="font-family: 'Playfair Display', serif; color: #006064;">
                <i class="fas fa-calendar-check me-2" style="color: #4DB6AC;"></i> Consultation Requests
            </h1>
            <p class="text-muted mb-0">Manage incoming booking requests from mothers.</p>
        </div>
    </div>

    <div id="bookingsList">
        <!-- Bookings will be injected here -->
        <div class="text-center py-5">
            <div class="spinner-border text-secondary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div class="modal-overlay" id="rescheduleModal">
        <div class="modal-card">
            <h3 style="margin-bottom: 1.5rem; color: #1565C0;">Reschedule Session</h3>
            <input type="hidden" id="rescheduleIndex">

            <div class="form-group">
                <label class="form-label">New Date</label>
                <input type="date" id="newDate" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">New Time</label>
                <input type="time" id="newTime" class="form-control">
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button onclick="closeReschedule()" class="btn btn-light border" style="flex: 1;">Cancel</button>
                <button onclick="confirmReschedule()" class="btn btn-primary"
                    style="flex: 1; background: #1565C0;">Confirm Change</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', loadBookings);

    function loadBookings() {
        const bookings = JSON.parse(localStorage.getItem('expertBookings')) || [];
        const container = document.getElementById('bookingsList');

        container.innerHTML = '';

        if (bookings.length === 0) {
            container.innerHTML = `
            <div class="empty-state">
                <i class="far fa-calendar-times" style="font-size: 4rem; opacity: 0.3; margin-bottom: 1rem;"></i>
                <p>No booking requests found.</p>
            </div>
        `;
            return;
        }

        bookings.forEach((booking, index) => {
            let statusClass = 'pending';
            if (booking.status === 'accepted') statusClass = 'accepted';
            if (booking.status === 'rescheduled') statusClass = 'rescheduled';

            // Format Date (simple)
            const dateObj = new Date(booking.date);
            const dateStr = isNaN(dateObj) ? booking.date : dateObj.toLocaleDateString();

            const patientName = booking.patientName || "Patient (Mother)";

            let actionsHtml = '';
            if (statusClass === 'pending' || booking.status === 'notified') {
                actionsHtml = `
                <button class="btn-action btn-accept" onclick="acceptBooking(${index})">
                    <i class="fas fa-check"></i> Accept
                </button>
                <button class="btn-action btn-reschedule" onclick="openReschedule(${index})">
                    <i class="fas fa-clock"></i> Reschedule
                </button>
            `;
            } else if (statusClass === 'accepted') {
                actionsHtml = `<span style="color: #2E7D32; font-weight: 600;"><i class="fas fa-check-circle me-1"></i> Confirmed</span>`;
            } else if (statusClass === 'rescheduled') {
                actionsHtml = `<span style="color: #1565C0; font-weight: 600;"><i class="fas fa-history me-1"></i> Rescheduled to ${booking.newDate} ${booking.newTime}</span>`;
            }

            const html = `
            <div class="booking-card ${statusClass}">
                <div class="booking-info">
                    <div class="booking-date">${booking.doctor} - Re: ${patientName}</div>
                    <div class="booking-details">Reason: Checkup/Consultation</div>
                    <div class="booking-details" style="margin-top: 0.25rem; font-size: 0.85rem; opacity: 0.8;">Requested: ${dateStr}</div>
                </div>
                <div class="booking-actions">
                    ${actionsHtml}
                </div>
            </div>
        `;
            container.innerHTML += html;
        });
    }

    function acceptBooking(index) {
        if(!confirm("Are you sure you want to accept this booking?")) return;
        
        let bookings = JSON.parse(localStorage.getItem('expertBookings'));
        bookings[index].status = 'accepted';
        localStorage.setItem('expertBookings', JSON.stringify(bookings));

        // Notify (fake)
        alert("✅ Booking Accepted!");
        loadBookings();
    }

    function openReschedule(index) {
        document.getElementById('rescheduleIndex').value = index;
        document.getElementById('rescheduleModal').classList.add('active');
    }

    function closeReschedule() {
        document.getElementById('rescheduleModal').classList.remove('active');
    }

    function confirmReschedule() {
        const index = document.getElementById('rescheduleIndex').value;
        const date = document.getElementById('newDate').value;
        const time = document.getElementById('newTime').value;

        if (!date || !time) {
            alert("Please select date and time");
            return;
        }

        let bookings = JSON.parse(localStorage.getItem('expertBookings'));
        bookings[index].status = 'rescheduled';
        bookings[index].newDate = date;
        bookings[index].newTime = time;
        localStorage.setItem('expertBookings', JSON.stringify(bookings));

        alert("📅 Booking Rescheduled!");
        closeReschedule();
        loadBookings();
    }
</script>

<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


