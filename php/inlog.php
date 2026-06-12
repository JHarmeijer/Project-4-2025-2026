<?php
session_start();
include "../includes/header.php";
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inloggen</title>

    <link rel="stylesheet" href="../css/style.css">

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