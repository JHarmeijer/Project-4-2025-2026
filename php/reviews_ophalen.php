<?php
require __DIR__ . '/../includes/db_connect.php';

$sql = "SELECT r.naam, r.beoordeling, r.tekst, r.pluspunt, r.minpunt, r.datum, p.product_naam
        FROM reviews r
        JOIN producten p ON r.product_ID = p.product_ID
        ORDER BY r.datum DESC";

$resultaat = $conn->query($sql);

// Foutcontrole toegevoegd
if ($resultaat === false) {
    echo json_encode(['fout' => $conn->error]);
    exit;
}

$reviews = [];
while ($rij = $resultaat->fetch_assoc()) {
    $reviews[] = $rij;
}

echo json_encode($reviews);
?>