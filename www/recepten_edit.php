<?php
require 'database.php';
session_start();

// Haal de receptcode op uit de URL
if (isset($_GET['id'])) {
    $receptCode = $_GET['id'];
} else {
    header("Location: recepten_index.php");
    exit;
}

// Verkrijg het recept op basis van de receptcode
$sql = "SELECT * FROM recepten WHERE recept_code = :recept_code";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':recept_code', $receptCode, PDO::PARAM_INT);
$stmt->execute();
$recept = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recept) {
    echo "Recept niet gevonden.";
    exit;
}

// Verkrijg de maaltijdtypes voor het dropdown-menu
$sql = "SELECT * FROM maaltijdtypes";
$stmt = $conn->prepare($sql);
$stmt->execute();
$types = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verkrijg de huidige ingrediënten voor dit recept
$sql = "SELECT i.naam 
        FROM ingredienten i
        JOIN recepten_ingredienten ri ON i.ingredient_id = ri.ingredient_id
        WHERE ri.recept_code = :recept_code";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':recept_code', $receptCode, PDO::PARAM_INT);
$stmt->execute();
$huidigeIngrediënten = $stmt->fetchAll(PDO::FETCH_ASSOC);
$huidigeIngredientenArray = array_map(function($ingredient) {
    return $ingredient['naam'];
}, $huidigeIngrediënten);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recept Bewerken</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <?php require 'header.php'; ?>
    <main>
        <div class="container mx-auto p-6">
            <h2 class="text-2xl font-bold mb-4">Recept Bewerken</h2>

            <form method="POST" action="recepten_edit_process.php?id=<?php echo $recept['recept_code']; ?>" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="naam" class="block">Naam van het recept:</label>
                    <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($recept['naam']); ?>" class="p-2 border border-gray-300 rounded w-full">
                </div>

                <div>
                    <label for="beschrijving" class="block">Beschrijving:</label>
                    <textarea id="beschrijving" name="beschrijving" class="p-2 border border-gray-300 rounded w-full" rows="4"><?php echo htmlspecialchars($recept['beschrijving']); ?></textarea>
                </div>

                <div>
                    <label for="bereidingstijd" class="block">Bereidingstijd (in minuten):</label>
                    <input type="time" id="bereidingstijd" name="bereidingstijd" value="<?php echo htmlspecialchars($recept['bereidingstijd']); ?>" class="p-2 border border-gray-300 rounded w-full">
                </div>

                <div>
                    <label for="type_id" class="block">Maaltijdtype:</label>
                    <select name="type_id" id="type_id" class="p-2 border border-gray-300 rounded w-full">
                        <?php foreach ($types as $type): ?>
                            <option value="<?php echo $type['type_id']; ?>" <?php echo ($type['type_id'] == $recept['type_id']) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($type['type']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="stappenplan" class="block">Stappenplan:</label>
                    <textarea id="stappenplan" name="stappenplan" class="p-2 border border-gray-300 rounded w-full" rows="4"><?php echo htmlspecialchars($recept['stappenplan']); ?></textarea>
                </div>

                <div>
                    <fieldset>
                        <legend>Ingrediënten</legend>
                        <?php
                        // Verkrijg alle ingrediënten
                        $sql = "SELECT naam FROM ingredienten ORDER BY naam";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $ingredienten = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($ingredienten as $ingredient):
                            $checked = in_array($ingredient['naam'], $huidigeIngredientenArray) ? 'checked' : '';
                        ?>
                            <input type="checkbox" id="<?php echo $ingredient['naam']; ?>" name="ingredienten[]" value="<?php echo $ingredient['naam']; ?>" <?php echo $checked; ?>>
                            <label for="<?php echo $ingredient['naam']; ?>"><?php echo ucfirst($ingredient['naam']); ?></label><br>
                        <?php endforeach; ?>
                    </fieldset>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Opslaan</button>
            </form>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>
