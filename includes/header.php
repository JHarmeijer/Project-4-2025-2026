<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="../css/style.css">


<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <a class="navbar-brand font-weight-bold" href="../index.php">Webshop</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="../php/index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="../php/registratie.php">Registratie</a></li>
            <li class="nav-item"><a class="nav-link active" href="../php/inlog.php">Inloggen</a></li>
            <li class="nav-item"><a class="nav-link" href="../php/account.php">Account</a></li>
            <li class="nav-item"><a class="nav-link" href="../php/product.php">producten</a></li>
            <li class="nav-item"><a class="nav-link" href="../php/reviews.php">Reviews</a></li>
            <li class="nav-item"><a class="nav-link" href="../php/winkelwagenpagina.php">winkelwagen</a></li>
            <li class="nav-item"><a class="nav-link" href="../php/admin.php">admin</a></li>


            <?php
            if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'){
                echo '<li class="nav-item"><a class="nav-link text-danger font-weight-bold" href="../php/admin.php">Admin paneel</a></li>';
            }
            ?>
        </ul>
    </div>
</nav>