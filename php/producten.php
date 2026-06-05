<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require "../includes/db_connect.php";

$resultaat = $conn->query('SELECT product_ID, product_naam, prijs FROM producten');

$producten = [];

while ($rij = $resultaat->fetch_assoc()) {
    $producten[] = $rij;
}

echo json_encode($producten);
?>