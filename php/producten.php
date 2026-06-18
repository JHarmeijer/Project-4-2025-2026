<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
 
require __DIR__ . '/../includes/db_connect.php';
 
$resultaat = $conn->query('SELECT product_ID, product_naam, prijs, voorraad, img FROM producten');
 
if ($resultaat === false) {
    echo json_encode(['fout' => $conn->error]);
    exit;
}
 
$producten = [];
while ($rij = $resultaat->fetch_assoc()) {
    $producten[] = $rij;
}
 
echo json_encode($producten);
?>
 