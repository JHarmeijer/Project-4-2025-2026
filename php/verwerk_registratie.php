<html lang="nl">
<head>
    <meta charset = "UTF-8">
    <meta http-equiv = "X-UA-Compatible" content="IE-edge">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
</head>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="../css/style.css">

<nav class="menu">
    <ul>
        <li><a href="../index.php">Home</a></li>
        <li><a href="registratie.php">Registratie</a></li>
        <li><a href="inlog.php">Inloggen</a></li>
</ul>
</nav>

<?php
include 'db_connect.php';

$gebruikersnaam = $_POST['naam'];
$email = $_POST['email'];
$wachtwoord = password_hash($_POST['password'], PASSWORD_DEFAULT);

if ($_POST['password'] !== $_POST['password2']){
    echo "Wachtwoorden komen niet overeen, probeer het nog een keer.";
    exit;
} 

$stmt = $conn->prepare("INSERT INTO gebruikers (gebruikersnaam, email, wachtwoord) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $gebruikersnaam, $email, $wachtwoord);


if ($stmt->execute()) {
    header("location: inlog.php");
    exit();
} else {
    echo "Fout: Er is wat fout gegaan, probeer het later nog eens. " . $stmt->error;
}
$password = password_hash("admin123", PASSWORD_DEFAULT);
$stmt->close();
$conn->close();
?>