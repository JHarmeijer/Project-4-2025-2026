<?php
session_start();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inloggen</title>

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
            <li class="nav-item"><a class="nav-link" href="../php/registratie.php">Registratie</a></li>
            <li class="nav-item"><a class="nav-link active" href="../php/inlog.php">Inloggen</a></li>
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
        <div class="col-md-5">
            <div class="card shadow-sm rounded">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Inloggen</h2>

                    <form action="verwerk_inlog.php" method="POST">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="E-mail" required>
                        </div>

                        <div class="form-group">
                            <input type="password" name="wachtwoord" class="form-control" placeholder="Wachtwoord" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Inloggen</button>
                    </form>

                    <p class="mt-3 text-center">
                        Nog geen account? <a href="../php/registratie.php">Registreren</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>