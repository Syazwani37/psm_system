<?php
require_once dirname(__FILE__, 1) . '/backend/config/database.php';

// Check tables
$result = mysqli_query($conn, "SHOW TABLES");
echo "Tables:\n";
while($row = mysqli_fetch_row($result)) {
    echo "- " . $row[0] . "\n";
}

// Check if consultations table exists
$tableExists = false;
$result = mysqli_query($conn, "SHOW TABLES LIKE 'consultations'");
if(mysqli_num_rows($result) > 0) $tableExists = true;

if (!$tableExists) {
    echo "\nCreating consultations table...\n";
    $sql = "CREATE TABLE consultations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        professional_id INT,
        scheduled_at DATETIME,
        status ENUM('pending','accepted','rejected','completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if(mysqli_query($conn, $sql)) echo "Table created.\n";
    else echo "Error creating table: " . mysqli_error($conn) . "\n";
}

// Seed dummy consultations if empty
$count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM consultations"))['c'];
if ($count == 0) {
    echo "Seeding consultations...\n";
    mysqli_query($conn, "INSERT INTO consultations (user_id, professional_id, scheduled_at, status) VALUES 
        (1, 2, '" . date('Y-m-d H:i:s', strtotime('+1 day')) . "', 'pending'),
        (1, 2, '" . date('Y-m-d H:i:s', strtotime('+2 days')) . "', 'pending'),
        (1, 2, '" . date('Y-m-d H:i:s', strtotime('+5 days')) . "', 'accepted')
    ");
    echo "Seeded 3 consultations.\n";
}

echo "Done.";
?>
