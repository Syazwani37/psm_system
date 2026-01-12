<?php
/**
 * PSM System - Emergency Database Importer
 * Run this ONCE on Railway to setup tables
 */
require_once 'backend/config/database.php';

echo "<h2>PSM System - Database Importer</h2>";

$sql_file = 'database/psm_system.sql';

if (!file_exists($sql_file)) {
    die("❌ SQL file not found at $sql_file");
}

$sql_content = file_get_contents($sql_file);

// Split SQL into individual queries
$queries = array_filter(array_map('trim', explode(';', $sql_content)));

$success_count = 0;
$error_count = 0;

foreach ($queries as $query) {
    if (empty($query)) continue;
    
    if (mysqli_query($conn, $query)) {
        $success_count++;
    } else {
        echo "❌ Error: " . mysqli_error($conn) . "<br>";
        $error_count++;
    }
}

echo "<h3>Migration Result:</h3>";
echo "✅ Successfully executed $success_count queries.<br>";
if ($error_count > 0) {
    echo "⚠️ Failed $error_count queries.<br>";
}

echo "<br><a href='index.php'>Go to Login Page</a>";

// Auto-delete this file for security after success? 
// Better keep it for now until user confirms.
?>
