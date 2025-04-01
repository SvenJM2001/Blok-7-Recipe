<?php
require 'database.php';
session_start();

// Controleer of de gebruiker is ingelogd (of dat de bezoeker_id in de sessie bestaat)
if (!isset($_SESSION['bezoeker_id'])) {
    header("Location: login.php"); // Als de gebruiker niet is ingelogd, doorsturen naar de loginpagina
    exit();
}

// Haal bezoeker_id uit de sessie
$bezoeker_id = $_SESSION['bezoeker_id'];

// De rest van je code om gegevens van de bezoeker op te halen
try {
    // Haal gegevens van de bezoeker op uit de database
    $stmt = $conn->prepare("SELECT g.voornaam, g.tussenvoegsel, g.achternaam, g.gebruikersnaam, g.email, l.landnaam AS land, b.plaats, b.straat, b.huisnummer, b.postcode, b.landcode
                            FROM gebruikers g
                            JOIN bezoekers b ON g.gebruiker_id = b.gebruiker_id
                            JOIN landen l ON b.landcode = l.landcode
                            WHERE b.bezoeker_id = :bezoeker_id");

    $stmt->execute([':bezoeker_id' => $bezoeker_id]);
    $bezoeker = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bezoeker) {
        throw new Exception("Bezoeker niet gevonden.");
    }

    // Haal landen op voor de land select optie
    $stmtLanden = $conn->prepare("SELECT landcode, landnaam FROM landen");
    $stmtLanden->execute();
    $landen = $stmtLanden->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Gegevens Bewerken</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <?php require 'header.php'; ?>

    <main class="container">
        <h1>Gegevens Bewerken</h1>
        
        <form method="POST" action="bezoeker_edit_process.php">
            <div>
                <label for="voornaam">Voornaam:</label>
                <input type="text" id="voornaam" name="voornaam" value="<?php echo htmlspecialchars($bezoeker['voornaam']); ?>" required>
            </div>

            <div>
                <label for="tussenvoegsel">Tussenvoegsel:</label>
                <input type="text" id="tussenvoegsel" name="tussenvoegsel" value="<?php echo htmlspecialchars($bezoeker['tussenvoegsel']); ?>">
            </div>

            <div>
                <label for="achternaam">Achternaam:</label>
                <input type="text" id="achternaam" name="achternaam" value="<?php echo htmlspecialchars($bezoeker['achternaam']); ?>" required>
            </div>

            <div>
                <label for="gebruikersnaam">Gebruikersnaam:</label>
                <input type="text" id="gebruikersnaam" name="gebruikersnaam" value="<?php echo htmlspecialchars($bezoeker['gebruikersnaam']); ?>" required>
            </div>

            <div>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($bezoeker['email']); ?>" required>
            </div>

            <div>
                <label for="land">Land:</label>
                <select id="land" name="landcode" required>
                    <?php foreach ($landen as $land): ?>
                        <option value="<?php echo $land['landcode']; ?>" <?php echo $land['landcode'] == $bezoeker['landcode'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($land['landnaam']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="plaats">Plaats:</label>
                <input type="text" id="plaats" name="plaats" value="<?php echo htmlspecialchars($bezoeker['plaats']); ?>" required>
            </div>

            <div>
                <label for="straat">Straat:</label>
                <input type="text" id="straat" name="straat" value="<?php echo htmlspecialchars($bezoeker['straat']); ?>" required>
            </div>

            <div>
                <label for="huisnummer">Huisnummer:</label>
                <input type="text" id="huisnummer" name="huisnummer" value="<?php echo htmlspecialchars($bezoeker['huisnummer']); ?>" required>
            </div>

            <div>
                <label for="postcode">Postcode:</label>
                <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($bezoeker['postcode']); ?>" required>
            </div>

            <div>
                <label for="wachtwoord">Wachtwoord:</label>
                <input type="password" id="wachtwoord" name="wachtwoord" placeholder="Laat leeg om wachtwoord niet te wijzigen">
            </div>

            <div>
                <label for="wachtwoord_herhalen">Herhaal Wachtwoord:</label>
                <input type="password" id="wachtwoord_herhalen" name="wachtwoord_herhalen" placeholder="Herhaal het wachtwoord">
            </div>

            <button type="submit">Opslaan</button>
        </form>
    </main>
    <script src="script.js"></script>
</body>
</html>