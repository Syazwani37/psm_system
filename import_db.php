<?php
/**
 * PSM System - Database Importer (V2)
 */
require_once __DIR__ . '/backend/config/database.php';

echo "<h2>PSM System - Database Importer</h2>";

$sqlFile = __DIR__ . '/database/psm_system.sql';

if (!file_exists($sqlFile)) {
    die("❌ Error: SQL file not found at $sqlFile");
}

$sql = file_get_contents($sqlFile);

// Cleanup SQL: Buang 'CREATE DATABASE' and 'USE' supaya dia masuk dalam database 'railway'
$sql = preg_replace('/CREATE DATABASE IF NOT EXISTS.*;/i', '', $sql);
$sql = preg_replace('/USE .*;/i', '', $sql);

echo "<p>Starting migration into database: <strong>" . getenv('MYSQLDATABASE') . "</strong>...</p>";

// Execute multiple queries
if (mysqli_multi_query($conn, $sql)) {
    $count = 0;
    do {
        // Clear results
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
        $count++;
    } while (mysqli_next_result($conn));
    
    echo "<p style='color: green;'>✅ Migration Result: Successfully executed queries.</p>";

    // Tambah test users kalau takde
    echo "<p>Checking for test users...</p>";
    $checkUser = mysqli_query($conn, "SELECT id FROM users LIMIT 1");
    if (mysqli_num_rows($checkUser) == 0) {
        $insertUsers = "
            INSERT INTO users (name, email, password, role) VALUES 
            ('Test Mother', 'mother@example.com', 'password123', 'mother'),
            ('Test Professional', 'pro@example.com', 'password123', 'professional'),
            ('Test Admin', 'admin@example.com', 'password123', 'admin');
        ";
        if (mysqli_query($conn, $insertUsers)) {
            echo "<p style='color: blue;'>✅ Test users created: mother@example.com, pro@example.com (password123)</p>";
        }
    }

    echo "<p><a href='frontend/views/auth/login.php'>Go to Login Page</a></p>";

} else {
    echo "<p style='color: red;'>❌ Migration Failed: " . mysqli_error($conn) . "</p>";
}
?>
