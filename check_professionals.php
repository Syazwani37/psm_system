<?php
/**
 * PSM System - Check Professional Accounts
 * This script checks for duplicate or unexpected professional accounts
 */

require_once dirname(__FILE__, 1) . '/backend/config/database.php';

echo "<h1>Checking Professional Accounts...</h1>";

// Query all professional accounts
$query = "SELECT id, name, email, created_at FROM users WHERE role='professional' ORDER BY name, id";
$result = mysqli_query($conn, $query);

echo "<h2>All Professional Accounts:</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Created At</th></tr>";

$expected_doctors = ['Dr. Hanan', 'Dr. Izzah', 'Dr. Fara'];
$unexpected_found = false;

while ($row = mysqli_fetch_assoc($result)) {
    $class = in_array($row['name'], $expected_doctors) ? '' : ' bgcolor="#ffcccc"';
    echo "<tr{$class}>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['email']}</td>";
    echo "<td>{$row['created_at']}</td>";
    echo "</tr>";
    
    if (!in_array($row['name'], $expected_doctors)) {
        $unexpected_found = true;
    }
}

echo "</table>";

if ($unexpected_found) {
    echo "<h3 style='color: red;'>⚠️ Warning: Found unexpected professional accounts!</h3>";
    echo "<p>Only the following doctors should exist:</p>";
    echo "<ul>";
    foreach ($expected_doctors as $doctor) {
        echo "<li>{$doctor}</li>";
    }
    echo "</ul>";
} else {
    echo "<h3 style='color: green;'>✅ All professional accounts are as expected.</h3>";
}

// Check for duplicates of expected doctors
echo "<h2>Duplicate Check for Expected Doctors:</h2>";
foreach ($expected_doctors as $doctor) {
    $dup_query = "SELECT id, name, email, created_at FROM users WHERE name='{$doctor}' AND role='professional'";
    $dup_result = mysqli_query($conn, $dup_query);
    $count = mysqli_num_rows($dup_result);
    
    if ($count > 1) {
        echo "<p style='color: orange;'>⚠️ Found {$count} entries for {$doctor}:</p>";
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($dup_result)) {
            echo "<li>ID: {$row['id']}, Email: {$row['email']}, Created: {$row['created_at']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: green;'>✅ {$doctor}: OK ({$count} entry)</p>";
    }
}

echo "<br><a href='.'>Back to Home</a>";
?>