<?php
require_once dirname(__FILE__, 1) . '/backend/config/database.php';

echo "<h1>Professional Deletion Tool</h1>";

// 1. Find the professionals that are NOT our main experts
$main_experts = ["'Dr. Hanan'", "'Dr. Izzah'", "'Dr. Fara'"];
$experts_list = implode(',', $main_experts);
$result = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role='professional' AND name NOT IN ($experts_list)");

if (mysqli_num_rows($result) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        echo "<li>Deleting user: <strong>{$row['name']}</strong> ({$row['email']}) - ID: $id... ";
        
        // Also delete their consultations to avoid foreign key issues (if any) or orphaned records
        mysqli_query($conn, "DELETE FROM consultations WHERE professional_id = $id");
        
        // Delete the user
        if (mysqli_query($conn, "DELETE FROM users WHERE id = $id")) {
            echo "<span style='color: green;'>Success!</span></li>";
        } else {
            echo "<span style='color: red;'>Error: " . mysqli_error($conn) . "</span></li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>No professional matching 'test' found.</p>";
}

echo "<h3>Current Professionals:</h3>";
$all = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role='professional'");
echo "<ul>";
while ($row = mysqli_fetch_assoc($all)) {
    echo "<li>{$row['id']}: {$row['name']} ({{$row['email']}})</li>";
}
echo "</ul>";

echo "<a href='index.php'>Back to Home</a>";
?>
