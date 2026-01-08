<?php
$mysqli = new mysqli("localhost", "root", "", "bexindoberkat");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$sqlTransactions = "CREATE TABLE IF NOT EXISTS zefatex_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) NOT NULL,
    customer_name VARCHAR(100),
    transaction_date DATETIME,
    total_amount DECIMAL(15,2) DEFAULT 0,
    description TEXT,
    image_path VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (invoice_number),
    INDEX (customer_name)
)";

if ($mysqli->query($sqlTransactions)) {
    echo "Table zefatex_transactions created successfully.\n";
} else {
    echo "Error creating table zefatex_transactions: " . $mysqli->error . "\n";
}

$sqlItems = "CREATE TABLE IF NOT EXISTS zefatex_transaction_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT,
    description TEXT,
    qty DECIMAL(15,2) DEFAULT 1,
    price DECIMAL(15,2) DEFAULT 0,
    amount DECIMAL(15,2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES zefatex_transactions(id) ON DELETE CASCADE
)";

if ($mysqli->query($sqlItems)) {
    echo "Table zefatex_transaction_items created successfully.\n";
} else {
    echo "Error creating table zefatex_transaction_items: " . $mysqli->error . "\n";
}

$mysqli->close();
?>
