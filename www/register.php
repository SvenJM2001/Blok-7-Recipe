<?php
require 'database.php';
session_start();

// Haal de types op uit de type-tabel
$query = "SELECT landnaam FROM landen";
$stmt = $conn->prepare($query);
$stmt->execute();
$landen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <?php include 'header.php';?>

    <div class="login-container">
        <h2>Registreren</h2>
        <form action="register_process.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="voornaam">Voornaam:</label>
                <input type="text" name="voornaam" id="voornaam" required>
            </div>
            <div class="form-group">
                <label for="tussenvoegsel">Tussenvoegsel:</label>
                <input type="text" name="tussenvoegsel" id="tussenvoegsel">
            </div>
            <div class="form-group">
                <label for="achternaam">Achternaam:</label>
                <input type="text" name="achternaam" id="achternaam" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="gebruikersnaam">Gebruikersnaam:</label>
                <input type="text" name="gebruikersnaam" id="gebruikersnaam" required>
            </div>
            <div class="form-group">
                <label for="wachtwoord">Wachtwoord:</label>
                <input type="password" name="wachtwoord" id="wachtwoord" required>
            </div>
            <div class="form-group">
                <label for="rol">Rol:</label>
                <select name="rol" id="rol">
                    <option value="bezoeker">Bezoeker</option>
                    <option value="beheerder">Beheerder</option>
                </select>
            </div>
            <h2>Adres</h2>
            <div class="form-group">
                <label for="land">Land:</label>
                <select name="land" id="land">
                <?php foreach ($landen as $land): ?>
                    <option value="<?php echo $land['landnaam']; ?>">
                        <?php echo ucfirst($land['landnaam']); ?>
                    </option>
                <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="plaats">Plaats:</label>
                <input type="text" name="plaats" id="plaats">
            </div>
            <div class="form-group">
                <label for="straat">Straat:</label>
                <input type="text" name="straat" id="straat">
            </div>
            <div class="form-group">
                <label for="huisnummer">Huisnummer:</label>
                <input type="number" name="huisnummer" id="huisnummer">
            </div>
            <div class="form-group">
                <label for="postcode">Postcode:</label>
                <input type="text" name="postcode" id="postcode">
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
    </div>
</body>
</html>
