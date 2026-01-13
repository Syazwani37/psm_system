<?php
/**
 * PSM System - Cleanup Professional Accounts
 * This script removes duplicate or unexpected professional accounts
 */

require_once dirname(__FILE__, 1) . '/backend/config/database.php';

echo "<h1>Cleaning up Professional Accounts...</h1>";

$expected_doctors = ['Dr. Hanan', 'Dr. Izzah', 'Dr. Fara'];

// Find all professional accounts that are NOT in the expected list
$query = "SELECT id, name, email FROM users WHERE role='professional' AND name NOT IN ('Dr. Hanan', 'Dr. Izzah', 'Dr. Fara')";
$result = mysqli_query($conn, $query);

echo "<h2>Removing Unexpected Professional Accounts:</h2>";

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<p>Removing: {$row['name']} ({$row['email']}) - ID: {$row['id']}</p>";
        
        // Remove consultations associated with this professional first (to avoid foreign key constraint issues)
        $del_consultations = mysqli_query($conn, "DELETE FROM consultations WHERE professional_id = {$row['id']}");
        
        // Remove the user
        $delete_query = "DELETE FROM users WHERE id = {$row['id']}";
        mysqli_query($conn, $delete_query);
    }
    echo "<p style='color: green;'>✅ Unexpected professional accounts removed.</p>";
} else {
    echo "<p>No unexpected professional accounts found.</p>";
}

// Check for duplicates of expected doctors and keep only the first one
echo "<h2>Removing Duplicate Entries:</h2>";
foreach ($expected_doctors as $doctor) {
    $dup_query = "SELECT id FROM users WHERE name='{$doctor}' AND role='professional' ORDER BY id";
    $dup_result = mysqli_query($conn, $dup_query);
    $duplicates = [];
    
    while ($row = mysqli_fetch_assoc($dup_result)) {
        $duplicates[] = $row['id'];
    }
    
    // Keep the first one, remove the rest
    if (count($duplicates) > 1) {
        echo "<p>Found " . count($duplicates) . " entries for {$doctor}. Keeping ID: {$duplicates[0]}, removing others.</p>";
        
        // Remove duplicates (all except the first one)
        $ids_to_remove = array_slice($duplicates, 1);
        $ids_str = implode(',', $ids_to_remove);
        
        // Remove consultations associated with these duplicate professionals first
        $del_consultations = mysqli_query($conn, "DELETE FROM consultations WHERE professional_id IN ({$ids_str})");
        
        // Remove the duplicate users
        $delete_query = "DELETE FROM users WHERE id IN ({$ids_str})";
        mysqli_query($conn, $delete_query);
        
        echo "<p style='color: green;'>✅ Duplicates for {$doctor} removed.</p>";
    } else {
        echo "<p>✅ {$doctor}: No duplicates found.</p>";
    }
}

echo "<h3>✅ Cleanup completed!</h3>";
echo "<p><a href='check_professionals.php'>Check Professional Accounts Again</a> | <a href='.'>Back to Home</a></p>";
?>