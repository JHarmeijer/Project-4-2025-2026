<?php
session_start();
include "../includes/header.php";
include "../includes/db_connect.php";

if (!isset($_SESSION["gebruiker_id"])) {
    die("Je moet ingelogd zijn om te bestellen.");
}

$melding = "";

// ==========================================
// ✅ NIEUW: PRODUCTEN OPSLAAN IN SESSIE
// Vangt de POST op van bestellen.php
// Slaat product_ID en aantal op in $_SESSION['winkelwagen']
// ==========================================
if (isset($_POST['toevoegen'])) {
    if (!isset($_SESSION['winkelwagen'])) {
        $_SESSION['winkelwagen'] = []; // lege winkelwagen aanmaken
    }

    foreach ($_POST as $key => $value) {
        // Alleen velden die beginnen met 'product_' en meer dan 0 zijn
        if (strpos($key, 'product_') === 0 && (int)$value > 0) {
            $product_ID = (int) str_replace('product_', '', $key);
            $aantal     = (int) $value;

            // Als product al in winkelwagen zit, aantal ophogen
            if (isset($_SESSION['winkelwagen'][$product_ID])) {
                $_SESSION['winkelwagen'][$product_ID] += $aantal;
            } else {
                // Anders nieuw product toevoegen
                $_SESSION['winkelwagen'][$product_ID] = $aantal;
            }
        }
    }
}

// ==========================================
// ✅ NIEUW: PRODUCT VERWIJDEREN UIT WINKELWAGEN
// Wordt aangeroepen via ?verwijder=product_ID in de URL
// ==========================================
if (isset($_GET['verwijder'])) {
    $product_ID = (int) $_GET['verwijder'];
    unset($_SESSION['winkelwagen'][$product_ID]);
    header('Location: winkelwagenpagina.php');
    exit;
}

// ==========================================
// ONVERANDERD: COUPON TOEPASSEN
// ==========================================
if (isset($_POST['check_coupon'])) {
    $coupon = trim($_POST['coupon']);

    $stmt = $conn->prepare("SELECT * FROM coupons WHERE coupon_code = ? AND actief = 1");
    $stmt->bind_param("s", $coupon);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $couponData = $result->fetch_assoc();
        $_SESSION['coupon'] = [
            'code'   => $couponData['coupon_code'],
            'type'   => $couponData['korting_type'],
            'waarde' => $couponData['kortings_waarde']
        ];
        $melding = "Kortingscode toegepast!";
    } else {
        $melding = "Ongeldige kortingscode.";
    }
}

// ONVERANDERD: COUPON VERWIJDEREN
if (isset($_POST['remove_coupon'])) {
    unset($_SESSION['coupon']);
    header("Location: winkelwagenpagina.php");
    exit;
}

// ==========================================
// ✅ NIEUW: AFREKENEN
// Stuurt door naar bestelling_verwerkt.php
// ==========================================
if (isset($_POST['afrekenen'])) {
    header('Location: bestelling_verwerkt.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Winkelwagen</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 40px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        .empty-cart { text-align: center; padding: 40px; color: #666; }
        button { padding: 10px 15px; cursor: pointer; margin-top: 10px; }
        .coupon-box { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        a.verwijder { color: red; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <h1>Winkelwagen</h1>

    <!-- MELDING tonen indien aanwezig -->
    <?php if (!empty($melding)): ?>
        <p><strong><?= htmlspecialchars($melding) ?></strong></p>
    <?php endif; ?>


    <!-- 'cart' bestond niet, waardoor winkelwagen altijd leeg leek -->
    <?php if (empty($_SESSION['winkelwagen'])): ?>
        <div class="empty-cart">
            <h2>Winkelwagen is leeg</h2>
            <p><a href="product.php">Ga terug naar de producten</a></p>
        </div>

    <?php else: ?>
        <form action="productWinkelVerwerkt.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Prijs</th>
                        <th>Aantal</th>
                        <th>Subtotaal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totaal = 0;

                    // Sessie slaat alleen product_ID + aantal op
                    // Naam en prijs worden vers uit de database gehaald
                    foreach ($_SESSION['winkelwagen'] as $product_ID => $aantal):
                        $stmt = $conn->prepare("SELECT product_naam, prijs FROM producten WHERE product_ID = ?");
                        $stmt->bind_param("i", $product_ID);
                        $stmt->execute();
                        $product = $stmt->get_result()->fetch_assoc();
                        $stmt->close();

                        $subtotaal = $product['prijs'] * $aantal;
                        $totaal   += $subtotaal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($product['product_naam']) ?></td>
                            <td>€<?= number_format($product['prijs'], 2) ?></td>
                            <td><?= $aantal ?></td>
                            <td>€<?= number_format($subtotaal, 2) ?></td>
                            <td>
                                <!-- Verwijder link stuurt product_ID mee in URL -->
                                <a class="verwijder" href="?verwijder=<?= $product_ID ?>">Verwijderen</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


            <!-- Berekent korting op basis van type: percentage of vast bedrag -->
            <?php
            $kortingsBedrag = 0;
            if (isset($_SESSION['coupon'])) {
                $coupon = $_SESSION['coupon'];
                if ($coupon['type'] === 'percentage') {
                    $kortingsBedrag = $totaal * ($coupon['waarde'] / 100);
                } else {
                    $kortingsBedrag = $coupon['waarde'];
                }
            }
            // max(0) zorgt dat totaal nooit onder €0 komt
            $eindTotaal = max(0, $totaal - $kortingsBedrag);
            ?>

            <?php if ($kortingsBedrag > 0): ?>
                <p>Subtotaal: €<?= number_format($totaal, 2) ?></p>
                <p>Korting: -€<?= number_format($kortingsBedrag, 2) ?></p>
            <?php endif; ?>

            <h3>Totaal: €<?= number_format($eindTotaal, 2) ?></h3>

            <button type="submit" name="afrekenen">Afrekenen</button>
        </form>
    <?php endif; ?>


    <div class="coupon-box">
        <form method="POST">
            <label>Kortingscode:</label><br>
            <input type="text" name="coupon" placeholder="Voer code in">
            <button type="submit" name="check_coupon">Toepassen</button>
        </form>

        <?php if (isset($_SESSION['coupon'])): ?>
            <p>Actieve code: <strong><?= htmlspecialchars($_SESSION['coupon']['code']) ?></strong></p>
            <form method="POST">
                <button type="submit" name="remove_coupon">Verwijder kortingscode</button>
            </form>
        <?php endif; ?>
    </div>

</div>
</body>
</html>