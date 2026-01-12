<?php
require_once 'backend/config/database.php';

$sql = "CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_by INT NOT NULL,
    type ENUM('info', 'warning', 'success', 'danger') DEFAULT 'info',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'announcements' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
