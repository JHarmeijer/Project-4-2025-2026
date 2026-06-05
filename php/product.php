<?php
session_start(); 
include "../includes/header.php";
include "../includes/db_connect.php";


$sql = "SELECT product_ID, product_naam, prijs, voorraad FROM producten";
$result = $conn->query($sql); 
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <link href="../CSS/stylesheet.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Bestellen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Plaats je bestelling</h2>
    <form action="product_verwerkt.php" method="POST">
        <div class="row">
            <?php while ($product = $result->fetch_assoc()) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($product['product_naam']); ?>
                            </h5>
                            <p class="card-text text-muted">
                                Product ID: <?php echo (int)$product['product_ID'];  ?>
                            </p>
                            <p class="card-text">
                                Prijs: €<?php echo number_format($product['prijs'], 2); ?>
                            </p>
                            <p class="card-text">
                                Voorraad: <?php echo (int)$product['voorraad']; ?>
                            </p>
                            <label for="product_<?php echo $product['product_ID']; ?>">Aantal:</label>
                            <input type="number"
                                   name="product_<?php echo $product['product_ID'];   ?>"
                                   id="product_<?php echo $product['product_ID']; ?>"
                                   class="form-control"
                                   min="0"
                                   max="<?php echo (int)$product['voorraad']; ?>"
                                   value="0">
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Bestellen</button>
        </div>
    </form>
</div>
</body>
</html>