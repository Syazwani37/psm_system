<?php
/**
 * Script to update expert emails to @gmail.com
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'backend/config/database.php';

$updates = [
    'hanan@psm.com' => 'hanan@gmail.com',
    'izzah@psm.com' => 'izzah@gmail.com',
    'fara@psm.com' => 'fara@gmail.com'
];

echo "<h1>Updating Expert Emails...</h1>";

foreach ($updates as $old_email => $new_email) {
    // Check if user with old email exists
    $check = mysqli_query($conn, "SELECT id, name FROM users WHERE email='$old_email'");
    if (mysqli_num_rows($check) > 0) {
        $user = mysqli_fetch_assoc($check);
        $id = $user['id'];
        $name = $user['name'];
        
        $sql = "UPDATE users SET email='$new_email' WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            echo "<p style='color: green;'>Updated <strong>$name</strong>: $old_email &rarr; <strong>$new_email</strong></p>";
        } else {
            echo "<p style='color: red;'>Error updating $name: " . mysqli_error($conn) . "</p>";
        }
    } else {
        // Maybe already updated? Check new email
        $check_new = mysqli_query($conn, "SELECT id FROM users WHERE email='$new_email'");
        if (mysqli_num_rows($check_new) > 0) {
             echo "<p style='color: blue;'>User already has email <strong>$new_email</strong> (skipped)</p>";
        } else {
             echo "<p style='color: orange;'>User with email $old_email not found.</p>";
        }
    }
}

echo "<h2>Update Complete.</h2>";
?>
