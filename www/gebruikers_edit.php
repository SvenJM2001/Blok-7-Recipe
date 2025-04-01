<?php
require 'database.php';
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['gebruiker_id'])) {
    header("Location: login.php"); // Als de gebruiker niet is ingelogd, doorsturen naar de loginpagina
    exit();
}

// Verkrijg de gebruiker_id uit de URL
if (isset($_GET['id'])) {
    $gebruiker_id = $_GET['id'];
} else {
    // Als geen id is meegegeven, stuur dan de gebruiker naar de gebruikerslijst of een foutpagina
    echo "Geen gebruiker_id opgegeven.";
    exit();
}

// Haal de gegevens van de gebruiker op uit de database
try {
    // Haal gegevens van de gebruiker op uit de database, inclusief rol en adresgegevens
    $stmt = $conn->prepare("SELECT g.voornaam, g.tussenvoegsel, g.achternaam, g.gebruikersnaam, g.email, g.rol, l.landnaam AS land, 
                            b.plaats, b.straat, b.huisnummer, b.postcode, b.landcode
                            FROM gebruikers g
                            JOIN bezoekers b ON g.gebruiker_id = b.gebruiker_id
                            JOIN landen l ON b.landcode = l.landcode
                            WHERE g.gebruiker_id = :gebruiker_id");

    $stmt->execute([':gebruiker_id' => $gebruiker_id]);
    $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$gebruiker) {
        throw new Exception("Gebruiker niet gevonden.");
    }

    // Haal landen op voor de land select optie
    $stmtLanden = $conn->prepare("SELECT landcode, landnaam FROM landen");
    $stmtLanden->execute();
    $landen = $stmtLanden->fetchAll(PDO::FETCH_ASSOC);

    // Definieer de beschikbare rollen
    $rollen = ['beheerder' => 'Beheerder', 'bezoeker' => 'Bezoeker'];

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
        
        <form method="POST" action="gebruikers_edit_process.php?id=<?php echo $gebruiker_id; ?>" enctype="multipart/form-data">
            <div>
                <label for="voornaam">Voornaam:</label>
                <input type="text" id="voornaam" name="voornaam" value="<?php echo htmlspecialchars($gebruiker['voornaam']); ?>" required>
            </div>

            <div>
                <label for="tussenvoegsel">Tussenvoegsel:</label>
                <input type="text" id="tussenvoegsel" name="tussenvoegsel" value="<?php echo htmlspecialchars($gebruiker['tussenvoegsel']); ?>">
            </div>

            <div>
                <label for="achternaam">Achternaam:</label>
                <input type="text" id="achternaam" name="achternaam" value="<?php echo htmlspecialchars($gebruiker['achternaam']); ?>" required>
            </div>

            <div>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($gebruiker['email']); ?>" required>
            </div>

            <div>
                <label for="rol">Rol:</label>
                <select name="rol" id="rol" required>
                    <?php foreach ($rollen as $key => $rol): ?>
                        <option value="<?php echo $key; ?>" <?php echo $key == $gebruiker['rol'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($rol); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="land">Land:</label>
                <select id="land" name="landcode" required>
                    <?php foreach ($landen as $land): ?>
                        <option value="<?php echo $land['landcode']; ?>" <?php echo $land['landcode'] == $gebruiker['landcode'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($land['landnaam']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="plaats">Plaats:</label>
                <input type="text" id="plaats" name="plaats" value="<?php echo htmlspecialchars($gebruiker['plaats']); ?>" required>
            </div>

            <div>
                <label for="straat">Straat:</label>
                <input type="text" id="straat" name="straat" value="<?php echo htmlspecialchars($gebruiker['straat']); ?>" required>
            </div>

            <div>
                <label for="huisnummer">Huisnummer:</label>
                <input type="text" id="huisnummer" name="huisnummer" value="<?php echo htmlspecialchars($gebruiker['huisnummer']); ?>" required>
            </div>

            <div>
                <label for="postcode">Postcode:</label>
                <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($gebruiker['postcode']); ?>" required>
            </div>

            <button type="submit">Opslaan</button>
        </form>
    </main>
    <script src="script.js"></script>
</body>
</html>
