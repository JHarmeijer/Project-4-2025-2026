<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['gebruiker_id'])) {
    die("Je moet ingelogd zijn om een bestelling te plaatsen.");
}

$klant_id = $_SESSION['gebruiker_id'];
$datum    = date('Y-m-d');

foreach ($_POST as $key => $value) {
    if (strpos($key, 'product_') === 0 && (int)$value > 0) {
        $product_ID = (int) str_replace('product_', '', $key);
        $aantal     = (int) $value;

        // Controleer of product bestaat en genoeg voorraad heeft
        $stmt = $conn->prepare("SELECT voorraad FROM producten WHERE product_ID = ?");
        $stmt->bind_param("i", $product_ID);
        $stmt->execute();
        $stmt->bind_result($voorraad);
        $stmt->fetch();
        $stmt->close();

        if ($voorraad < $aantal) {
            echo "Niet genoeg voorraad voor product #{$product_ID}.";
            continue;
        }

        // Bestelling invoegen
        $stmt = $conn->prepare("
            INSERT INTO bestellingen (klant_ID, product_ID, datum_aankoop) 
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iis", $klant_id, $product_ID, $datum);
        $stmt->execute();
        $bestelling_id = $conn->insert_id;
        $stmt->close();

        // Voorraad verlagen
        $stmt = $conn->prepare("
            UPDATE producten SET voorraad = voorraad - ? WHERE product_ID = ?
        ");
        $stmt->bind_param("ii", $aantal, $product_ID);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
echo "Je bestelling is succesvol geplaatst!";
?>