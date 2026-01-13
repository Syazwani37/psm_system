<?php
require_once dirname(__FILE__, 1) . '/backend/config/database.php';

echo "<h1 style='color: #4A5D53;'>User Deletion Tool: Nurul Aqilah</h1>";

// 1. Search for the user
$name_pattern = 'Nurul Aqilah%';
$result = mysqli_query($conn, "SELECT id, name, email, role FROM users WHERE name LIKE '$name_pattern' OR email LIKE 'nurul%'");

if ($result && mysqli_num_rows($result) > 0) {
    echo "<h3>Matching Users Found:</h3><ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $name = $row['name'];
        $email = $row['email'];
        $role = $row['role'];
        
        echo "<li>ID: $id | Name: <strong>$name</strong> | Email: $email | Role: $role ... ";
        
        // Deleting associated records (foreign key safeguard)
        mysqli_query($conn, "DELETE FROM consultations WHERE user_id = $id OR professional_id = $id");
        mysqli_query($conn, "DELETE FROM symptom_logs WHERE user_id = $id");
        mysqli_query($conn, "DELETE FROM baby_growth WHERE user_id = $id");
        mysqli_query($conn, "DELETE FROM moms_journal WHERE user_id = $id");
        mysqli_query($conn, "DELETE FROM epds_responses WHERE user_id = $id");
        mysqli_query($conn, "DELETE FROM baby_milestones WHERE user_id = $id");
        
        // 2. Delete the user
        if (mysqli_query($conn, "DELETE FROM users WHERE id = $id")) {
            echo "<span style='color: green;'>✅ Deleted successfully.</span></li>";
        } else {
            echo "<span style='color: red;'>❌ Error: " . mysqli_error($conn) . "</span></li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: orange;'>No user found matching 'Nurul Aqilah'. They might have already been deleted.</p>";
}

echo "<h3>Current Users List:</h3>";
$all = mysqli_query($conn, "SELECT id, name, role FROM users ORDER BY role, name ASC");
echo "<ul style='background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace;'>";
while ($row = mysqli_fetch_assoc($all)) {
    echo "<li>[{$row['role']}] ID: {$row['id']} - {$row['name']}</li>";
}
echo "</ul>";

echo "<p><a href='index.php' style='display: inline-block; padding: 8px 16px; background: #8DA399; color: white; text-decoration: none; border-radius: 4px;'>Back to Home</a></p>";
?>
