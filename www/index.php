<?php
session_start();
require 'database.php';


// Verkrijg de zoek- en filterwaarden uit de URL
$search = isset($_GET['search']) ? $_GET['search'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Basis SQL-query
$sql = "SELECT * FROM recepten WHERE 1";

// Voeg zoekfilter toe
if (!empty($search)) {
    $sql .= " AND naam LIKE :search";
}

// Voeg typefilter toe
if (!empty($type)) {
    $sql .= " AND type = :type";
}

$stmt = $conn->prepare($sql);

// Bind de zoekparameter
if (!empty($search)) {
    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
}

// Bind de typeparameter
if (!empty($type)) {
    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
}

$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokémon Verzameling</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <?php include 'header.php'; ?>


    <!-- Main Content -->
    <div>
        <!-- Hero Section -->
        <div>
            <div>
                <h1>Welkom bij mijn Pokémon Verzameling</h1>
                <p>Ontdek de wonderlijke wereld van Pokémon en bekijk mijn uitgebreide collectie!</p>
            </div>
        </div>

        
        <!-- Zoek en filter sectie -->
        <div>
            <div>
                <form method="GET" action="index.php">
                    <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Zoek op naam" class="p-2 border border-gray-300 rounded">
                    <select name="type" id="type">
                        <option value="">Alle types</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo $type['name'] == $type ? 'selected' : ''; ?>>
                                <?php echo ucfirst($type); // Capitalize the first letter of the type ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Zoeken</button>
                </form>
            </div>
        </div>

        <!-- Pokemon Grid -->
        <div>
            <h2>Mijn Favoriete Pokémon</h2>
            <div>
                <?php foreach ($cards as $card): ?>
                    <!-- Pokemon Card  -->
                    <div>
                        <img src="./uploads/<?php echo $card['image']?>" alt="<?php echo $card['name']?>">
                        <div class="p-6">
                            <h3><?php echo $card['name']?></h3>
                            <p><?php echo $card['description']?></p>
                            <a href="pokemon_detail.php?id=<?php echo $card['id']; ?>">Meer informatie →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div>
            <div>
                <h4>Over Ons</h4>
                <p>Wij zijn gepassioneerde Pokémon verzamelaars die onze liefde voor deze geweldige wezens willen delen met de wereld.</p>
            </div>
            <div>
                <h4>Snelle Links</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Verzameling</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <p>Email: info@pokemon-verzameling.nl</p>
                <p>Tel: +31 (0)6 12345678</p>
                <p>Locatie: Amsterdam, Nederland</p>
            </div>
        </div>
    </footer>
</body>

</html>
