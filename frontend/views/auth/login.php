<?php
require_once dirname(__FILE__, 4) . '/backend/config/database.php';
/**
 * PSM System - Login Page
 * Frontend View: Auth Module
 */

$page_title = "Login - PSM System";

require_once BASE_PATH . '/backend/helpers/functions.php';
require_once BASE_PATH . '/backend/includes/auth_check.php';
require_once BASE_PATH . '/backend/includes/header.php';

$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        redirectToDashboard($user['role']);
    } else {
        $error = "Invalid email or password.";
    }
}

?>

<style>
    body {
        background: url('<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo BASE_URL; ?>/frontend/assets/images/mom_and_baby_background.jpg') no-repeat center center/cover;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        margin: 0;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        z-index: 0;
    }

    .login-wrapper {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 400px;
        padding: 1rem;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .logo-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #F8BBD0, #F48FB1);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
        margin: 0 auto 1rem;
        box-shadow: 0 8px 16px rgba(244, 143, 177, 0.3);
    }

    .btn-login {
        background: linear-gradient(135deg, #F06292, #E91E63);
        border: none;
        padding: 1rem;
        border-radius: 50px;
        font-weight: 600;
        width: 100%;
        margin-top: 1rem;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(233, 30, 99, 0.3);
    }

    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: #999;
        margin: 2rem 0;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #EEE;
    }

    .divider::before {
        margin-right: 0.5em;
    }

    .divider::after {
        margin-left: 0.5em;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-icon">
                <i class="fas fa-heart"></i>
            </div>
            <h2 class="mb-1" style="font-family: 'Playfair Display', serif;">Welcome Back</h2>
            <p class="text-muted mb-0">Please log in to continue</p>
        </div>

        <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-medium">Email Address</label>
                <input type="email" name="email" class="form-control form-control-lg" 
                       placeholder="Enter your email" required 
                       style="border-radius: 12px; border: 1px solid #EEE; background: #FAFAFA;">
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Password</label>
                <input type="password" name="password" class="form-control form-control-lg" 
                       placeholder="Enter password" required
                       style="border-radius: 12px; border: 1px solid #EEE; background: #FAFAFA;">
            </div>

            <button type="submit" class="btn btn-primary btn-login">
                Login <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="divider">or</div>

        <p class="text-center mb-0">
            Don't have an account? 
            <a href="register.php" class="text-decoration-none fw-bold" style="color: #E91E63;">
                Register here
            </a>
        </p>
    </div>
</div>

<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


