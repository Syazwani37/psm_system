<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Book Consultation
 * Frontend View: Consultations Module
 */

$page_title = "Book Consultation - PSM System";
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
        background: #333 !important;
        color: white !important;
        padding: 1.5rem 2rem !important; /* Larger padding */
        border-radius: 12px !important;
        margin-top: 10px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5) !important;
        display: flex;
        align-items: center;
        gap: 1rem;
        animation: fadeInUp 0.3s ease-out;
        font-weight: 600;
        border: 3px solid rgba(255,255,255,0.8) !important; /* Thicker White Border */
        z-index: 9999 !important;
        min-width: 300px;
        font-size: 1.1rem;
    }
    .toast-custom.success { background: #43A047 !important; border: 3px solid #1B5E20 !important; }
    .toast-custom.error { background: #E53935 !important; border: 3px solid #B71C1C !important; }
    
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
    <div class="d-flex align-items-center mb-5">
        <a href="<?php echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="mb-0" style="font-family: 'Playfair Display', serif; color: #4A5D53;">
                <i class="fas fa-user-md me-2" style="color: #B4C5BD;"></i> Expert Consultations
            </h1>
            <p class="text-muted mb-0">Book a session with our healthcare specialists.</p>
        </div>
    </div>

    <div class="doctor-grid">
        <?php
        // Only show our 3 official specialists (ensuring no duplicates)
        $prof_res = mysqli_query($conn, "SELECT DISTINCT id, name FROM users
            WHERE role='professional'
            AND name IN ('Dr. Hanan', 'Dr. Izzah', 'Dr. Fara')
            ORDER BY FIELD(name, 'Dr. Hanan', 'Dr. Izzah', 'Dr. Fara')");
        
        // Define icons/roles mapping
        $expert_meta = [
            'Dr. Hanan' => ['role' => 'OB-GYN Specialist', 'avatar' => '👩‍⚕️'],
            'Dr. Izzah' => ['role' => 'Physiotherapist', 'avatar' => '👩‍⚕️'],
            'Dr. Fara' => ['role' => 'Mental Health Counselor', 'avatar' => '👩‍💼']
        ];

        if (mysqli_num_rows($prof_res) > 0):
            while ($prof = mysqli_fetch_assoc($prof_res)):
                $meta = $expert_meta[$prof['name']] ?? ['role' => 'Healthcare Specialist', 'avatar' => '👩‍⚕️'];
        ?>
        <div class="doctor-card" data-id="<?php echo $prof['id']; ?>">
            <div class="doctor-avatar"><?php echo $meta['avatar']; ?></div>
            <div class="doctor-name"><?php echo htmlspecialchars($prof['name']); ?></div>
            <div class="doctor-role"><?php echo $meta['role']; ?></div>
            <button class="book-btn" onclick="openBooking(<?php echo $prof['id']; ?>, '<?php echo addslashes($prof['name']); ?>')">Book Session</button>
        </div>
        <?php 
            endwhile; 
        else:
        ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">No specialists available for booking at the moment.</p>
            </div>
        <?php endif; ?>
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
            <input type="hidden" id="doctorIdInput">
            <input type="hidden" id="doctorNameInput">

            <p style="margin-bottom: 1.5rem; color: #666;">Booking with: <strong id="doctorNameDisplay"></strong></p>

            <div class="form-group">
                <label class="form-label">Preferred Date</label>
                <input type="date" id="dateInput" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Preferred Time</label>
                <input type="time" id="timeInput" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Reason for Consultation</label>
                <input type="text" id="reasonInput" class="form-control" placeholder="e.g. Checkup, Pain relief..." required>
            </div>

            <button type="submit" class="book-btn" style="margin-top: 1rem;">Confirm Booking</button>
        </form>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<script>
    function openBooking(doctorId, doctorName) {
        document.getElementById('doctorIdInput').value = doctorId;
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
        const doctorId = document.getElementById('doctorIdInput').value;
        const doctorName = document.getElementById('doctorNameInput').value;
        const date = document.getElementById('dateInput').value;
        const time = document.getElementById('timeInput').value;
        const reason = document.getElementById('reasonInput').value;

        const bookingData = {
            professional_id: doctorId,
            scheduled_at: `${date} ${time}:00`,
            reason: reason
        };

        fetch('<?php echo BASE_URL; ?>/backend/api/consultations/book.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(bookingData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close Modal
                closeBooking();
                // Show Notification
                showNotification(`✅ Booking request sent to ${doctorName}!`, 'success');
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to send booking request.', 'error');
        });
    }

    function showNotification(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast-custom ${type}`;

        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        toast.innerHTML = `<i class="fas ${icon}"></i> <span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'fadeOutDown 0.3s ease-in forwards';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
</script>

<?php require_once BASE_PATH . '/backend/includes/footer.php'; ?>
