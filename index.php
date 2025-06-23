<?php
session_start();

$admin_wachtwoord = 'MevrouwVanDijk'; // het wachtwoord voor de admin login

// controleert of de gebruiker al is ingelogd als admin, als dat zo is, laat het admin menu zien
if (!isset($_SESSION['is_admin']) && isset($_POST['admin_login'])) {
    if ($_POST['password'] === $admin_wachtwoord) {
        $_SESSION['is_admin'] = true; // als het wachtwoord klopt, maak een sessie aan die het admin menu laat zien
        header("Location: index.php"); 
        exit();
    } else {
        $error = "Onjuist wachtwoord!";
    }
}

// defineer de dataverbinding
$host = 'localhost';
$dbname = 'bibliotheek';
$user = 'root';
$pass = '';

// maak de verbinding met de database doormiddel van PDO (PHP Data Objects)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass); // nieuwe instantie van PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // stel foutmodus in op uitzondering
} catch (PDOException $e) {
    die("Verbinding mislukt: " . $e->getMessage());
}

// controleert of de gebruiker een admin is, als dat zo is, kan de admin boeken toevoegen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toevoegen']) && isset($_SESSION['is_admin'])) {
    $titel = $_POST['titel'];
    $auteur = $_POST['auteur'];
    $stmt = $pdo->prepare("INSERT INTO boeken (titel, auteur, status, uitleendatum, terugbrengdatum) VALUES (?, ?, 'beschikbaar', NULL, NULL)"); // maak een nieuwe boek aan met de status 'beschikbaar' en zonder uitleendatum of terugbrengdatum 
    $stmt->execute([$titel, $auteur]); // voert de query uit met de opgegeven waarden (in dit geval de titel en auteur van het boek)
    header("Location: index.php");
    exit();
}

// laat de gebrruiker een boek uitlenen, laat de status van het boek veranderen naar 'uitgeleend' en voeg de uitleendatum en terugbrengdatum toe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uitleen'])) {
    $id = $_POST['id']; // haal het id van het boek op
    $terugbrengdatum = $_POST['terugbrengdatum'] ?? NULL;
    $stmt = $pdo->prepare("UPDATE boeken SET status = 'uitgeleend', uitleendatum = NOW(), terugbrengdatum = ? WHERE id = ?"); // maak de status van het boek  'uitgeleend' en voeg de uitleendatum en terugbrengdatum toe
    $stmt->execute([$terugbrengdatum, $id]);
    header("Location: index.php");
    exit();
}

// laat de gebruiker een boek terugbrengen, laat de status van het boek veranderen naar 'beschikbaar' en verwijder de uitleendatum en terugbrengdatum
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['terugbrengen'])) {
    $id = $_POST['id']; // haal het id van het boek op
    $stmt = $pdo->prepare("UPDATE boeken SET status = 'beschikbaar', uitleendatum = NULL, terugbrengdatum = NULL WHERE id = ?"); // maak de status van het boek 'beschikbaar' en verwijder de uitleendatum en terugbrengdatum
    $stmt->execute([$id]);
    header("Location: index.php");
    exit();
}

// controleert of de gebruiker een admin is, als dat zo is, kan de admin boeken verwijderen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verwijderen']) && isset($_SESSION['is_admin'])) {
    $id = $_POST['id']; // haal het id van het boek op
    $stmt = $pdo->prepare("DELETE FROM boeken WHERE id = ?"); // verwijder het boek met het opgegeven id
    $stmt->execute([$id]);
    header("Location: index.php");
    exit();
}

// haalt alle boeken op uit de database
$stmt = $pdo->query("SELECT * FROM boeken");
$boeken = $stmt->fetchAll(PDO::FETCH_ASSOC); // slaat de boeken op in een array
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bibliotheek</title>
    <style>
        body {
            background-color: #E1CA96;
        }
        .right-side {
            background-color: #ACA885;
            border: 1px solid black;
            border-radius: 15px;
            position: absolute;
            right: 10px;
            padding: 15px;
            margin: 10px;
        }
    </style>
</head>
<body>
    <h1>Bibliotheek van Dijk</h1>
    <h2>Wat wil je vandaag doen?</h2>
    
    <?php if (!isset($_SESSION['is_admin'])): ?>
        <div class="right-side">
        <h2>Admin Login</h2>
        <form method="post">
            <input type="password" name="password" placeholder="Wachtwoord" required>
            <button type="submit" name="admin_login">Inloggen</button>
        </form>
    </div>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?> <!-- als het wachtwoord onjuist is, laat het een foutmelding zien -->
    <?php else: ?>
        <div class="right-side">
        <h2>Boek Toevoegen</h2>
        <form method="post">
            <input type="text" name="titel" placeholder="Boektitel" required>
            <input type="text" name="auteur" placeholder="Auteur" required>
            <button type="submit" name="toevoegen">Boek Toevoegen</button>
        </form>
    </div>
    <?php endif; ?>

    <h2>Beschikbare boeken</h2>
    <ul>
        <?php foreach ($boeken as $boek): ?> <!-- loopt door de boeken heen en laat ze zien -->
            <li>
                <?= htmlspecialchars($boek['titel']) ?> - <?= htmlspecialchars($boek['auteur']) ?> <!-- laat de titel en auteur zien -->
                (<?= htmlspecialchars($boek['status']) ?>) <!-- laat de status van het boek zien, bijvoorbeeld 'beschikbaar' of 'uitgeleend'-->
                <?php if ($boek['status'] == 'uitgeleend'): ?>
                    - Terugbrengen op: <?= htmlspecialchars($boek['terugbrengdatum']) ?> <!-- als het boek uitgeleend is, laat dan de terugbrengdatum zien -->
                <?php endif; ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $boek['id'] ?>">
                    <?php if ($boek['status'] == 'beschikbaar'): ?>
                        <input type="date" name="terugbrengdatum">
                        <button type="submit" name="uitleen">Uitleen</button>
                    <?php else: ?>
                        <button type="submit" name="terugbrengen">Terugbrengen</button>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['is_admin'])): ?>
                        <button type="submit" name="verwijderen">Verwijder</button>
                    <?php endif; ?>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
