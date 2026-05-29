<?php
$servername = "webshopProject4";
$username = "root";
$wachtwoord = "";
$database = "webshopProject4"; 

$conn = new mysqli($servername, $username, $wachtwoord, $database);

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
?> 