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

// Verkrijg alle ingrediënten en hun types
$sql = "SELECT i.ingredient_id, i.naam, i.type_id, t.type 
        FROM ingredienten i
        LEFT JOIN ingredienttypes t ON i.type_id = t.type_id
        ORDER BY t.type, i.naam";
$stmt = $conn->prepare($sql);
$stmt->execute();
$alleIngredienten = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verkrijg de ingrediënten die al in het recept zitten met hoeveelheid en eenheid
$sql = "SELECT i.ingredient_id, i.naam, ri.hoeveelheid, ri.eenheid 
        FROM recepten_ingredienten ri
        JOIN ingredienten i ON ri.ingredient_id = i.ingredient_id
        WHERE ri.recept_code = :recept_code";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':recept_code', $receptCode, PDO::PARAM_INT);
$stmt->execute();
$geselecteerdeIngredienten = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Zet de geselecteerde ingrediënten in een array voor snelle lookup
$geselecteerdeIngredientenArray = [];
foreach ($geselecteerdeIngredienten as $ing) {
    $geselecteerdeIngredientenArray[$ing['ingredient_id']] = $ing;
}

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
                        <legend class="text-xl font-bold">Ingrediënten</legend>

                        <?php 
                        $huidigType = null;
                        foreach ($alleIngredienten as $ingredient): 
                            // Start een nieuwe sectie als het ingredienttype verandert
                            if ($huidigType !== $ingredient['type']) {
                                if ($huidigType !== null) {
                                    echo "</div>"; // Sluit vorige groep af
                                }
                                echo "<div class='mt-4'><h3 class='font-semibold text-lg'>{$ingredient['type']}</h3>";
                                $huidigType = $ingredient['type'];
                            }

                            $ingredientID = $ingredient['ingredient_id'];
                            $checked = isset($geselecteerdeIngredientenArray[$ingredientID]) ? 'checked' : '';
                            $hoeveelheid = $checked ? $geselecteerdeIngredientenArray[$ingredientID]['hoeveelheid'] : '';
                            $eenheid = $checked ? $geselecteerdeIngredientenArray[$ingredientID]['eenheid'] : '';
                        ?>

                            <div class="ml-4">
                                <input type="checkbox" 
                                    id="ingredient_<?php echo $ingredientID; ?>" 
                                    name="ingredienten[]" 
                                    value="<?php echo $ingredientID; ?>" 
                                    <?php echo $checked; ?> 
                                    onchange="toggleInputFields('<?php echo $ingredientID; ?>')">
                                <label for="ingredient_<?php echo $ingredientID; ?>"><?php echo ucfirst($ingredient['naam']); ?></label>
                                
                                <div id="fields_<?php echo $ingredientID; ?>" style="display: <?php echo $checked ? 'block' : 'none'; ?>;">
                                    <input type="number" name="hoeveelheid[<?php echo $ingredientID; ?>]" value="<?php echo $hoeveelheid; ?>" class="ml-2 p-1 border border-gray-300 rounded" placeholder="Hoeveelheid" step="any">
                                    <select name="eenheid[<?php echo $ingredientID; ?>]" class="ml-2 p-1 border border-gray-300 rounded">
                                        <option value="gram" <?php echo ($eenheid == 'gram') ? 'selected' : ''; ?>>gram</option>
                                        <option value="ml" <?php echo ($eenheid == 'ml') ? 'selected' : ''; ?>>ml</option>
                                        <option value="stuk" <?php echo ($eenheid == 'stuk') ? 'selected' : ''; ?>>stuk</option>
                                        <option value="theelepel" <?php echo ($eenheid == 'theelepel') ? 'selected' : ''; ?>>theelepel</option>
                                        <option value="eetlepel" <?php echo ($eenheid == 'eetlepel') ? 'selected' : ''; ?>>eetlepel</option>
                                        <option value="teen" <?php echo ($eenheid == 'teen') ? 'selected' : ''; ?>>teen</option>
                                        <option value="tenen" <?php echo ($eenheid == 'tenen') ? 'selected' : ''; ?>>tenen</option>
                                        <option value="blokje" <?php echo ($eenheid == 'blokje') ? 'selected' : ''; ?>>blokje</option>
                                        <option value="blokjes" <?php echo ($eenheid == 'blokjes') ? 'selected' : ''; ?>>blokjes</option>
                                        <option value="bol" <?php echo ($eenheid == 'bol') ? 'selected' : ''; ?>>bol</option>
                                        <option value="bollen" <?php echo ($eenheid == 'bollen') ? 'selected' : ''; ?>>bollen</option>
                                        <option value="snufje" <?php echo ($eenheid == 'snufje') ? 'selected' : ''; ?>>snufje</option>
                                        <option value="handje" <?php echo ($eenheid == 'handje') ? 'selected' : ''; ?>>handje</option>
                                    </select>
                                </div>
                            </div>

                        <?php endforeach; ?>
                        </div> <!-- Sluit laatste groep af -->
                    </fieldset>
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Opslaan</button>
            </form>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>
