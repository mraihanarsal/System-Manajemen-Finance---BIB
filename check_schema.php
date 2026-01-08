<?php

$db = \Config\Database::connect();
$query = $db->query("DESCRIBE users");
$results = $query->getResultArray();

echo "Columns in users table:\n";
foreach ($results as $row) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
