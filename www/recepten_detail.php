<?php
include 'database.php';
session_start();

if (!isset($_GET['id'])) {
    die("Geen recept geselecteerd.");
}

$recept_code = $_GET['id'];

// Haal de receptgegevens op
$sql = "SELECT recepten.*, maaltijdtypes.type AS maaltijdtype 
        FROM recepten 
        LEFT JOIN maaltijdtypes ON recepten.type_id = maaltijdtypes.type_id
        WHERE recepten.recept_code = :recept_code";

$stmt = $conn->prepare($sql);
$stmt->execute([':recept_code' => $recept_code]);
$recept = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recept) {
    die("Recept niet gevonden.");
}

// Haal ingrediënten op
$sql = "SELECT i.naam FROM recepten_ingredienten ri
        JOIN ingredienten i ON ri.ingredient_id = i.ingredient_id
        WHERE ri.recept_code = :recept_code";

$stmt = $conn->prepare($sql);
$stmt->execute([':recept_code' => $recept_code]);
$ingredienten = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Haal gemiddelde beoordeling op
$sql = "SELECT AVG(rating) as gemiddelde_rating FROM reviews WHERE recept_code = :recept_code";
$stmt = $conn->prepare($sql);
$stmt->execute([':recept_code' => $recept_code]);
$rating = $stmt->fetch(PDO::FETCH_ASSOC)['gemiddelde_rating'];

// Stel een standaardwaarde in als er geen beoordelingen zijn
if ($rating === null) {
    $rating = 0;  // Of een andere standaardwaarde, bijvoorbeeld 0.0
} else {
    $rating = round($rating, 1); // Afronden op 1 decimaal
}

// Haal de reviews op
$sql = "SELECT r.rating, r.tekst, u.gebruikersnaam AS bezoeker_naam
        FROM reviews r
        LEFT JOIN bezoekers b ON r.bezoeker_id = b.bezoeker_id
        LEFT JOIN gebruikers u ON b.gebruiker_id = u.gebruiker_id
        WHERE r.recept_code = :recept_code
        ORDER BY r.review_id DESC"; 

$stmt = $conn->prepare($sql);
$stmt->execute([':recept_code' => $recept_code]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Controleer of het recept favoriet is voor de gebruiker
$is_favorite = false;
if (isset($_SESSION['bezoeker_id'])) {  // Verander user_id naar bezoeker_id
    $sql = "SELECT * FROM favorieten WHERE bezoeker_id = :bezoeker_id AND recept_code = :recept_code";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':bezoeker_id' => $_SESSION['bezoeker_id'],  // Zorg ervoor dat de juiste sessievariabele wordt gebruikt
        ':recept_code' => $recept_code
    ]);
    $is_favorite = $stmt->fetch() ? true : false;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recept['naam']); ?></title>
    <link rel="stylesheet" href="stylesheet.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php require 'header.php'; ?>
    <main class="container">
        <div class="recipe-detail">
            <h1><?php echo htmlspecialchars($recept['naam']); ?></h1>
            <img src="./uploads/<?php echo htmlspecialchars($recept['afbeelding']); ?>" alt="<?php echo htmlspecialchars($recept['naam']); ?>" class="recipe-img">
            
            <div class="recipe-meta">
                <p><strong>Maaltijdtype:</strong> <?php echo htmlspecialchars($recept['maaltijdtype']); ?></p>
                <p><strong>Moeilijkheidsgraad:</strong> <?php echo htmlspecialchars($recept['moeilijkheidsgraad']); ?></p>
                <p><strong>Bereidingstijd:</strong> <?php echo htmlspecialchars($recept['bereidingstijd']); ?> minuten</p>
            </div>

            <h2>Beschrijving</h2>
            <p><?php echo nl2br(htmlspecialchars($recept['beschrijving'])); ?></p>

            <h2>Ingrediënten</h2>
            <ul>
                <?php foreach ($ingredienten as $ingredient): ?>
                    <li><?php echo htmlspecialchars($ingredient['naam']); ?></li>
                <?php endforeach; ?>
            </ul>

            <h2>Stappenplan</h2>
            <p><?php echo nl2br(htmlspecialchars($recept['stappenplan'])); ?></p>

            <h2>Gemiddelde Beoordeling</h2>
            <div class="rating">
                <?php
                $fullStars = floor($rating);
                $halfStar = ($rating - $fullStars >= 0.5) ? 1 : 0;
                $emptyStars = 5 - ($fullStars + $halfStar);
                
                for ($i = 0; $i < $fullStars; $i++) echo '<span class="star">⭐</span>';;
                if ($halfStar) echo '<span class="star-half">⭐½</span>';
                for ($i = 0; $i < $emptyStars; $i++) echo '<span class="star-empty">☆</span>';
                ?>
                <span>(<?php echo $rating; ?> / 5)</span>
            </div>

            <div class="recipe-actions">
                <?php if (isset($_SESSION['bezoeker_id'])): ?>
                    <form method="POST" action="favorieten_toggle.php">
                        <input type="hidden" name="recept_code" value="<?php echo $recept_code; ?>">
                        <button type="submit" class="favorite-btn">
                            <?php echo $is_favorite ? "❤️ Verwijder uit favorieten" : "🤍 Voeg toe aan favorieten"; ?>
                        </button>
                    </form>
                <?php else: ?>
                    <p>Log in om dit recept aan je favorieten toe te voegen.</p>
                <?php endif; ?>

                <a href="review_add.php?id=<?php echo $recept_code; ?>" class="review-btn">Schrijf een review</a>
            </div>
        </div>

        <div class="reviews">
            <!-- Reviews Sectie -->
            <div class="reviews-section">
                <h2>Reviews</h2>
                <?php if (count($reviews) > 0): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review">
                            <div class="review-header">
                                <strong><?php echo htmlspecialchars($review['bezoeker_naam']); ?></strong>
                                <div class="review-rating">
                                    <?php
                                    $fullStars = floor($review['rating']);
                                    $halfStar = ($review['rating'] - $fullStars >= 0.5) ? 1 : 0;
                                    $emptyStars = 5 - ($fullStars + $halfStar);
                                    
                                    for ($i = 0; $i < $fullStars; $i++) {
                                        echo '<span class="star">⭐</span>';
                                    }
                                    if ($halfStar) {
                                        echo '<span class="star-half">⭐½</span>';
                                    }
                                    for ($i = 0; $i < $emptyStars; $i++) {
                                        echo '<span class="star-empty">☆</span>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($review['tekst'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Er zijn nog geen reviews voor dit recept.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>
