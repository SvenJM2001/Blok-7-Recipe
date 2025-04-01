<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>

    <?php include 'header.php';?>

    <div class="login-container">
        <h2>Inloggen</h2>
        <form action="login_process.php" method="post">
            <div class="form-group">
                <label for="gebruikersnaam">Gebruikersnaam:</label>
                <input type="text" name="gebruikersnaam" id="gebruikersnaam" required>
            </div>
            <div class="form-group">
                <label for="wachtwoord">Wachtwoord:</label>
                <input type="password" name="wachtwoord" id="wachtwoord" required>
            </div>
            <button type="submit" class="btn">Inloggen</button>
        </form>
        <p>Heb je nog geen account? <a href="register.php">Registreer hier</a></p>
    </div>
    <script src="script.js"></script>
</body>
</html>
