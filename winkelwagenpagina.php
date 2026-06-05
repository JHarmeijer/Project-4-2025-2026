<?php
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
    <h1>🛒 Winkelwagen</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <h2>Je winkelwagen is leeg</h2>
            <p>Voeg producten toe om verder te gaan.</p>
        </div>
    <?php else: ?>
        <p>Hier komen later je producten te staan.</p>
    <?php endif; ?>

    <div class="checkout">
        <button disabled>Afrekenen</button>
    </div>
</div>

</body>
</html>