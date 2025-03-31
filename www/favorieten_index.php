<?php
require 'database.php';
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['bezoeker_id'])) {
    header('Location: login.php');
    exit();
}

$bezoeker_id = $_SESSION['bezoeker_id'];

// Verwerk de zoekfilters (naam en collectienaam)
$search_name = isset($_GET['name']) ? '%' . $_GET['name'] . '%' : '%';
$search_collection = isset($_GET['collection']) ? '%' . $_GET['collection'] . '%' : '%';

// Verwerk het toevoegen van een collectie naam
if (isset($_POST['collectienaam'], $_POST['recept_code'])) {
    $collectienaam = $_POST['collectienaam'];
    $recept_code = $_POST['recept_code'];

    try {
        // Update de collectie naam voor het geselecteerde recept in de favorieten tabel
        $sql = "UPDATE favorieten 
                SET collectienaam = :collectienaam 
                WHERE bezoeker_id = :bezoeker_id AND recept_code = :recept_code";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':collectienaam' => $collectienaam,
            ':bezoeker_id' => $bezoeker_id,
            ':recept_code' => $recept_code
        ]);
        
        // Redirect naar de favorietenpagina na succesvolle update
        header('Location: favorieten_index.php?success=collectie_toegevoegd');
        exit();
    } catch (Exception $e) {
        echo "Fout bij het toevoegen van de collectie: " . $e->getMessage();
    }
}

try {
    // Zet foutmeldingen aan
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query voor het ophalen van favorieten en de bijbehorende recepten en collectienamen uit de favorieten tabel
    $sql = "SELECT r.recept_code, r.naam AS recept_naam, r.afbeelding, f.collectienaam
            FROM favorieten f
            LEFT JOIN recepten r ON f.recept_code = r.recept_code
            WHERE f.bezoeker_id = :bezoeker_id
            AND r.naam LIKE :search_name
            AND (f.collectienaam LIKE :search_collection OR f.collectienaam IS NULL)
            ORDER BY f.collectienaam, r.naam";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':bezoeker_id' => $bezoeker_id,
        ':search_name' => $search_name,
        ':search_collection' => $search_collection
    ]);
    $favorieten = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Groeperen op collectienaam (inclusief 'Geen collectie' voor recepten zonder collectienaam)
    $favorieten_groep = [];
    foreach ($favorieten as $favoriet) {
        $collectienaam = $favoriet['collectienaam'] ?: 'Geen collectie'; // Gebruik 'Geen collectie' als er geen collectie is
        $favorieten_groep[$collectienaam][] = $favoriet;
    }
} catch (Exception $e) {
    echo "Fout bij het ophalen van favorieten: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Favorieten</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <?php require 'header.php'; ?>

    <main class="container">
        <h1>Mijn Favorieten</h1>

        <!-- Filterformulier -->
        <form method="get" action="favorieten_index.php" class="filter-form">
            <input type="text" name="name" placeholder="Zoek op receptnaam" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>" class="filter-input">
            <input type="text" name="collection" placeholder="Zoek op collectienaam" value="<?php echo isset($_GET['collection']) ? htmlspecialchars($_GET['collection']) : ''; ?>" class="filter-input">
            <button type="submit" class="filter-button">Filter</button>
        </form>

        <!-- Favorieten weergeven per collectie -->
        <?php foreach ($favorieten_groep as $collectienaam => $recepten): ?>
            <section class="favorite-collection">
                <h2><?php echo htmlspecialchars($collectienaam); ?></h2>
                <div class="recipe-grid">
                    <?php foreach ($recepten as $favoriet): ?>
                        <div class="recipe-card">
                            <a href="recept_detail.php?id=<?php echo $favoriet['recept_code']; ?>">
                                <img src="uploads/<?php echo htmlspecialchars($favoriet['afbeelding']); ?>" alt="<?php echo htmlspecialchars($favoriet['recept_naam']); ?>" class="recipe-image">
                                <h3 class="recipe-link"><?php echo htmlspecialchars($favoriet['recept_naam']); ?></h3>
                            </a>

                            <!-- Formulier voor het toevoegen van een collectie naam -->
                            <form method="POST" action="favorieten_index.php" class="add-collection-form">
                                <input type="hidden" name="recept_code" value="<?php echo $favoriet['recept_code']; ?>">
                                <input type="text" name="collectienaam" placeholder="Voeg collectie toe" class="collection-input">
                                <button type="submit" class="add-collection-btn">Voeg toe aan collectie</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>

        <?php if (empty($favorieten)): ?>
            <p>Je hebt nog geen favorieten toegevoegd.</p>
        <?php endif; ?>
    </main>
</body>
</html>
