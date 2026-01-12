<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/config/database.php';
/**
 * PSM System - Database Migration
 * Add professional action columns to symptom_logs table
 * Run once: http://localhost/psm_system/database/add_symptom_log_actions.php
 */



echo "<h2>PSM System - Adding Symptom Log Action Columns</h2>";

// Add professional_notes column
$check = mysqli_query($conn, "SHOW COLUMNS FROM symptom_logs LIKE 'professional_notes'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "ALTER TABLE symptom_logs ADD COLUMN professional_notes TEXT NULL");
    echo "✅ Added 'professional_notes' column<br>";
} else {
    echo "ℹ️ Column 'professional_notes' already exists<br>";
}

// Add reviewed_by column (foreign key to users)
$check2 = mysqli_query($conn, "SHOW COLUMNS FROM symptom_logs LIKE 'reviewed_by'");
if (mysqli_num_rows($check2) == 0) {
    mysqli_query($conn, "ALTER TABLE symptom_logs ADD COLUMN reviewed_by INT NULL");
    echo "✅ Added 'reviewed_by' column<br>";
} else {
    echo "ℹ️ Column 'reviewed_by' already exists<br>";
}

// Add reviewed_at column
$check3 = mysqli_query($conn, "SHOW COLUMNS FROM symptom_logs LIKE 'reviewed_at'");
if (mysqli_num_rows($check3) == 0) {
    mysqli_query($conn, "ALTER TABLE symptom_logs ADD COLUMN reviewed_at DATETIME NULL");
    echo "✅ Added 'reviewed_at' column<br>";
} else {
    echo "ℹ️ Column 'reviewed_at' already exists<br>";
}

// Add action_status column
$check4 = mysqli_query($conn, "SHOW COLUMNS FROM symptom_logs LIKE 'action_status'");
if (mysqli_num_rows($check4) == 0) {
    mysqli_query($conn, "ALTER TABLE symptom_logs ADD COLUMN action_status ENUM('pending', 'reviewed', 'follow_up_required') DEFAULT 'pending'");
    echo "✅ Added 'action_status' column<br>";
} else {
    echo "ℹ️ Column 'action_status' already exists<br>";
}

echo "<br><strong>✅ Migration complete!</strong>";
echo "<br><a href='" . BASE_URL . "/frontend/views/dashboard/professional.php'>← Back to Professional Dashboard</a>";

?>
