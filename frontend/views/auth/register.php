<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Register Page
 * Frontend View: Auth Module
 */

$page_title = "Register - PSM System";

require_once BASE_PATH . '/backend/helpers/functions.php';
require_once BASE_PATH . '/backend/includes/header.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Registration Validation & Restrictions
    if (strpos($email, '@') === false) {
        $error = "Invalid email formatting! Missing '@' symbol.";
    } else if ($role === 'professional') {
        $allowed = [
            'hanan@gmail.com', 'izzah@gmail.com', 'fara@gmail.com'
        ];
        if (!in_array(strtolower($email), $allowed)) {
            $error = "Unauthorized email! Only Dr. Hanan, Dr. Izzah, and Dr. Fara can register as Professionals.";
        }
    } else {
        // Enforce @gmail.com for Mothers and Admins
        if (!preg_match('/@gmail\.com$/i', $email)) {
            $error = "Invalid email domain! Please use a correctly spelled '@gmail.com' address.";
        }
    }

    if (!$error) {
        // Check if email exists
        $check_query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) > 0) {
            $error = "Email already registered!";
        } else {
            $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";

            if (mysqli_query($conn, $sql)) {
                $success = "Registration successful! Redirecting to login...";
                echo "<script>setTimeout(() => { window.location.href = 'login.php'; }, 2000);</script>";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<style>
    body {
        background: #FAF6F1;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
    }

    .register-wrapper {
        width: 100%;
        max-width: 420px;
        padding: 1rem;
    }

    .register-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }

    .logo-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #8DA399, #6B8E82);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
        margin: 0 auto 1rem;
        box-shadow: 0 8px 16px rgba(141, 163, 153, 0.3);
    }

    .btn-register {
        background: linear-gradient(135deg, #8DA399, #6B8E82);
        border: none;
        padding: 1rem;
        border-radius: 12px;
        font-weight: 600;
        width: 100%;
        margin-top: 1rem;
        transition: all 0.3s ease;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(141, 163, 153, 0.4);
    }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <div class="text-center mb-4">
            <div class="logo-icon">
                <i class="fas fa-child"></i>
            </div>
            <h2 style="font-family: 'Playfair Display', serif;">DSS Recovery</h2>
            <p class="text-muted mb-0">Start your journey with us</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-medium">Full Name</label>
                <input type="text" name="name" class="form-control form-control-lg" 
                       placeholder="Enter full name" required
                       style="border-radius: 12px; border: 1px solid #EEE;">
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Email Address</label>
                <input type="email" name="email" class="form-control form-control-lg" 
                       placeholder="Enter email" required
                       style="border-radius: 12px; border: 1px solid #EEE;">
                <small class="text-muted" style="font-size: 0.8rem;">Required: <strong>@gmail.com</strong> (for Mothers/Admins)</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Password</label>
                <input type="password" name="password" class="form-control form-control-lg" 
                       placeholder="Create password" required
                       style="border-radius: 12px; border: 1px solid #EEE;">
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">I am a...</label>
                <select name="role" class="form-select form-select-lg" required
                        style="border-radius: 12px; border: 1px solid #EEE;">
                    <option value="" disabled selected>Select Role</option>
                    <option value="mother">🤱 Mother</option>
                    <option value="professional">👩‍⚕️ Doctor / Professional</option>
                    <option value="admin">🛡️ Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-register">
                Register Now <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <p class="text-center mt-3 mb-0">
            Already have an account? 
            <a href="login.php" class="text-decoration-none fw-bold" style="color: #8DA399;">
                Login here
            </a>
        </p>
    </div>
</div>

<?php require_once BASE_PATH . '/backend/includes/footer.php'; ?>
