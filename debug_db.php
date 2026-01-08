<?php
$mysqli = new mysqli("localhost", "root", "", "bexindoberkat");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$sql = "SELECT id, id_toko, settlement, profit, created_at FROM tiktok_transactions ORDER BY id DESC LIMIT 10";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - Toko: [" . $row["id_toko"]. "] - Settle: " . $row["settlement"]. " - P: " . $row["profit"]. "\n";
    }
} else {
    echo "0 results";
}
$mysqli->close();
?>
