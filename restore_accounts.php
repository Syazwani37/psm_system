<?php
// restore_accounts.php
require_once __DIR__ . '/backend/config/database.php';

function restoreUser($conn, $name, $email, $password, $role) {
    // 1. Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $res = $check->get_result();
    
    // Hash the password (assuming you used password_hash in register.php)
    // If you use plain text, remove password_hash() wrapper.
    // Using plain text to match current login.php implementation
    $hashed_pass = $password;

    if ($res->num_rows > 0) {
        // Update existing
        $update = $conn->prepare("UPDATE users SET password = ?, name = ?, role = ? WHERE email = ?");
        $update->bind_param("ssss", $hashed_pass, $name, $role, $email);
        if ($update->execute()) {
            echo "<div style='color:green; margin: 10px 0;'>✅ Updated User: <strong>$email</strong> (Password: $password)</div>";
        } else {
            echo "<div style='color:red;'>❌ Error updating $email: " . $conn->error . "</div>";
        }
    } else {
        // specific ID assignment is tricky with AUTO_INCREMENT, so we just let it generate.
        $insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $name, $email, $hashed_pass, $role);
        if ($insert->execute()) {
            echo "<div style='color:blue; margin: 10px 0;'>✨ Created New User: <strong>$email</strong> (Password: $password)</div>";
        } else {
            echo "<div style='color:red;'>❌ Error creating $email: " . $conn->error . "</div>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restore Default Accounts</title>
    <style>body { font-family: sans-serif; padding: 2rem; line-height: 1.5; }</style>
</head>
<body>
    <h1>Restoring Default Accounts...</h1>

    <?php
    // 1. Restore Mother
    restoreUser($conn, 'Mother Demo', 'mother@gmail.com', '12345', 'mother');

    // 2. Restore Professional
    restoreUser($conn, 'Dr. Expert', 'professional@gmail.com', 'abc123', 'professional');

    // 3. Restore Admin
    restoreUser($conn, 'System Admin', 'admin@gmail.com', 'adminpass', 'admin');
    ?>

    <h2>All Done!</h2>
    <p>You can now log in with the following credentials:</p>
    <ul>
        <li><strong>Mother:</strong> mother@gmail.com / 12345</li>
        <li><strong>Professional:</strong> professional@gmail.com / abc123</li>
        <li><strong>Admin:</strong> admin@gmail.com / adminpass</li>
    </ul>

    <a href="auth/login.php" style="background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Go to Login</a>
</body>
</html>
