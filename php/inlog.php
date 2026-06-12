<?php
session_start();
include "../includes/header.php";
include "../includes/db_connect.php";
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
    <title>Inloggen</title>
</head>
<body>

    <div class="login-wrapper">
    <div class="login-kaart">

        <h2 class="login-titel">Inloggen</h2>

        <form action="verwerk_inlog.php" method="POST">

            <label for="email">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Voer je e-mailadres in"
                required
            >

            <label for="wachtwoord">Wachtwoord</label>
            <input
                type="password"
                id="wachtwoord"
                name="wachtwoord"
                placeholder="Voer je wachtwoord in"
                required
            >

            <button type="submit">
                Inloggen
            </button>

        </form>

        <div class="login-link">
            Nog geen account?
            <a href="../php/registratie.php">Registreren</a>
        </div>

    </div>
</div>

</body>
</html>