<?php
session_start();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registratie</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <a class="navbar-brand font-weight-bold" href="../index.php">Webshop</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link active" href="../php/registratie.php">Registratie</a></li>
            <li class="nav-item"><a class="nav-link" href="../php/inlog.php">Inloggen</a></li>
            <li class="nav-item"><a class="nav-link" href="../php/account.php">Account</a></li>
            <li class="nav-item"><a class="nav-link" href="../php/bestellen.php">Bestellen</a></li>
            <li class="nav-item"><a class="nav-link" href="../php/reviews.php">Reviews</a></li>

            <?php
            if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'){
                echo '<li class="nav-item"><a class="nav-link text-danger font-weight-bold" href="../php/admin.php">Admin paneel</a></li>';
            }
            ?>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm rounded">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Registreren</h2>

                    <form action="verwerk_registratie.php" method="post">
                        <div class="form-group">
                            <label for="naam">Naam:</label>
                            <input type="text" class="form-control" id="naam" name="naam" placeholder="Vul je naam in" required>
                        </div>

                        <div class="form-group">
                            <label for="email">E-mail:</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Vul je e-mail in" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Wachtwoord:</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Maak een wachtwoord" required>
                        </div>

                        <div class="form-group">
                            <label for="password2">Bevestig wachtwoord:</label>
                            <input type="password" class="form-control" id="password2" name="password2" placeholder="Herhaal je wachtwoord" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Registreer</button>
                    </form>

                    <p class="mt-3 text-center">
                        Heb je al een account? <a href="../php/inlog.php">Inloggen</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>