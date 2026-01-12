<?php
require_once 'backend/config/database.php';

$result = mysqli_query($conn, "SHOW COLUMNS FROM users");
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
