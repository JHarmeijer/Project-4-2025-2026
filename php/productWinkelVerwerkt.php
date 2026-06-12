<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['gebruiker_id'])) {
    die("Je moet ingelogd zijn om een bestelling te plaatsen.");
}

$winkelwagen = $_SESSION['winkelwagen'] ?? [];

if (empty($winkelwagen)) {
    die("Je winkelwagen is leeg.");
}

$klant_id = $_SESSION['gebruiker_id'];
$datum    = date('Y-m-d');

// ✅ Uniek groep_ID aanmaken voor alle producten in deze bestelling
// time() geeft het huidige tijdstempel als uniek getal
$groep_ID = time();

// Coupon ID ophalen uit database
$coupon_ID = null;
if (isset($_SESSION['coupon'])) {
    $couponCode = $_SESSION['coupon']['code'];
    $stmt = $conn->prepare("SELECT coupon_ID FROM coupons WHERE coupon_code = ?");
    $stmt->bind_param("s", $couponCode);
    $stmt->execute();
    $stmt->bind_result($coupon_ID);
    $stmt->fetch();
    $stmt->close();
}

$fout = false;

// ✅ Loop door winkelwagen sessie
foreach ($winkelwagen as $product_ID => $aantal) {

    // Voorraad controleren
    $stmt = $conn->prepare("SELECT voorraad FROM producten WHERE product_ID = ?");
    $stmt->bind_param("i", $product_ID);
    $stmt->execute();
    $stmt->bind_result($voorraad);
    $stmt->fetch();
    $stmt->close();

    if ($voorraad < $aantal) {
        echo "Niet genoeg voorraad voor product #{$product_ID}.<br>";
        $fout = true;
        continue;
    }

    // ✅ Bestelling invoegen met groep_ID
    $stmt = $conn->prepare("
        INSERT INTO bestellingen (klant_ID, product_ID, coupon_ID, datum_aankoop, groep_ID) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiisi", $klant_id, $product_ID, $coupon_ID, $datum, $groep_ID);
    $stmt->execute();
    $stmt->close();

    // Voorraad verlagen
    $stmt = $conn->prepare("UPDATE producten SET voorraad = voorraad - ? WHERE product_ID = ?");
    $stmt->bind_param("ii", $aantal, $product_ID);
    $stmt->execute();
    $stmt->close();
}

// Winkelwagen en coupon leegmaken
unset($_SESSION['winkelwagen']);
unset($_SESSION['coupon']);
$conn->close();

if (!$fout) {
    echo "✅ Bestelling #$groep_ID succesvol geplaatst!";
} else {
    echo "⚠️ Bestelling geplaatst, maar sommige producten hadden onvoldoende voorraad.";
}
?>