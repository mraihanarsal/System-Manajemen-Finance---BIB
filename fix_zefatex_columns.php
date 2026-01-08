<?php
$mysqli = new mysqli("localhost", "root", "", "bexindoberkat");

if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

// 1. Add description to zefatex_transactions if not exists
// We try to ADD it. If it exists, it might error, so we can use modifying logic or check first.
// Simplest is to just try ADD, if fails try MODIFY.

$sql_add = "ALTER TABLE zefatex_transactions ADD COLUMN description TEXT AFTER total_amount";
if ($mysqli->query($sql_add)) {
    echo "Added 'description' to zefatex_transactions.\n";
} else {
    echo "Add failed (maybe exists?): " . $mysqli->error . "\n";
    // Try MODIFY just in case it exists but is wrong type
    $sql_mod = "ALTER TABLE zefatex_transactions MODIFY description TEXT";
    if ($mysqli->query($sql_mod)) {
        echo "Modified 'description' to TEXT.\n";
    }
}

// 2. Ensure items description is TEXT
$sql_items_desc = "ALTER TABLE zefatex_transaction_items MODIFY description TEXT";
$mysqli->query($sql_items_desc);

// 3. Ensure qty is DECIMAL(15,2)
$sql_items_qty = "ALTER TABLE zefatex_transaction_items MODIFY qty DECIMAL(15,2) DEFAULT 0";
$mysqli->query($sql_items_qty);

echo "Detailed Schema Fix Complete.\n";

$mysqli->close();
?>
