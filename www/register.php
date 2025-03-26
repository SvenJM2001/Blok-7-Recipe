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
    <title>Card Create</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <?php include 'header.php';?>

    <form action="register_process.php" method="post" enctype="multipart/form-data">
        <div>
            <h2></h2>
            <div>
                <label for="voornaam">Voornaam:</label>
                <input type="text" name="voornaam" id="voornaam" required>
            </div>
            <div>
                <label for="tussenvoegsel">Tussenvoegsel:</label>
                <input type="text" name="tussenvoegsel" id="tussenvoegsel" required>
            </div>
            <div>
                <label for="achternaam">Achternaam:</label>
                <input type="text" name="achternaam" id="achternaam" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div>
                <label for="gebruikersnaam">Gebruikersnaam:</label>
                <input type="text" name="gebruikersnaam" id="gebruikersnaam" required>
            </div>
            <div>
                <label for="wachtwoord">Wachtwoord:</label>
                <input type="password" name="wachtwoord" id="wachtwoord" required>
            </div>
            <div>
                <label for="rol">Rol:</label>
                <select name="rol" id="rol">
                    <option value="bezoeker">Bezoeker</option>
                    <option value="beheerder">beheerder</option>
                </select>
            </div>
        </div>
        <div>
            <h2>Adres</h2>
            <div>
                <label for="land">Land:</label>
                <select name="land" id="land">
                <?php foreach ($landen as $land): ?>
                    <option value="<?php echo $land['landnaam']; ?>" <?php echo isset($_GET['landnaam']) && $_GET['landnaam'] == $land['landnaam'] ? 'selected' : ''; ?>>
                        <?php echo ucfirst($land['landnaam']); ?>
                    </option>
                <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="plaats">Plaats:</label>
                <input type="text" name="plaats" id="plaats">
            </div>
            <div>
                <label for="straat">Straat:</label>
                <input type="text" name="straat" id="straat">
            </div>
            <div>
                <label for="huisnummer">Huisnummer:</label>
                <input type="number" name="huisnummer" id="huisnummer">
            </div>
            <div>
                <label for="postcode">Postcode:</label>
                <input type="text" name="postcode" id="postcode">
            </div>
        </div>
        <button type="submit">register</button>
    </form>
    
</body>
</html>