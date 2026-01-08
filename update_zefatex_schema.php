<?php
$mysqli = new mysqli("localhost", "root", "", "bexindoberkat");

if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

// 1. Change Qty to DECIMAL
$sql1 = "ALTER TABLE zefatex_transaction_items MODIFY qty DECIMAL(15,2) DEFAULT 0";
if ($mysqli->query($sql1)) {
    echo "Updated 'qty' to DECIMAL(15,2).\n";
} else {
    echo "Error updating 'qty': " . $mysqli->error . "\n";
}

// 2. Change Description to TEXT
$sql2 = "ALTER TABLE zefatex_transaction_items MODIFY description TEXT";
if ($mysqli->query($sql2)) {
    echo "Updated 'description' in items to TEXT.\n";
} else {
    echo "Error updating 'description': " . $mysqli->error . "\n";
}

// 3. Change Transaction Header Description to TEXT if needed
$sql3 = "ALTER TABLE zefatex_transactions MODIFY description TEXT";
if ($mysqli->query($sql3)) {
    echo "Updated 'description' in transactions to TEXT.\n";
} else {
    echo "Error updating 'description': " . $mysqli->error . "\n";
}

$mysqli->close();
?>
