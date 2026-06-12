<?php
session_start();
include "../includes/header.php";
include "../includes/db_connect.php";

if (!isset($_SESSION['klant_ID'])) {
    header('Location: inlog.php');
    exit;
}


$melding = "";

if (isset($_POST['toevoegen'])) {
    if (!isset($_SESSION['winkelwagen'])) {
        $_SESSION['winkelwagen'] = [];
    }
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'product_') === 0 && (int)$value > 0) {
            $product_ID = (int) str_replace('product_', '', $key);
            $aantal     = (int) $value;
            if (isset($_SESSION['winkelwagen'][$product_ID])) {
                $_SESSION['winkelwagen'][$product_ID] += $aantal;
            } else {
                $_SESSION['winkelwagen'][$product_ID] = $aantal;
            }
        }
    }
}

if (isset($_GET['verwijder'])) {
    $product_ID = (int) $_GET['verwijder'];
    unset($_SESSION['winkelwagen'][$product_ID]);
    header('Location: winkelwagenpagina.php');
    exit;
}

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

if (isset($_POST['remove_coupon'])) {
    unset($_SESSION['coupon']);
    header("Location: winkelwagenpagina.php");
    exit;
}

if (isset($_POST['afrekenen'])) {
    header('Location: bestelling_verwerkt.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="pagina-inhoud" style="max-width: 760px;">

    <h2 class="sectie-titel">Winkelwagen</h2>

    <?php if (!empty($melding)): ?>
        <div class="<?= strpos($melding, 'toegepast') !== false ? 'melding-ok' : 'melding-err' ?>">
            <?= htmlspecialchars($melding) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['winkelwagen'])): ?>

        <div style="background: white; border: 1px solid #d6cfb8; border-radius: 12px; padding: 60px 20px; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 16px;"></div>
            <h3 style="color: #1a2236; margin-bottom: 8px;">Je winkelwagen is leeg</h3>
            <p style="color: #888; font-size: 14px; margin-bottom: 24px;">Voeg producten toe om te beginnen met bestellen.</p>
            <a href="bestellen.php" class="product-knop">Bekijk producten</a>
        </div>

    <?php else: ?>

        <!-- Producttabel -->
        <div style="background: white; border: 1px solid #d6cfb8; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px;">
            <form action="productWinkelVerwerkt.php" method="POST">
                <table class="wagen-tabel">
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
                                <td style="font-weight: bold;"><?= htmlspecialchars($product['product_naam']) ?></td>
                                <td class="prijs-cel">€<?= number_format($product['prijs'], 2) ?></td>
                                <td><?= $aantal ?>×</td>
                                <td class="prijs-cel">€<?= number_format($subtotaal, 2) ?></td>
                                <td><a class="verwijder-link" href="?verwijder=<?= $product_ID ?>">✕ Verwijder</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Totaal blok -->
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
                $eindTotaal = max(0, $totaal - $kortingsBedrag);
                ?>

                <div style="margin-top: 20px; padding-top: 16px; border-top: 2px solid #d6cfb8;">

                    <?php if ($kortingsBedrag > 0): ?>
                        <div class="totaal-rij">
                            <span>Subtotaal</span>
                            <span>€<?= number_format($totaal, 2) ?></span>
                        </div>
                        <div class="totaal-rij" style="color: #27500a;">
                            <span> Korting (<?= htmlspecialchars($_SESSION['coupon']['code']) ?>)</span>
                            <span>− €<?= number_format($kortingsBedrag, 2) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="totaal-rij totaal-eindprijs" style="margin-top: 8px;">
                        <span>Totaal</span>
                        <span>€<?= number_format($eindTotaal, 2) ?></span>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" name="afrekenen">Afrekenen</button>
                </div>

            </form>
        </div>

    <?php endif; ?>

    <!-- Coupon sectie -->
    <div style="background: white; border: 1px solid #d6cfb8; border-radius: 12px; padding: 20px 24px;">

        <div style="font-size: 14px; font-weight: bold; color: #1a2236; margin-bottom: 12px;">Kortingscode</div>

        <form method="POST" style="display: flex; gap: 10px; align-items: flex-end;">
            <div style="flex: 1;">
                <input type="text" name="coupon" placeholder="Voer code in" style="margin: 0;">
            </div>
            <button type="submit" name="check_coupon" style="margin: 0; width: auto; padding: 8px 18px;">
                Toepassen
            </button>
        </form>

        <?php if (isset($_SESSION['coupon'])): ?>
            <div class="coupon-actief">
                <span>Actieve code:</span>
                <span class="coupon-badge"><?= htmlspecialchars($_SESSION['coupon']['code']) ?></span>
                <form method="POST" style="margin: 0; margin-left: auto;">
                    <button type="submit" name="remove_coupon" class="coupon-verwijder">✕ Verwijder</button>
                </form>
            </div>
        <?php endif; ?>

    </div>

</div>

</body>
</html>