<?php
/**
 * Script to create default expert accounts
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/backend/config/database.php';

// List of experts to create
$experts = [
    [
        'name' => 'Dr. Hanan',
        'email' => 'hanan@psm.com',
        'password' => 'password123',
        'role' => 'professional'
    ],
    [
        'name' => 'Dr. Izzah',
        'email' => 'izzah@psm.com',
        'password' => 'password123',
        'role' => 'professional'
    ],
    [
        'name' => 'Dr. Fara',
        'email' => 'fara@psm.com',
        'password' => 'password123',
        'role' => 'professional'
    ]
];

echo "<h1>Creating Expert Accounts...</h1>";

foreach ($experts as $expert) {
    $name = mysqli_real_escape_string($conn, $expert['name']);
    $email = mysqli_real_escape_string($conn, $expert['email']);
    $password = mysqli_real_escape_string($conn, $expert['password']); // Storing plain text as per existing login.php logic
    $role = mysqli_real_escape_string($conn, $expert['role']);

    // Check if user exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) == 0) {
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        if (mysqli_query($conn, $sql)) {
            echo "<p style='color: green;'>Created account: <strong>$name</strong> ($email)</p>";
        } else {
            echo "<p style='color: red;'>Error creating $name: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>Account already exists: <strong>$name</strong> ($email)</p>";
    }
}

echo "<h2>Done! You can now login with these accounts.</h2>";
echo "<p>Default password for all: <strong>password123</strong></p>";
?>
