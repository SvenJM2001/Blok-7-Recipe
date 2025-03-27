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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body class="bg-gray-100">
    <?php include 'header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Hero Sectie -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold">Welkom bij Lekker Koken</h1>
            <p class="text-gray-700 mt-2">Ontdek heerlijke recepten en experimenteer met smaken!</p>
        </div>

        <!-- Zoek- en filtersectie -->
        <div class="mb-6 bg-white p-4 shadow-md rounded-lg">
            <form method="GET" action="index.php" class="flex flex-wrap gap-2">
                <!-- Zoekveld -->
                <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Zoek op naam" class="p-2 border border-gray-300 rounded w-full sm:w-auto">

                <!-- Filter: Maaltijdtype -->
                <select name="type" class="p-2 border border-gray-300 rounded">
                    <option value="">Alle maaltijdtypes</option>
                    <?php foreach ($maaltijdtypes as $type): ?>
                        <option value="<?php echo $type['type_id']; ?>" <?php echo (isset($_GET['type']) && $_GET['type'] == $type['type_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['type']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Filter: Moeilijkheidsgraad -->
                <select name="moeilijkheid" class="p-2 border border-gray-300 rounded">
                    <option value="">Alle moeilijkheidsgraden</option>
                    <?php foreach ($moeilijkheidsgraden as $moeilijkheid): ?>
                        <option value="<?php echo $moeilijkheid['moeilijkheidsgraad']; ?>" <?php echo (isset($_GET['moeilijkheid']) && $_GET['moeilijkheid'] == $moeilijkheid['moeilijkheidsgraad']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($moeilijkheid['moeilijkheidsgraad']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
            </form>
        </div>

        <!-- Recepten Grid -->
        <h2 class="text-2xl font-semibold mb-4">Kies een lekker recept</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($recepten)): ?>
                <p class="col-span-full text-center text-gray-500">Geen recepten gevonden...</p>
            <?php else: ?>
                <?php foreach ($recepten as $recept): ?>
                    <!-- Recept Card -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <img src="./uploads/<?php echo htmlspecialchars($recept['afbeelding']); ?>" alt="<?php echo htmlspecialchars($recept['naam']); ?>" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-bold"><?php echo htmlspecialchars($recept['naam']); ?></h3>
                            <p class="text-gray-600">Bereidingstijd: <?php echo htmlspecialchars($recept['bereidingstijd']); ?> minuten</p>
                            <p class="text-gray-600">Moeilijkheid: <?php echo htmlspecialchars($recept['moeilijkheidsgraad']); ?></p>
                            <p class="text-gray-600">Type: <?php echo htmlspecialchars($recept['maaltijdtype']); ?></p>
                            <a href="recept_detail.php?id=<?php echo $recept['recept_code']; ?>" class="text-blue-500 mt-2 inline-block">Meer informatie →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
