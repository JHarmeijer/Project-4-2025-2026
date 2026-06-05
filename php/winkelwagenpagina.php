<?php
include "../includes/header.php";
session_start();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Winkelwagen</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            margin-top: 0;
        }

        .empty-cart {
            text-align: center;
            padding: 50px 0;
            color: #666;
        }

        .checkout {
            text-align: right;
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h1> Winkelwagen</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <h2 Winkelwagen is leeg</h2>
            <p>Voeg producten toe om verder te gaan.</p>
        </div>
    <?php else: ?>
        <p>Hier komen later je producten te staan.</p>
   
        <?php endif; ?>
    <form method="POST">
    <label for="coupon">Kortingscode:</label>
    <input type="text" name="coupon" id="coupon" placeholder="Voer code in">
    <button type="submit" name="check_coupon">Toepassen</button>
</form>

<?php
$korting = 0;
$melding = "";

if (isset($_POST['check_coupon'])) {

    $coupon = trim($_POST['coupon']);

    $sql = "SELECT * FROM coupons
            WHERE coupon_code = ?
            AND actief = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $coupon);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $couponData = $result->fetch_assoc();

        $_SESSION['coupon'] = [
            'code' => $couponData['coupon_code'],
            'type' => $couponData['korting_type'],
            'waarde' => $couponData['kortings_waarde']
        ];

        $melding = "Kortingscode toegepast!";
    } else {
        $melding = "Ongeldige kortingscode.";
    }
}
?>


<?php if (!empty($melding)): ?>
    <p><?= htmlspecialchars($melding) ?></p>
<?php endif; ?>

<?php

$totaal = 100;

if (isset($_SESSION['coupon'])) {

    if ($_SESSION['coupon']['type'] === 'percentage') {

        $korting = ($totaal * $_SESSION['coupon']['waarde']) / 100;

    } else {

        $korting = $_SESSION['coupon']['waarde'];
    }

    $nieuwTotaal = max(0, $totaal - $korting);

    echo "<p>Subtotaal: €" . number_format($totaal, 2) . "</p>";
    echo "<p>Korting: -€" . number_format($korting, 2) . "</p>";
    echo "<h3>Totaal: €" . number_format($nieuwTotaal, 2) . "</h3>";

} else {

    echo "<h3>Totaal: €" . number_format($totaal, 2) . "</h3>";
}
?>



    <div class="checkout">
        <button disabled>Afrekenen</button>
    </div>
</div>

</body>
</html>