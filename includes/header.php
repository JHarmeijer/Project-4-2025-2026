<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="../css/style.css">

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">

    <a class="navbar-brand d-flex align-items-center" href="../index.php">
        <img src="../assets/Chef's Choice Logo.png"
             alt="Chef's Choice Logo"
             class="navbar-logo">
    </a>

    <button class="navbar-toggler" type="button"
            data-toggle="collapse"
            data-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Menu openen">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

        <ul class="navbar-nav ml-auto">

            <li class="nav-item">
                <a class="nav-link" href="../php/index.php">Home</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../php/producten.php">Bestellen</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../php/reviews.php">Reviews</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../php/winkelwagenpagina.php">Winkelwagen</a>
            </li>

            <?php if (!isset($_SESSION['id'])): ?>

                <li class="nav-item">
                    <a class="nav-link" href="../php/registratie.php">Registreren</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="../php/inlog.php">Inloggen</a>
                </li>

            <?php else: ?>

                <li class="nav-item">
                    <a class="nav-link" href="../php/account.php">Account</a>
                </li>

            <?php endif; ?>

            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>

                <li class="nav-item">
                    <a class="nav-link text-danger font-weight-bold"
                       href="../php/admin.php">
                        Admin paneel
                    </a>
                </li>

            <?php endif; ?>

        </ul>

    </div>

</nav>