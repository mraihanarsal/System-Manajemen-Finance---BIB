<?php
$mysqli = new mysqli("localhost", "root", "", "bexindoberkat");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 1. Truncate table because old data is corrupted (id_toko=0)
$mysqli->query("TRUNCATE TABLE tiktok_transactions");
echo "Table truncated.\n";

// 2. Alter table
$sql = "ALTER TABLE tiktok_transactions MODIFY COLUMN id_toko VARCHAR(50) NOT NULL";

if ($mysqli->query($sql) === TRUE) {
    echo "Column id_toko modified to VARCHAR(50) successfully";
} else {
    echo "Error modifying column: " . $mysqli->error;
}

$mysqli->close();
?>
