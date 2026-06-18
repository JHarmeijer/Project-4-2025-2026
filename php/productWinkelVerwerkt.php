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
$groep_ID  = time(); // unieke bestelling-groep

/* =========================
   COUPON OPHALEN
========================= */

$coupon_ID = null;

if (isset($_SESSION['coupon'])) {
    $couponCode = $_SESSION['coupon']['code'];

    $stmt = $conn->prepare("
        SELECT coupon_ID 
        FROM coupons 
        WHERE coupon_code = ?
    ");
    $stmt->bind_param("s", $couponCode);
    $stmt->execute();
    $stmt->bind_result($coupon_ID);
    $stmt->fetch();
    $stmt->close();
}

$fout = false;

/* =========================
   BESTELLINGEN VERWERKEN
========================= */

foreach ($winkelwagen as $product_ID => $aantal) {

    // voorraad checken
    $stmt = $conn->prepare("
        SELECT voorraad 
        FROM producten 
        WHERE product_ID = ?
    ");
    $stmt->bind_param("i", $product_ID);
    $stmt->execute();
    $stmt->bind_result($voorraad);
    $stmt->fetch();
    $stmt->close();

    if ($voorraad < $aantal) {
        $fout = true;
        continue;
    }

    // bestelling opslaan
    $stmt = $conn->prepare("
        INSERT INTO bestellingen 
        (klant_ID, product_ID, coupon_ID, datum_aankoop, groep_ID)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiisi", $klant_id, $product_ID, $coupon_ID, $datum, $groep_ID);
    $stmt->execute();
    $stmt->close();

    // voorraad verlagen
    $stmt = $conn->prepare("
        UPDATE producten 
        SET voorraad = voorraad - ? 
        WHERE product_ID = ?
    ");
    $stmt->bind_param("ii", $aantal, $product_ID);
    $stmt->execute();
    $stmt->close();
}

/* =========================
   WINKELWAGEN LEEGMAKEN
========================= */

unset($_SESSION['winkelwagen']);
unset($_SESSION['coupon']);
$conn->close();

/* =========================
   MELDING
========================= */

$ok = !$fout;

$melding = $ok
    ? "Bestelling #$groep_ID succesvol geplaatst!"
    : "Bestelling geplaatst, maar sommige producten waren niet op voorraad.";
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/style.css">
<title>Bestelling geplaatst</title>
</head>

<body>

<div class="pagina-inhoud">

    <div class="admin-kaart">

        <div class="admin-kaart-header">
            <h3>🧾 Bestelling bevestiging</h3>
        </div>

        <div class="admin-kaart-body" style="text-align:center;">

            <?php if ($ok): ?>

                <div class="melding-ok" style="font-size:16px;">
                    ✅ <?= htmlspecialchars($melding) ?>
                </div>

                <p style="margin-top:10px; color:#444;">
                    Bedankt voor je bestelling! We gaan er direct mee aan de slag.
                </p>

                <div style="margin-top:20px;">
                    <a href="account.php#bestellingen" class="product-knop">
                        Bekijk mijn bestellingen
                    </a>
                </div>

            <?php else: ?>

                <div class="melding-err" style="font-size:16px;">
                    ⚠️ <?= htmlspecialchars($melding) ?>
                </div>

                <p style="margin-top:10px; color:#444;">
                    Controleer je winkelwagen en probeer het opnieuw.
                </p>

                <div style="margin-top:20px;">
                    <a href="winkelwagenpagina.php" class="product-knop">
                        Terug naar winkelwagen
                    </a>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>