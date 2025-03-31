<?php
require 'database.php';
session_start();

// Controleer of de bezoeker is ingelogd
if (!isset($_SESSION['bezoeker_id'])) {
    header("Location: login.php");
    exit();
}

$bezoeker_id = $_SESSION['bezoeker_id'];


try {
    // Haal de gegevens van de bezoeker op uit de 'gebruikers' en 'bezoekers' tabellen
    // Voeg een JOIN toe om het land op te halen uit de landen tabel
    $stmt = $conn->prepare("SELECT g.voornaam, g.tussenvoegsel, g.achternaam, g.gebruikersnaam, g.email, l.landnaam AS land, b.bezoeker_id, b.plaats, b.straat, b.huisnummer, b.postcode
                            FROM gebruikers g
                            JOIN bezoekers b ON g.gebruiker_id = b.gebruiker_id
                            JOIN landen l ON b.landcode = l.landcode
                            WHERE b.bezoeker_id = :bezoeker_id");

    $stmt->execute([':bezoeker_id' => $bezoeker_id]);
    $bezoeker = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$bezoeker) {
        throw new Exception("Bezoeker niet gevonden.");
    }
} catch (Exception $e) {
    echo "Fout bij ophalen gegevens: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Gegevens</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <?php require 'header.php'; ?>

    <main class="container">
        <h1>Mijn Gegevens</h1>
        
        <div class="bezoeker-gegevens">
            <p><strong>Naam:</strong> <?php echo htmlspecialchars($bezoeker['voornaam']) . ' ' . htmlspecialchars($bezoeker['tussenvoegsel']) . ' ' . htmlspecialchars($bezoeker['achternaam']); ?></p>
            <p><strong>Gebruikersnaam:</strong> <?php echo htmlspecialchars($bezoeker['gebruikersnaam']); ?></p>
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($bezoeker['email']); ?></p>

            <h2>Adres</h2>
            <p><strong>Land:</strong> <?php echo htmlspecialchars($bezoeker['land']); ?></p>
            <p><strong>Plaats:</strong> <?php echo htmlspecialchars($bezoeker['plaats']); ?></p>
            <p><strong>Straat:</strong> <?php echo htmlspecialchars($bezoeker['straat']); ?></p>
            <p><strong>Huisnummer:</strong> <?php echo htmlspecialchars($bezoeker['huisnummer']); ?></p>
            <p><strong>Postcode:</strong> <?php echo htmlspecialchars($bezoeker['postcode']); ?></p>
        </div>
        <!-- bewerking knop op bezoeker_detail.php -->
        <?php if (isset($_SESSION['bezoeker_id']) && $_SESSION['bezoeker_id'] == $bezoeker['bezoeker_id']): ?>
            <a href="bezoeker_edit.php" class="btn">Bewerk Gegevens</a>
        <?php endif; ?>

    </main>
</body>
</html>
