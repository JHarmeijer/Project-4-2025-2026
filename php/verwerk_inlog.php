<?php
include "../includes/db_connect.php";
session_start();


$email = $_POST['email'];
$wachtwoord = $_POST['wachtwoord'];

$stmt = $conn->prepare("SELECT * FROM klant WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();

    
    if (password_verify($wachtwoord, $row['wachtwoord'])){ // wachtwoord controleren //

        $_SESSION['gebruiker_id'] = $row['gebruiker_id']; // Als het overeenkomt met de gegevens in de database, dan log je in //
        $_SESSION['rol'] = $row['rol'];

        
        if ($row['rol'] === 'admin') {
            header('Location: admin.php');
            exit();
        } 
        
        else {
            header('Location: index.php');
            exit();
        }

    } else {
        echo "Onjuist wachtwoord";
    }

} else {
    echo "Gebruiker niet gevonden";
}

$stmt->close(); // Sluit de query //
$conn->close(); // Sluit de database verbinding //
?>