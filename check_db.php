$result = mysqli_query($conn, "SHOW COLUMNS FROM consultations");
echo "Consultations Table Schema:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
