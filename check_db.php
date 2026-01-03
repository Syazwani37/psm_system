<?php
require_once 'backend/config/database.php';

$sql = "SELECT id, name, email, password, role FROM users";
$result = mysqli_query($conn, $sql);

if ($result) {
    echo "<h1>Users in Database:</h1>";
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Password</th><th>Role</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['password']}</td><td>{$row['role']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
