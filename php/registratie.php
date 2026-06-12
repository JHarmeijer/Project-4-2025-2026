<?php
session_start();
include "../includes/header.php";
include "../includes/db_connect.php";

if (!isset($_SESSION['gebruiker_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
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
    <title>Registreren</title>
</head>

<body>

<div class="registratie-wrapper">
    <div class="registratie-kaart">

        <h2 class="registratie-titel">Registreren</h2>

        <form action="verwerk_registratie.php" method="post">

            <label for="naam">Naam</label>
            <input
                type="text"
                id="naam"
                name="naam"
                placeholder="Vul je naam in"
                required
            >

            <label for="email">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Vul je e-mailadres in"
                required
            >

            <label for="password">Wachtwoord</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Maak een wachtwoord"
                required
            >

            <label for="password2">Bevestig wachtwoord</label>
            <input
                type="password"
                id="password2"
                name="password2"
                placeholder="Herhaal je wachtwoord"
                required
            >

            <button type="submit">
                Registreren
            </button>

        </form>

        <div class="registratie-link">
            Heb je al een account?
            <a href="../php/inlog.php">Inloggen</a>
        </div>

    </div>
</div>

</body>
</html>