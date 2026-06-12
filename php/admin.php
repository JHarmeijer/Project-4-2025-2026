<?php
session_start();
include "../includes/header.php";
include "../includes/db_connect.php";

// ✅ Toegangscontrole — alleen admins mogen hier komen
if (!isset($_SESSION['gebruiker_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$melding = "";

// ==========================================
// PRODUCTEN VERWIJDEREN
// ==========================================
if (isset($_GET['verwijder_product'])) {
    $product_ID = (int) $_GET['verwijder_product'];
    $stmt = $conn->prepare("DELETE FROM producten WHERE product_ID = ?");
    $stmt->bind_param("i", $product_ID);
    $stmt->execute();
    $stmt->close();
    $melding = "✅ Product verwijderd.";
}

// ==========================================
// PRODUCT TOEVOEGEN
// ==========================================
if (isset($_POST['product_toevoegen'])) {
    $naam     = trim($_POST['product_naam']);
    $prijs    = (float) $_POST['prijs'];
    $voorraad = (int) $_POST['voorraad'];
    $img      = trim($_POST['img']);

    if (empty($naam) || $prijs <= 0) {
        $melding = "❌ Vul alle verplichte velden in.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO producten (product_naam, prijs, voorraad, img) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("sdis", $naam, $prijs, $voorraad, $img);
        $stmt->execute();
        $stmt->close();
        $melding = "✅ Product toegevoegd.";
    }
}

// ==========================================
// VOORRAAD AANPASSEN
// ==========================================
if (isset($_POST['voorraad_aanpassen'])) {
    $product_ID      = (int) $_POST['product_ID'];
    $nieuwe_voorraad = (int) $_POST['nieuwe_voorraad'];

    $stmt = $conn->prepare("UPDATE producten SET voorraad = ? WHERE product_ID = ?");
    $stmt->bind_param("ii", $nieuwe_voorraad, $product_ID);
    $stmt->execute();
    $stmt->close();
    $melding = "✅ Voorraad bijgewerkt.";
}

// ==========================================
// KLANT VERWIJDEREN
// ==========================================
if (isset($_GET['verwijder_klant'])) {
    $klant_ID = (int) $_GET['verwijder_klant'];

    // Voorkom dat admin zichzelf verwijdert
    if ($klant_ID === $_SESSION['gebruiker_id']) {
        $melding = "❌ Je kunt jezelf niet verwijderen.";
    } else {
        $stmt = $conn->prepare("DELETE FROM klant WHERE klant_ID = ?");
        $stmt->bind_param("i", $klant_ID);
        $stmt->execute();
        $stmt->close();
        $melding = "✅ Klant verwijderd.";
    }
}

// ==========================================
// ROL AANPASSEN (admin / klant)
// ==========================================
if (isset($_POST['rol_aanpassen'])) {
    $klant_ID  = (int) $_POST['klant_ID'];
    $nieuwe_rol = $_POST['nieuwe_rol'];

    // Voorkom dat admin zichzelf degradeert
    if ($klant_ID === $_SESSION['gebruiker_id']) {
        $melding = "❌ Je kunt je eigen rol niet aanpassen.";
    } else {
        $stmt = $conn->prepare("UPDATE klant SET rol = ? WHERE klant_ID = ?");
        $stmt->bind_param("si", $nieuwe_rol, $klant_ID);
        $stmt->execute();
        $stmt->close();
        $melding = "✅ Rol bijgewerkt.";
    }
}

// ==========================================
// DATA OPHALEN
// ==========================================
$producten = $conn->query("SELECT * FROM producten");
$klanten   = $conn->query("SELECT klant_ID, klant_naam, email, rol, registratie_datum FROM klant");
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Admin Panel</h1>
    <p>Ingelogd als: <strong><?= htmlspecialchars($_SESSION['gebruiker_naam'] ?? 'Admin') ?></strong></p>

    <!-- MELDING -->
    <?php if (!empty($melding)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($melding) ?></div>
    <?php endif; ?>

    <!-- ==========================================
         PRODUCTEN BEHEREN
    ========================================== -->
    <div class="card mt-4">
        <div class="card-header">
            <h3>Producten beheren</h3>
        </div>
        <div class="card-body">

            <!-- PRODUCT TOEVOEGEN -->
            <h5>Product toevoegen</h5>
            <form method="POST" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="product_naam" class="form-control" 
                           placeholder="Productnaam" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="prijs" class="form-control" 
                           placeholder="Prijs" step="0.01" min="0" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="voorraad" class="form-control" 
                           placeholder="Voorraad" min="0">
                </div>
                <div class="col-md-3">
                    <input type="text" name="img" class="form-control" 
                           placeholder="Afbeelding URL">
                </div>
                <div class="col-md-2">
                    <button type="submit" name="product_toevoegen" class="btn btn-success w-100">
                        Toevoegen
                    </button>
                </div>
            </form>

            <!-- PRODUCTEN TABEL -->
            <h5>Huidige producten</h5>
            <table class="table table-bordered">
                <thead class="table-dark">
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
                            <td><?= $product['product_ID'] ?></td>
                            <td><?= htmlspecialchars($product['product_naam']) ?></td>
                            <td>€<?= number_format($product['prijs'], 2) ?></td>
                            <td>
                                <!-- VOORRAAD AANPASSEN -->
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="product_ID" 
                                           value="<?= $product['product_ID'] ?>">
                                    <input type="number" name="nieuwe_voorraad" 
                                           class="form-control form-control-sm" 
                                           value="<?= $product['voorraad'] ?>" min="0">
                                    <button type="submit" name="voorraad_aanpassen" 
                                            class="btn btn-sm btn-primary">
                                        Opslaan
                                    </button>
                                </form>
                            </td>
                            <td>
                                <a href="?verwijder_product=<?= $product['product_ID'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Weet je zeker dat je dit product wilt verwijderen?')">
                                    Verwijderen
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
    <div class="card mt-4 mb-5">
        <div class="card-header">
            <h3>Klanten beheren</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-dark">
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
                            <td><?= $klant['klant_ID'] ?></td>
                            <td><?= htmlspecialchars($klant['klant_naam']) ?></td>
                            <td><?= htmlspecialchars($klant['email']) ?></td>
                            <td><?= $klant['registratie_datum'] ?></td>
                            <td>
                                <!-- ROL AANPASSEN -->
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="klant_ID" 
                                           value="<?= $klant['klant_ID'] ?>">
                                    <select name="nieuwe_rol" class="form-select form-select-sm">
                                        <option value="klant" 
                                            <?= $klant['rol'] === 'klant' ? 'selected' : '' ?>>
                                            Klant
                                        </option>
                                        <option value="admin" 
                                            <?= $klant['rol'] === 'admin' ? 'selected' : '' ?>>
                                            Admin
                                        </option>
                                    </select>
                                    <button type="submit" name="rol_aanpassen" 
                                            class="btn btn-sm btn-primary">
                                        Opslaan
                                    </button>
                                </form>
                            </td>
                            <td>
                                <?php if ($klant['klant_ID'] !== $_SESSION['gebruiker_id']): ?>
                                    <a href="?verwijder_klant=<?= $klant['klant_ID'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Weet je zeker dat je deze klant wilt verwijderen?')">
                                        Verwijderen
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Dit ben jij</span>
                                <?php endif; ?>
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