<?php
require "database.php";
session_start();

// Haal maaltijdtypes en moeilijkheidsgraden op
try {
    $query1 = "SELECT type_id, type FROM maaltijdtypes";
    $stmt1 = $conn->prepare($query1);
    $stmt1->execute();
    $maaltijdtypes = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    $query2 = "SELECT DISTINCT moeilijkheidsgraad FROM recepten";
    $stmt2 = $conn->prepare($query2);
    $stmt2->execute();
    $moeilijkheidsgraden = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Fout bij ophalen filters: " . $e->getMessage();
}

// Zoek en filter functionaliteit
try {
    $query = "SELECT recepten.*, maaltijdtypes.type AS maaltijdtype 
              FROM recepten
              LEFT JOIN maaltijdtypes ON recepten.type_id = maaltijdtypes.type_id";

    $conditions = [];
    $params = [];

    if (!empty($_GET['search'])) {
        $conditions[] = "recepten.naam LIKE :search";
        $params[':search'] = "%" . $_GET['search'] . "%";
    }

    if (!empty($_GET['type'])) {
        $conditions[] = "recepten.type_id = :type";
        $params[':type'] = $_GET['type'];
    }

    if (!empty($_GET['moeilijkheid'])) {
        $conditions[] = "recepten.moeilijkheidsgraad = :moeilijkheid";
        $params[':moeilijkheid'] = $_GET['moeilijkheid'];
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $recepten = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Fout bij ophalen recepten: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekker Koken - Recepten</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <!-- Hero Sectie -->
        <div class="hero">
            <h1 class="hero-title">Welkom bij Lekker Koken</h1>
            <p class="hero-text">Ontdek heerlijke recepten en experimenteer met smaken!</p>
        </div>

        <!-- Zoek- en filtersectie -->
        <div class="filters">
            <form method="GET" action="index.php" class="filter-form">
                <!-- Zoekveld -->
                <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Zoek op naam" class="filter-input">

                <!-- Filter: Maaltijdtype -->
                <select name="type" class="filter-select">
                    <option value="">Alle maaltijdtypes</option>
                    <?php foreach ($maaltijdtypes as $type): ?>
                        <option value="<?php echo $type['type_id']; ?>" <?php echo (isset($_GET['type']) && $_GET['type'] == $type['type_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['type']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Filter: Moeilijkheidsgraad -->
                <select name="moeilijkheid" class="filter-select">
                    <option value="">Alle moeilijkheidsgraden</option>
                    <?php foreach ($moeilijkheidsgraden as $moeilijkheid): ?>
                        <option value="<?php echo $moeilijkheid['moeilijkheidsgraad']; ?>" <?php echo (isset($_GET['moeilijkheid']) && $_GET['moeilijkheid'] == $moeilijkheid['moeilijkheidsgraad']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($moeilijkheid['moeilijkheidsgraad']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="filter-button">Filter</button>
            </form>
        </div>

        <!-- Recepten Grid -->
        <h2 class="recipe-title">Kies een lekker recept</h2>
        <div class="recipes-grid">
            <?php if (empty($recepten)): ?>
                <p class="no-recipes">Geen recepten gevonden...</p>
            <?php else: ?>
                <?php foreach ($recepten as $recept): ?>
                    <!-- Recept Card -->
                    <div class="recipe-card">
                        <img src="./uploads/<?php echo htmlspecialchars($recept['afbeelding']); ?>" alt="<?php echo htmlspecialchars($recept['naam']); ?>" class="recipe-img">
                        <div class="recipe-info">
                            <h3 class="recipe-name"><?php echo htmlspecialchars($recept['naam']); ?></h3>
                            <p class="recipe-details">Bereidingstijd: <?php echo htmlspecialchars($recept['bereidingstijd']); ?> minuten</p>
                            <p class="recipe-details">Moeilijkheid: <?php echo htmlspecialchars($recept['moeilijkheidsgraad']); ?></p>
                            <p class="recipe-details">Type: <?php echo htmlspecialchars($recept['maaltijdtype']); ?></p>
                            <a href="recepten_detail.php?id=<?php echo $recept['recept_code']; ?>" class="recipe-link">Meer informatie →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
