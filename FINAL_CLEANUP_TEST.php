<?php
require_once dirname(__FILE__, 1) . '/backend/config/database.php';

echo "<h1 style='color: #4A5D53;'>Database Cleanup: Removing Test Professional</h1>";

// 1. Specifically targeting 'Test Professional'
$name_to_delete = 'Test Professional';

// 2. Find and Delete
$result = mysqli_query($conn, "SELECT id, name FROM users WHERE name = '$name_to_delete'");

if ($result && mysqli_num_rows($result) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        echo "<li>Deleting <strong>{$row['name']}</strong> (ID: $id)... ";
        
        // Delete related consultations first
        mysqli_query($conn, "DELETE FROM consultations WHERE professional_id = $id");
        
        // Delete the user
        if (mysqli_query($conn, "DELETE FROM users WHERE id = $id")) {
            echo "<span style='color: green;'>✅ Deleted successfully.</span></li>";
        } else {
            echo "<span style='color: red;'>❌ Error: " . mysqli_error($conn) . "</span></li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: #666;'>No account named 'Test Professional' was found in the database. It might already be gone!</p>";
}

echo "<h3>Remaining Specialists:</h3>";
$all = mysqli_query($conn, "SELECT id, name FROM users WHERE role='professional' ORDER BY name ASC");
echo "<ul style='background: #f4f4f4; padding: 15px; border-radius: 8px;'>";
while ($row = mysqli_fetch_assoc($all)) {
    echo "<li><strong>{$row['name']}</strong> (Database ID: {$row['id']})</li>";
}
echo "</ul>";

echo "<p><a href='index.php' style='display: inline-block; padding: 10px 20px; background: #8DA399; color: white; text-decoration: none; border-radius: 5px;'>Back to Dashboard</a></p>";
?>
