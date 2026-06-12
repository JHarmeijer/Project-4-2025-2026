<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require __DIR__ . '/../includes/db_connect.php';

// Gegevens ophalen die vanuit het formulier binnenkomen
$product_id  = $_POST['product_id'];
$naam        = $_POST['naam'];
$beoordeling = $_POST['beoordeling'];
$tekst       = $_POST['tekst'];
$pluspunt    = $_POST['pluspunt'];
$minpunt     = $_POST['minpunt'];

// Beveiligen tegen SQL-injectie
$product_id  = $conn->real_escape_string($product_id);
$naam        = $conn->real_escape_string($naam);
$beoordeling = $conn->real_escape_string($beoordeling);
$tekst       = $conn->real_escape_string($tekst);
$pluspunt    = $conn->real_escape_string($pluspunt);
$minpunt     = $conn->real_escape_string($minpunt);

// Opslaan in de database
$sql = "INSERT INTO reviews 
        (product_ID, naam, beoordeling, tekst, pluspunt, minpunt) 
        VALUES 
        ('$product_id', '$naam', '$beoordeling', '$tekst', '$pluspunt', '$minpunt')";

if ($conn->query($sql)) {
    echo json_encode(['succes' => true, 'bericht' => 'Review opgeslagen!']);
} else {
    echo json_encode(['succes' => false, 'bericht' => 'Er ging iets mis: ' . $conn->error]);
}
?>