<?php
require_once dirname(__FILE__, 1) . '/backend/config/database.php';

$output = "Professionals:\n";
$result = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role='professional'");
while ($row = mysqli_fetch_assoc($result)) {
    $output .= "{$row['id']}: {$row['name']} ({$row['email']})\n";
}

file_put_contents('debug_prof_list.txt', $output);
echo "File written.";
?>
