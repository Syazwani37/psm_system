<?php
/**
 * PSM System - Database Migration Support
 * Ensures 'consultations' table exists with correct schema.
 */

require_once 'backend/config/database.php';

echo "<h2>Consultation Table Migration</h2>";

try {
    // 1. Check if table exists
    $checkTable = mysqli_query($conn, "SHOW TABLES LIKE 'consultations'");
    $tableExists = mysqli_num_rows($checkTable) > 0;

    if ($tableExists) {
        // Check for 'reason' column (added recently)
        $checkReason = mysqli_query($conn, "SHOW COLUMNS FROM consultations LIKE 'reason'");
        if (mysqli_num_rows($checkReason) == 0) {
            echo "Adding 'reason' column...<br>";
            mysqli_query($conn, "ALTER TABLE consultations ADD COLUMN reason VARCHAR(255) AFTER scheduled_at");
        }

        // Check for 'rescheduled' status in ENUM
        $checkStatus = mysqli_query($conn, "SHOW COLUMNS FROM consultations LIKE 'status'");
        $row = mysqli_fetch_assoc($checkStatus);
        if (strpos($row['Type'], 'rescheduled') === false) {
            echo "Updating 'status' ENUM to include 'rescheduled'...<br>";
            mysqli_query($conn, "ALTER TABLE consultations MODIFY COLUMN status ENUM('pending','accepted','rejected','completed','rescheduled') DEFAULT 'pending'");
        }
        
        echo "✅ Table 'consultations' is already up to date.<br>";
    } else {
        echo "Creating 'consultations' table...<br>";
        $sql_create = "CREATE TABLE consultations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            professional_id INT,
            scheduled_at DATETIME,
            reason VARCHAR(255),
            status ENUM('pending','accepted','rejected','completed','rescheduled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        mysqli_query($conn, $sql_create);
        echo "✅ Table 'consultations' created successfully.<br>";
    }

} catch (Exception $e) {
    echo "❌ Error during migration: " . $e->getMessage();
}

echo "<br><a href='index.php'>Back to Home</a>";
?>
