<?php
session_start();
include "../includes/header.php";
include "../includes/db_connect.php";

if (!isset($_SESSION['gebruiker_id'])) {
    header("Location: inlog.php");
    exit;
}

$klantID = (int) $_SESSION['gebruiker_id'];
$fout    = '';

/* =========================
   KLANT UPDATE
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {

    $naam     = trim($_POST['naam']);
    $email    = trim($_POST['email']);
    $telefoon = trim($_POST['telefoon']);
    $adres    = trim($_POST['adres']);

    if ($naam === '' || $email === '') {
        $fout = 'Naam en e-mailadres zijn verplicht.';
    } else {
        $stmt = $conn->prepare("
            UPDATE klant
            SET klant_naam = ?, email = ?, telefoon_nummer = ?, adres = ?
            WHERE klant_ID = ?
        ");
        $stmt->bind_param("ssssi", $naam, $email, $telefoon, $adres, $klantID);
        $stmt->execute();

        header("Location: account.php?success=1");
        exit;
    }
}

/* =========================
   KLANT OPHALEN
========================= */

$stmt = $conn->prepare("SELECT * FROM klant WHERE klant_ID = ?");
$stmt->bind_param("i", $klantID);
$stmt->execute();
$klant = $stmt->get_result()->fetch_assoc();

if (!$klant) {
    die("Gebruiker niet gevonden.");
}

/* =========================
   BESTELLINGEN
========================= */

$stmtOrders = $conn->prepare("
    SELECT
        b.bestelling_ID,
        b.datum_aankoop,
        p.product_naam,
        p.prijs,
        p.img
    FROM bestellingen b
    INNER JOIN producten p ON b.product_ID = p.product_ID
    WHERE b.klant_ID = ?
    ORDER BY b.datum_aankoop DESC
");
$stmtOrders->bind_param("i", $klantID);
$stmtOrders->execute();
$bestellingen = $stmtOrders->get_result();

/* =========================
   REVIEWS (op naam)
========================= */

$stmtReviews = $conn->prepare("
    SELECT r.*, p.product_naam
    FROM reviews r
    INNER JOIN producten p ON r.product_ID = p.product_ID
    WHERE r.naam = ?
    ORDER BY r.datum DESC
");
$stmtReviews->bind_param("s", $klant['klant_naam']);
$stmtReviews->execute();
$reviews = $stmtReviews->get_result();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">

    <title>Mijn Account</title>
</head>
<body>

<div class="pagina-inhoud">

    <?php if (isset($_GET['success'])): ?>
        <div class="melding-ok">Gegevens succesvol opgeslagen.</div>
    <?php endif; ?>

    <?php if ($fout !== ''): ?>
        <div class="melding-err"><?= htmlspecialchars($fout) ?></div>
    <?php endif; ?>

    <!-- ================= PROFIEL ================= -->
    <div class="admin-kaart" id="profiel">
        <div class="admin-kaart-header">
            <h3>👤 Mijn Account</h3>
        </div>
        <div class="admin-kaart-body">
            <p><b>Naam:</b> <?= htmlspecialchars($klant['klant_naam']) ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($klant['email']) ?></p>
            <p><b>Telefoon:</b> <?= htmlspecialchars($klant['telefoon_nummer'] ?? 'Niet ingevuld') ?></p>
            <p><b>Adres:</b> <?= htmlspecialchars($klant['adres'] ?? 'Niet ingevuld') ?></p>
        </div>
    </div>

    <!-- ================= BESTELLINGEN ================= -->
    <div class="admin-kaart" id="bestellingen">
        <div class="admin-kaart-header">
            <h3>Bestellingen</h3>
        </div>
        <div class="admin-kaart-body">

            <?php if ($bestellingen->num_rows > 0): ?>
                <table class="admin-tabel">
                    <tr>
                        <th>Product</th>
                        <th>Prijs</th>
                        <th>Datum</th>
                    </tr>
                    <?php while ($b = $bestellingen->fetch_assoc()): ?>
                        <tr>
                            <td class="bestelling-product-cel">
                                <?php if (!empty($b['img'])): ?>
                                    <img src="<?= htmlspecialchars($b['img']) ?>"
                                         alt="<?= htmlspecialchars($b['product_naam']) ?>"
                                         class="bestelling-thumb">
                                <?php endif; ?>
                                <span><?= htmlspecialchars($b['product_naam']) ?></span>
                            </td>
                            <td class="prijs-cel">
                                €<?= number_format($b['prijs'], 2, ',', '.') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($b['datum_aankoop']) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>Je hebt nog geen bestellingen geplaatst.</p>
            <?php endif; ?>

        </div>
    </div>

    <!-- ================= GEGEVENS AANPASSEN ================= -->
    <div class="admin-kaart" id="gegevens">
        <div class="admin-kaart-header">
            <h3>Gegevens aanpassen</h3>
        </div>
        <div class="admin-kaart-body">

            <form method="POST">
                <label>Naam</label>
                <input type="text" name="naam" value="<?= htmlspecialchars($klant['klant_naam']) ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($klant['email']) ?>" required>

                <label>Telefoon</label>
                <input type="text" name="telefoon" value="<?= htmlspecialchars($klant['telefoon_nummer'] ?? '') ?>">

                <label>Adres</label>
                <input type="text" name="adres" value="<?= htmlspecialchars($klant['adres'] ?? '') ?>">

                <button type="submit" name="update_account">Opslaan</button>
            </form>

        </div>
    </div>

    <!-- ================= MIJN REVIEWS ================= -->
    <div class="admin-kaart" id="reviews">
        <div class="admin-kaart-header">
            <h3>Mijn Reviews</h3>
        </div>
        <div class="admin-kaart-body">

            <?php if ($reviews->num_rows > 0): ?>
                <div id="account-review-lijst">
                    <?php while ($r = $reviews->fetch_assoc()): ?>
                        <div class="review-kaart">
                            <div class="product-naam"><?= htmlspecialchars($r['product_naam']) ?></div>
                            <div class="review-sterren"><?= str_repeat("⭐", (int) $r['beoordeling']) ?></div>
                            <div class="tekst"><?= htmlspecialchars($r['tekst']) ?></div>
                            <div class="datum"><?= htmlspecialchars($r['datum']) ?></div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>Je hebt nog geen reviews geplaatst.</p>
            <?php endif; ?>

        </div>
    </div>

</div>

</body>
</html>