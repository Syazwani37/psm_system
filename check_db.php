<?php
require_once __DIR__ . '/backend/config/database.php';

echo "<h3>Professionals (Experts)</h3>";
$result = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role='professional'");
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['id'] . ": " . $row['name'] . " (" . $row['email'] . ")<br>";
}

echo "<h3>Consultations Table Data</h3>";
$result = mysqli_query($conn, "SELECT * FROM consultations");
if (mysqli_num_rows($result) > 0) {
    echo "<table border='1'><tr><th>ID</th><th>User</th><th>Prof</th><th>Date</th><th>Reason</th><th>Status</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['user_id']}</td><td>{$row['professional_id']}</td><td>{$row['scheduled_at']}</td><td>{$row['reason']}</td><td>{$row['status']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "No consultation records found.";
}
?>
