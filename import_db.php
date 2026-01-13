<?php
/**
 * PSM System - Database Importer (V4 - Robust Accounts)
 */
require_once __DIR__ . '/backend/config/database.php';

echo "<h2>PSM System - Database Importer</h2>";

$sqlFile = __DIR__ . '/database/psm_system.sql';

if (!file_exists($sqlFile)) {
    die("❌ Error: SQL file not found at $sqlFile");
}

$sql = file_get_contents($sqlFile);

// Cleanup SQL: Buang 'CREATE DATABASE' and 'USE'
$sql = preg_replace('/CREATE DATABASE IF NOT EXISTS.*;/i', '', $sql);
$sql = preg_replace('/USE .*;/i', '', $sql);

echo "<p>Starting migration into database: <strong>" . getenv('MYSQLDATABASE') . "</strong>...</p>";

// Execute multiple queries
if (mysqli_multi_query($conn, $sql)) {
    do {
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    
    echo "<p style='color: green;'>✅ Tables created/checked.</p>";

    // Logic baru: Check & Insert satu per satu kalau takde
    $testUsers = [
        ['name' => 'Dr. Hanan', 'email' => 'hanan@gmail.com', 'pass' => 'password123', 'role' => 'professional'],
        ['name' => 'Dr. Izzah', 'email' => 'izzah@gmail.com', 'pass' => 'password123', 'role' => 'professional'],
        ['name' => 'Dr. Fara', 'email' => 'fara@gmail.com', 'pass' => 'password123', 'role' => 'professional'],
        ['name' => 'Test Mother', 'email' => 'mother@gmail.com', 'pass' => 'password123', 'role' => 'mother'],
        ['name' => 'Test Admin', 'email' => 'admin@gmail.com', 'pass' => 'password123', 'role' => 'admin']
    ];

    foreach ($testUsers as $user) {
        $email = $user['email'];
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) == 0) {
            $name = $user['name'];
            $pass = $user['pass'];
            $role = $user['role'];
            mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$pass', '$role')");
            echo "<li>✅ Created account: <strong>$email</strong> ($name)</li>";
        } else {
            echo "<li>ℹ️ Account already exists: <strong>$email</strong></li>";
        }
    }

    echo "<p style='color: blue; margin-top: 20px;'><strong>All accounts are ready!</strong></p>";
    echo "<p><a href='frontend/views/auth/login.php'>Go to Login Page</a></p>";

} else {
    echo "<p style='color: red;'>❌ Migration Failed: " . mysqli_error($conn) . "</p>";
}
?>
