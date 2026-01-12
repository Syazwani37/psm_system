<?php
require_once __DIR__ . '/backend/config/database.php';

echo "<h1>Database Check: Resources</h1>";

$result = mysqli_query($conn, "SHOW TABLES LIKE 'resources'");
if (mysqli_num_rows($result) > 0) {
    echo "Table 'resources' EXISTS.<br>";
    $columns = mysqli_query($conn, "DESCRIBE resources");
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($columns)) {
        echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "Table 'resources' does NOT exist.<br>";
}
?>
