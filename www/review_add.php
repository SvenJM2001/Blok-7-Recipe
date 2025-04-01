<?php
include 'database.php';
session_start();

if (!isset($_GET['id'])) {
    die("Geen recept geselecteerd.");
}

$recept_code = $_GET['id'];

// Haal het recept op
$sql = "SELECT * FROM recepten WHERE recept_code = :recept_code";
$stmt = $conn->prepare($sql);
$stmt->execute([':recept_code' => $recept_code]);
$recept = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recept) {
    die("Recept niet gevonden.");
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review voor <?php echo htmlspecialchars($recept['naam']); ?></title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <?php require 'header.php'; ?>
    <main class="container">
        <div class="review-form">
            <h1>Voeg een Review toe voor "<?php echo htmlspecialchars($recept['naam']); ?>"</h1>

            <form method="POST" action="review_add_process.php">
                <input type="hidden" name="recept_code" value="<?php echo $recept_code; ?>">

                <div class="form-group">
                    <label for="rating">Beoordeling:</label>
                    <select name="rating" id="rating" required>
                        <option value="1">1 Ster</option>
                        <option value="2">2 Sterren</option>
                        <option value="3">3 Sterren</option>
                        <option value="4">4 Sterren</option>
                        <option value="5">5 Sterren</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tekst">Review Tekst:</label>
                    <textarea name="tekst" id="tekst" rows="5" placeholder="Schrijf hier je review..." required></textarea>
                </div>

                <button type="submit" class="submit-btn">Review toevoegen</button>
            </form>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>
