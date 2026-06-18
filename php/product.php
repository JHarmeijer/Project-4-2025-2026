<?php
session_start(); 
include "../includes/header.php";
include "../includes/db_connect.php";

if (!isset($_SESSION['gebruiker_id'])) {
    header('Location: inlog.php');
    exit;
}

$sql = "SELECT product_ID, product_naam, prijs, voorraad, img FROM producten";
$result = $conn->query($sql); 
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

<div class="pagina-inhoud" style="max-width: 900px;">

    <h2 class="sectie-titel">Plaats je bestelling</h2>

    <form action="winkelwagenpagina.php" method="POST">

        <div class="row g-4">
            <?php while ($product = $result->fetch_assoc()) { ?>

                <div class="col-md-4">
                    <div class="product-bestel-kaart">

                        <?php if (!empty($product['img'])): ?>
                            <div class="product-bestel-img-wrapper">
                                <img src="<?php echo htmlspecialchars($product['img']); ?>"
                                     alt="<?php echo htmlspecialchars($product['product_naam']); ?>"
                                     class="product-bestel-img">
                            </div>
                        <?php else: ?>
                            <div class="product-bestel-img-wrapper product-bestel-geen-img">
                                <span style="font-size: 40px;">🍽️</span>
                            </div>
                        <?php endif; ?>

                        <div class="product-bestel-body">

                            <div class="product-naam" style="font-size: 16px; font-weight: bold; color: #1a2236; margin-bottom: 6px;">
                                <?php echo htmlspecialchars($product['product_naam']); ?>
                            </div>

                            <div style="font-size: 18px; font-weight: bold; color: #c9a84c; margin-bottom: 4px;">
                                €<?php echo number_format($product['prijs'], 2); ?>
                            </div>

                            <div style="font-size: 12px; color: #888; margin-bottom: 4px;">
                                Product ID: <?php echo (int)$product['product_ID']; ?>
                            </div>

                            <div style="font-size: 12px; color: #888; margin-bottom: 16px;">
                                Voorraad: <?php echo (int)$product['voorraad']; ?> stuks
                            </div>

                            <label for="product_<?php echo $product['product_ID']; ?>"
                                   style="display: block; font-size: 13px; font-weight: bold; color: #1a2236; margin-bottom: 5px;">
                                Aantal:
                            </label>
                            <input type="number"
                                   name="product_<?php echo $product['product_ID']; ?>"
                                   id="product_<?php echo $product['product_ID']; ?>"
                                   min="0"
                                   max="<?php echo (int)$product['voorraad']; ?>"
                                   value="0">
                        </div>
                    </div>
                </div>

            <?php } ?>
        </div>

        <div class="text-center mt-5">
            <button type="submit" name="toevoegen">
                Bestellen
            </button>
        </div>

    </form>
</div>

</body>
</html>