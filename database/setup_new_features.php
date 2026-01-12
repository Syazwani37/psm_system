<?php
require_once dirname(__FILE__, 2) . '/backend/config/database.php';
// database/setup_new_features.php
require_once '../config/db_connect.php';

function executeQuery($conn, $sql, $message) {
    if (mysqli_query($conn, $sql)) {
        echo "[SUCCESS] $message <br>";
    } else {
        echo "[ERROR] $message: " . mysqli_error($conn) . "<br>";
    }
}

// 1. EPDS Responses Table
$sql_epds = "CREATE TABLE IF NOT EXISTS epds_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    score INT NOT NULL,
    risk_level VARCHAR(50) NOT NULL, -- 'Low', 'Medium', 'High'
    responses JSON NULL, -- Store raw answers if needed
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql_epds, "Created 'epds_responses' table");

// 2. Baby Growth Table
$sql_growth = "CREATE TABLE IF NOT EXISTS baby_growth (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    weight_kg DECIMAL(5,2),
    height_cm DECIMAL(5,2),
    head_circ_cm DECIMAL(5,2),
    notes TEXT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql_growth, "Created 'baby_growth' table");

// 3. Baby Milestones Table
$sql_milestones = "CREATE TABLE IF NOT EXISTS baby_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    milestone_id VARCHAR(50) NOT NULL, -- e.g., '1m_smile'
    milestone_name VARCHAR(100) NOT NULL,
    is_achieved BOOLEAN DEFAULT FALSE,
    achieved_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql_milestones, "Created 'baby_milestones' table");

// 4. Mom's Journal Table
$sql_journal = "CREATE TABLE IF NOT EXISTS moms_journal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mood VARCHAR(50) NOT NULL, -- 'Happy', 'Sad', etc.
    entry_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql_journal, "Created 'moms_journal' table");

// 5. Symptom Logs Table
$sql_symptoms = "CREATE TABLE IF NOT EXISTS symptom_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    week_postpartum INT,
    temperature DECIMAL(4,1),
    pain_level VARCHAR(50),
    wound_condition VARCHAR(50),
    bleeding_status VARCHAR(50),
    mood_status VARCHAR(50),
    result_status VARCHAR(20), -- 'safe', 'warning', 'danger'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql_symptoms, "Created 'symptom_logs' table");

echo "Database setup completed.";
?>
