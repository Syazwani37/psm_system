<?php
/**
 * PSM System - Database Importer (Temporary)
 */
require_once __DIR__ . '/backend/config/database.php';

echo "<h2>PSM System - Database Importer</h2>";

$sqlFile = __DIR__ . '/database/psm_system.sql';

if (!file_exists($sqlFile)) {
    die("❌ Error: SQL file not found at $sqlFile");
}

$sql = file_get_content($sqlFile);

// Execute multiple queries
if (mysqli_multi_query($conn, $sql)) {
    $count = 0;
    do {
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
        $count++;
    } while (mysqli_next_result($conn));
    
    echo "<p style='color: green;'>✅ Migration Result: Successfully executed queries.</p>";
    echo "<a href='index.php'>Go to Login Page</a>";
} else {
    echo "<p style='color: red;'>❌ Migration Failed: " . mysqli_error($conn) . "</p>";
}
?>
