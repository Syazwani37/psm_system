<?php
require_once 'backend/config/database.php';

$table = 'consultations';
$result = mysqli_query($conn, "SHOW COLUMNS FROM $table");

if (!$result) {
    echo "Table '$table' does not exist or error: " . mysqli_error($conn);
} else {
    echo "Columns in '$table':\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
    // Drop table to fix the issue
    echo "\nDropping table '$table' to allow recreation...\n";
    if(mysqli_query($conn, "DROP TABLE $table")) {
        echo "Table dropped successfully. Please refresh the Admin Dashboard.";
    } else {
        echo "Error dropping table: " . mysqli_error($conn);
    }
}
?>
