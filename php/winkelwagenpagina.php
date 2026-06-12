<?php
session_start();
include "../includes/header.php";
include "../includes/db_connect.php"; 

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

if (isset($_POST['remove_coupon'])) {
    unset($_SESSION['coupon']);
    header("Location: winkelwagenpagina.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
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

        .empty-cart {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        button {
            padding: 10px 15px;
            cursor: pointer;
            margin-top: 10px;
        }

        .coupon-box {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>

<div class="container">

    <h1> Winkelwagen</h1>

    <!-- WINKELWAGEN (NU NOG LEEG) -->
    <?php if (empty($_SESSION['cart'])): ?>

        <div class="empty-cart">
            <h2>Winkelwagen is leeg</h2>
            <p>Voeg producten toe om verder te gaan.</p>
        </div>

    <?php else: ?>

        <p>Hier komen later je producten te staan.</p>

    <?php endif; ?>

    <!-- COUPON FORM -->
    <div class="coupon-box">

        <form method="POST">
            <label>Kortingscode:</label><br>
            <input type="text" name="coupon" placeholder="Voer code in">
            <button type="submit" name="check_coupon">Toepassen</button>
        </form>

        <?php if (!empty($melding)): ?>
            <p><?= htmlspecialchars($melding) ?></p>
        <?php endif; ?>

        <!-- ACTIEVE COUPON -->
        <?php if (isset($_SESSION['coupon'])): ?>
            <p>
                Actieve code:
                <strong><?= htmlspecialchars($_SESSION['coupon']['code']) ?></strong>
            </p>

            <form method="POST">
                <button type="submit" name="remove_coupon">
                    Verwijder kortingscode
                </button>
            </form>
        <?php endif; ?>

    </div>

    <!-- CHECKOUT (NU NOG DISABLED) -->
    <button disabled>Afrekenen</button>

</div>

</body>
</html>