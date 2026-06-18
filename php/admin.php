<?php
session_start();
include "../includes/header.php";
include "../includes/db_connect.php";

if ($_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$melding = "";
$melding_type = "ok";

if (isset($_GET['verwijder_product'])) {
    $product_ID = (int) $_GET['verwijder_product'];
    $stmt = $conn->prepare("DELETE FROM producten WHERE product_ID = ?");
    $stmt->bind_param("i", $product_ID);
    $stmt->execute();
    $stmt->close();
    $melding = "Product verwijderd.";
}

if (isset($_POST['product_toevoegen'])) {
    $naam     = trim($_POST['product_naam']);
    $prijs    = (float) $_POST['prijs'];
    $voorraad = (int) $_POST['voorraad'];
    $img      = trim($_POST['img']);

    if (empty($naam) || $prijs <= 0) {
        $melding = "Vul alle verplichte velden in.";
        $melding_type = "err";
    } else {
        $stmt = $conn->prepare("INSERT INTO producten (product_naam, prijs, voorraad, img) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdis", $naam, $prijs, $voorraad, $img);
        $stmt->execute();
        $stmt->close();
        $melding = "Product toegevoegd.";
    }
}

if (isset($_POST['voorraad_aanpassen'])) {
    $product_ID      = (int) $_POST['product_ID'];
    $nieuwe_voorraad = (int) $_POST['nieuwe_voorraad'];
    $stmt = $conn->prepare("UPDATE producten SET voorraad = ? WHERE product_ID = ?");
    $stmt->bind_param("ii", $nieuwe_voorraad, $product_ID);
    $stmt->execute();
    $stmt->close();
    $melding = "Voorraad bijgewerkt.";
}

if (isset($_GET['verwijder_klant'])) {
    $klant_ID = (int) $_GET['verwijder_klant'];
    if ($klant_ID === $_SESSION['gebruiker_id']) {
        $melding = "Je kunt jezelf niet verwijderen.";
        $melding_type = "err";
    } else {
        $stmt = $conn->prepare("DELETE FROM klant WHERE klant_ID = ?");
        $stmt->bind_param("i", $klant_ID);
        $stmt->execute();
        $stmt->close();
        $melding = "Klant verwijderd.";
    }
}

if (isset($_POST['rol_aanpassen'])) {
    $klant_ID   = (int) $_POST['klant_ID'];
    $nieuwe_rol = $_POST['nieuwe_rol'];
    if ($klant_ID === $_SESSION['gebruiker_id']) {
        $melding = "Je kunt je eigen rol niet aanpassen.";
        $melding_type = "err";
    } else {
        $stmt = $conn->prepare("UPDATE klant SET rol = ? WHERE klant_ID = ?");
        $stmt->bind_param("si", $nieuwe_rol, $klant_ID);
        $stmt->execute();
        $stmt->close();
        $melding = "Rol bijgewerkt.";
    }
}

// ==========================================
// COUPONS BEHEREN
// ==========================================

if (isset($_GET['verwijder_coupon'])) {
    $coupon_ID = (int) $_GET['verwijder_coupon'];
    $stmt = $conn->prepare("DELETE FROM coupons WHERE coupon_ID = ?");
    $stmt->bind_param("i", $coupon_ID);
    $stmt->execute();
    $stmt->close();
    $melding = "Coupon verwijderd.";
}

if (isset($_GET['toggle_coupon'])) {
    $coupon_ID = (int) $_GET['toggle_coupon'];
    $stmt = $conn->prepare("UPDATE coupons SET actief = NOT actief WHERE coupon_ID = ?");
    $stmt->bind_param("i", $coupon_ID);
    $stmt->execute();
    $stmt->close();
    $melding = "Coupon status gewijzigd.";
}

if (isset($_POST['coupon_toevoegen'])) {
    $coupon_code     = trim($_POST['coupon_code']);
    $korting_type    = $_POST['korting_type'];
    $kortings_waarde = (float) $_POST['kortings_waarde'];

    if (empty($coupon_code) || $kortings_waarde <= 0) {
        $melding = "Vul alle verplichte couponvelden in.";
        $melding_type = "err";
    } else {
        $stmt = $conn->prepare("INSERT INTO coupons (coupon_code, korting_type, kortings_waarde, actief) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("ssd", $coupon_code, $korting_type, $kortings_waarde);
        if ($stmt->execute()) {
            $melding = "Coupon toegevoegd.";
        } else {
            $melding = "Couponcode bestaat al, of er ging iets mis.";
            $melding_type = "err";
        }
        $stmt->close();
    }
}

if (isset($_POST['coupon_aanpassen'])) {
    $coupon_ID       = (int) $_POST['coupon_ID'];
    $coupon_code     = trim($_POST['coupon_code']);
    $korting_type    = $_POST['korting_type'];
    $kortings_waarde = (float) $_POST['kortings_waarde'];

    if (empty($coupon_code) || $kortings_waarde <= 0) {
        $melding = "Vul alle verplichte couponvelden in.";
        $melding_type = "err";
    } else {
        $stmt = $conn->prepare("UPDATE coupons SET coupon_code = ?, korting_type = ?, kortings_waarde = ? WHERE coupon_ID = ?");
        $stmt->bind_param("ssdi", $coupon_code, $korting_type, $kortings_waarde, $coupon_ID);
        if ($stmt->execute()) {
            $melding = "Coupon bijgewerkt.";
        } else {
            $melding = "Couponcode bestaat al, of er ging iets mis.";
            $melding_type = "err";
        }
        $stmt->close();
    }
}

// ==========================================
// BESTELLINGEN BEHEREN
// ==========================================

if (isset($_GET['verwijder_bestelling'])) {
    $bestelling_ID = (int) $_GET['verwijder_bestelling'];
    $stmt = $conn->prepare("DELETE FROM bestellingen WHERE bestelling_ID = ?");
    $stmt->bind_param("i", $bestelling_ID);
    $stmt->execute();
    $stmt->close();
    $melding = "Bestelling verwijderd.";
}

if (isset($_POST['bestelling_aanpassen'])) {
    $bestelling_ID = (int) $_POST['bestelling_ID'];
    $klant_ID_b    = (int) $_POST['klant_ID_b'];
    $product_ID_b  = (int) $_POST['product_ID_b'];
    $coupon_ID_b   = ($_POST['coupon_ID_b'] !== '') ? (int) $_POST['coupon_ID_b'] : null;
    $datum_aankoop = ($_POST['datum_aankoop'] !== '') ? $_POST['datum_aankoop'] : null;
    $groep_ID      = ($_POST['groep_ID'] !== '') ? (int) $_POST['groep_ID'] : null;

    $stmt = $conn->prepare("UPDATE bestellingen SET klant_ID = ?, product_ID = ?, coupon_ID = ?, datum_aankoop = ?, groep_ID = ? WHERE bestelling_ID = ?");
    $stmt->bind_param("iiisii", $klant_ID_b, $product_ID_b, $coupon_ID_b, $datum_aankoop, $groep_ID, $bestelling_ID);
    $stmt->execute();
    $stmt->close();
    $melding = "Bestelling bijgewerkt.";
}

$producten = $conn->query("
    SELECT *
    FROM producten
    ORDER BY product_ID ASC
");

$zoekterm = trim($_GET['zoek'] ?? '');

if (!empty($zoekterm)) {

    if (is_numeric($zoekterm)) {

        $stmt = $conn->prepare("
            SELECT klant_ID, klant_naam, email, rol, registratie_datum
            FROM klant
            WHERE klant_ID = ?
               OR klant_naam LIKE ?
               OR email LIKE ?
            ORDER BY klant_ID ASC
        ");

        $zoekID   = (int) $zoekterm;
        $zoekLike = "%{$zoekterm}%";

        $stmt->bind_param("iss", $zoekID, $zoekLike, $zoekLike);

    } else {

        $stmt = $conn->prepare("
            SELECT klant_ID, klant_naam, email, rol, registratie_datum
            FROM klant
            WHERE klant_naam LIKE ?
               OR email LIKE ?
            ORDER BY klant_ID ASC
        ");

        $zoekLike = "%{$zoekterm}%";

        $stmt->bind_param("ss", $zoekLike, $zoekLike);
    }

    $stmt->execute();
    $klanten = $stmt->get_result();
    $stmt->close();

} else {

    $klanten = $conn->query("
        SELECT klant_ID, klant_naam, email, rol, registratie_datum
        FROM klant
        ORDER BY klant_ID ASC
    ");
}

$coupons = $conn->query("
    SELECT *
    FROM coupons
    ORDER BY coupon_ID ASC
");

$bestellingen = $conn->query("
    SELECT b.*, k.klant_naam, p.product_naam
    FROM bestellingen b
    JOIN klant k ON b.klant_ID = k.klant_ID
    JOIN producten p ON b.product_ID = p.product_ID
    ORDER BY b.bestelling_ID DESC
");

$alle_klanten_lijst   = $conn->query("SELECT klant_ID, klant_naam FROM klant ORDER BY klant_naam ASC")->fetch_all(MYSQLI_ASSOC);
$alle_producten_lijst = $conn->query("SELECT product_ID, product_naam FROM producten ORDER BY product_naam ASC")->fetch_all(MYSQLI_ASSOC);
$alle_coupons_lijst   = $conn->query("SELECT coupon_ID, coupon_code FROM coupons ORDER BY coupon_code ASC")->fetch_all(MYSQLI_ASSOC);
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
</head>
<body>

<div class="pagina-inhoud" style="max-width: 1000px;">

    <!-- Header -->
    <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 24px;">
        <h2 class="sectie-titel" style="margin-bottom: 0; border: none;">Admin Panel</h2>
        <span style="font-size: 13px; color: #888;">
            Ingelogd als <strong style="color: #1a2236;"><?= htmlspecialchars($_SESSION['gebruiker_naam'] ?? 'Admin') ?></strong>
        </span>
    </div>

    <?php if (!empty($melding)): ?>
        <div class="melding-<?= $melding_type ?>">
            <?= htmlspecialchars($melding) ?>
        </div>
    <?php endif; ?>

    <!-- ==========================================
         PRODUCTEN BEHEREN
    ========================================== -->
    <div class="admin-kaart">
        <div class="admin-kaart-header">
            <h3>Producten beheren</h3>
        </div>
        <div class="admin-kaart-body">

            <div class="sub-label">Product toevoegen</div>
            <form method="POST">
                <div class="toevoeg-grid">
                    <div>
                        <label>Productnaam *</label>
                        <input type="text" name="product_naam" placeholder="bijv. een Chef's mes" required>
                    </div>
                    <div>
                        <label>Prijs (€) *</label>
                        <input type="number" name="prijs" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label>Voorraad</label>
                        <input type="number" name="voorraad" placeholder="0" min="0">
                    </div>
                    <div>
                        <label>Afbeelding URL</label>
                        <input type="text" name="img" placeholder="https://...">
                    </div>
                    <div>
                        <label>&nbsp;</label>
                        <button type="submit" name="product_toevoegen" class="btn-opslaan" style="padding: 8px 18px; font-size: 13px;">
                            + Toevoegen
                        </button>
                    </div>
                </div>
            </form>

            <div class="sub-label">Huidige producten</div>
            <table class="admin-tabel">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Naam</th>
                        <th>Prijs</th>
                        <th>Voorraad aanpassen</th>
                        <th>Verwijderen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $producten->fetch_assoc()): ?>
                        <tr>
                            <td class="id-cel">#<?= $product['product_ID'] ?></td>
                            <td style="font-weight: bold;"><?= htmlspecialchars($product['product_naam']) ?></td>
                            <td class="prijs-cel">€<?= number_format($product['prijs'], 2) ?></td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="product_ID" value="<?= $product['product_ID'] ?>">
                                    <input type="number" name="nieuwe_voorraad" value="<?= $product['voorraad'] ?>" min="0">
                                    <button type="submit" name="voorraad_aanpassen" class="btn-opslaan">Opslaan</button>
                                </form>
                            </td>
                            <td>
                                <a href="?verwijder_product=<?= $product['product_ID'] ?>"
                                   class="btn-verwijder"
                                   onclick="return confirm('Weet je zeker dat je dit product wilt verwijderen?')">
                                    ✕ Verwijder
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==========================================
         KLANTEN BEHEREN
    ========================================== -->
    <div class="admin-kaart">
        <div class="admin-kaart-header">
            <h3>Klanten beheren</h3>
        </div>
<div class="admin-kaart-body">

    <div class="sub-label">Zoek klant</div>

    <form method="GET" style="margin-bottom:20px;">
        <div style="display:flex; gap:10px; align-items:center;">
            <input
                type="text"
                name="zoek"
                placeholder="Zoek op naam, klantennummer of e-mail..."
                value="<?= htmlspecialchars($zoekterm) ?>"
                style="flex:1; padding:8px;"
            >

            <button type="submit" class="btn-opslaan">
                Zoeken
            </button>

            <?php if (!empty($zoekterm)): ?>
                <a href="admin.php" class="btn-verwijder">
                    Reset
                </a>
            <?php endif; ?>
        </div>
    </form>

    <p style="font-size:13px; color:#666; margin-bottom:15px;">
        <?= $klanten->num_rows ?> klant(en) gevonden
    </p>

    <table class="admin-tabel">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Naam</th>
                        <th>Email</th>
                        <th>Registratie</th>
                        <th>Rol aanpassen</th>
                        <th>Verwijderen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($klant = $klanten->fetch_assoc()): ?>
                        <tr>
                            <td class="id-cel">#<?= $klant['klant_ID'] ?></td>
                            <td style="font-weight: bold;"><?= htmlspecialchars($klant['klant_naam']) ?></td>
                            <td style="color: #555;"><?= htmlspecialchars($klant['email']) ?></td>
                            <td style="color: #888; font-size: 12px;"><?= $klant['registratie_datum'] ?? '—' ?></td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="klant_ID" value="<?= $klant['klant_ID'] ?>">
                                    <select name="nieuwe_rol">
                                        <option value="klant" <?= $klant['rol'] === 'klant' ? 'selected' : '' ?>>Klant</option>
                                        <option value="admin" <?= $klant['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button type="submit" name="rol_aanpassen" class="btn-opslaan">Opslaan</button>
                                </form>
                            </td>
                            <td>
                                <?php if ($klant['klant_ID'] !== $_SESSION['gebruiker_id']): ?>
                                    <a href="?verwijder_klant=<?= $klant['klant_ID'] ?>"
                                       class="btn-verwijder"
                                       onclick="return confirm('Weet je zeker dat je deze klant wilt verwijderen?')">
                                        ✕ Verwijder
                                    </a>
                                <?php else: ?>
                                    <span class="jij-badge">Dit ben jij</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==========================================
         COUPONS BEHEREN
    ========================================== -->
    <div class="admin-kaart">
        <div class="admin-kaart-header">
            <h3>Coupons beheren</h3>
        </div>
        <div class="admin-kaart-body">

            <div class="sub-label">Coupon toevoegen</div>
            <form method="POST">
                <div class="toevoeg-grid">
                    <div>
                        <label>Coupon code *</label>
                        <input type="text" name="coupon_code" placeholder="bijv. ZOMER10" required>
                    </div>
                    <div>
                        <label>Type *</label>
                        <select name="korting_type" required>
                            <option value="percentage">Percentage (%)</option>
                            <option value="vast_bedrag">Vast bedrag (€)</option>
                        </select>
                    </div>
                    <div>
                        <label>Waarde *</label>
                        <input type="number" name="kortings_waarde" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label>&nbsp;</label>
                        <button type="submit" name="coupon_toevoegen" class="btn-opslaan" style="padding: 8px 18px; font-size: 13px;">
                            + Toevoegen
                        </button>
                    </div>
                </div>
            </form>

            <div class="sub-label">Huidige coupons</div>
            <table class="admin-tabel">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Coupon bewerken</th>
                        <th>Status</th>
                        <th>Verwijderen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($coupon = $coupons->fetch_assoc()): ?>
                        <tr>
                            <td class="id-cel">#<?= $coupon['coupon_ID'] ?></td>
                            <td>
                                <form method="POST" class="inline-form" style="flex-wrap: wrap; gap: 6px;">
                                    <input type="hidden" name="coupon_ID" value="<?= $coupon['coupon_ID'] ?>">
                                    <input type="text" name="coupon_code" value="<?= htmlspecialchars($coupon['coupon_code']) ?>" style="width: 110px;">
                                    <select name="korting_type">
                                        <option value="percentage" <?= $coupon['korting_type'] === 'percentage' ? 'selected' : '' ?>>%</option>
                                        <option value="vast_bedrag" <?= $coupon['korting_type'] === 'vast_bedrag' ? 'selected' : '' ?>>€</option>
                                    </select>
                                    <input type="number" name="kortings_waarde" value="<?= $coupon['kortings_waarde'] ?>" step="0.01" min="0" style="width: 75px;">
                                    <button type="submit" name="coupon_aanpassen" class="btn-opslaan">Opslaan</button>
                                </form>
                            </td>
                            <td>
                                <?php if ($coupon['actief']): ?>
                                    <span style="padding:3px 8px; border-radius:4px; font-size:12px; font-weight:600; background:#d4edda; color:#155724;">Actief</span>
                                <?php else: ?>
                                    <span style="padding:3px 8px; border-radius:4px; font-size:12px; font-weight:600; background:#f1f1f1; color:#888;">Inactief</span>
                                <?php endif; ?>
                                <a href="?toggle_coupon=<?= $coupon['coupon_ID'] ?>" class="btn-opslaan" style="margin-left: 6px;">
                                    <?= $coupon['actief'] ? 'Deactiveer' : 'Activeer' ?>
                                </a>
                            </td>
                            <td>
                                <a href="?verwijder_coupon=<?= $coupon['coupon_ID'] ?>"
                                   class="btn-verwijder"
                                   onclick="return confirm('Weet je zeker dat je deze coupon wilt verwijderen?')">
                                    ✕ Verwijder
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==========================================
         BESTELLINGEN BEHEREN
    ========================================== -->
    <div class="admin-kaart">
        <div class="admin-kaart-header">
            <h3>Bestellingen beheren</h3>
        </div>
        <div class="admin-kaart-body">

            <table class="admin-tabel">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Klant</th>
                        <th>Product</th>
                        <th>Coupon</th>
                        <th>Datum</th>
                        <th>Groep ID</th>
                        <th>Verwijderen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($bestelling = $bestellingen->fetch_assoc()): ?>
                        <tr>
                            <td class="id-cel">#<?= $bestelling['bestelling_ID'] ?></td>
                            <td colspan="5">
                                <form method="POST" class="inline-form" style="flex-wrap: wrap; gap: 6px;">
                                    <input type="hidden" name="bestelling_ID" value="<?= $bestelling['bestelling_ID'] ?>">

                                    <select name="klant_ID_b">
                                        <?php foreach ($alle_klanten_lijst as $k): ?>
                                            <option value="<?= $k['klant_ID'] ?>" <?= $k['klant_ID'] == $bestelling['klant_ID'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($k['klant_naam']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <select name="product_ID_b">
                                        <?php foreach ($alle_producten_lijst as $p): ?>
                                            <option value="<?= $p['product_ID'] ?>" <?= $p['product_ID'] == $bestelling['product_ID'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($p['product_naam']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <select name="coupon_ID_b">
                                        <option value="">Geen coupon</option>
                                        <?php foreach ($alle_coupons_lijst as $c): ?>
                                            <option value="<?= $c['coupon_ID'] ?>" <?= $c['coupon_ID'] == $bestelling['coupon_ID'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($c['coupon_code']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <input type="date" name="datum_aankoop" value="<?= $bestelling['datum_aankoop'] ?>" style="width: 140px;">

                                    <input type="number" name="groep_ID" value="<?= htmlspecialchars($bestelling['groep_ID'] ?? '') ?>" placeholder="groep ID" style="width: 100px;">

                                    <button type="submit" name="bestelling_aanpassen" class="btn-opslaan">Opslaan</button>
                                </form>
                            </td>
                            <td>
                                <a href="?verwijder_bestelling=<?= $bestelling['bestelling_ID'] ?>"
                                   class="btn-verwijder"
                                   onclick="return confirm('Weet je zeker dat je deze bestelling wilt verwijderen?')">
                                    ✕ Verwijder
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>