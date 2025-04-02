<?php
require 'database.php';
session_start();

// Haal de types op uit de maaltijdtypes-tabel
$query1 = "SELECT type FROM maaltijdtypes";
$stmt1 = $conn->prepare($query1);
$stmt1->execute();
$types = $stmt1->fetchAll(PDO::FETCH_ASSOC);

// Haal de ingredienten op gesorteerd op hun type
$query2 = "SELECT i.naam, it.type AS ingredient_type 
           FROM ingredienten i
           JOIN ingredienttypes it ON i.type_id = it.type_id
           ORDER BY it.type, i.naam"; 
$stmt2 = $conn->prepare($query2);
$stmt2->execute();
$ingredienten = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Groepeer de ingrediënten op type
$ingredientenPerType = [];
foreach ($ingredienten as $ingredient) {
    $ingredientenPerType[$ingredient['ingredient_type']][] = $ingredient['naam'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recept Toevoegen</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <?php include 'header.php';?>
    <main>
        <form action="recepten_add_process.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="naam">Naam:</label>
                <input type="text" name="naam" id="naam">
            </div>
            <div>
                <label for="type">Type:</label>
                <select name="type" id="type">
                <?php foreach ($types as $type): ?>
                    <option value="<?php echo $type['type']; ?>" <?php echo isset($_GET['type']) && $_GET['type'] == $type['type'] ? 'selected' : ''; ?>>
                        <?php echo ucfirst($type['type']); ?>
                    </option>
                <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Ingrediënten sectie gesorteerd per type -->
            <div>
                <fieldset>
                    <legend>Ingrediënten</legend>
                    <?php foreach ($ingredientenPerType as $ingredientType => $ingredientenLijst): ?>
                        <h3><?php echo ucfirst($ingredientType); ?></h3>
                        <?php foreach ($ingredientenLijst as $ingredient): ?>
                            <div>
                                <input type="checkbox" 
                                    id="ingredient_<?php echo $ingredient; ?>" 
                                    name="ingredienten[]" 
                                    value="<?php echo $ingredient; ?>"
                                    onchange="toggleIngredientFields('<?php echo $ingredient; ?>')">
                                <label for="ingredient_<?php echo $ingredient; ?>"><?php echo ucfirst($ingredient); ?></label>
                                
                                <div id="extraFields_<?php echo $ingredient; ?>" style="display: none; margin-left: 20px;">
                                    <label for="hoeveelheid_<?php echo $ingredient; ?>">Hoeveelheid:</label>
                                    <input type="number" name="hoeveelheid[<?php echo $ingredient; ?>]" id="hoeveelheid_<?php echo $ingredient; ?>" min="0" step="0.1">
                                    
                                    <label for="eenheid_<?php echo $ingredient; ?>">Eenheid:</label>
                                    <select name="eenheid[<?php echo $ingredient; ?>]" id="eenheid_<?php echo $ingredient; ?>">
                                        <option value="gram">gram</option>
                                        <option value="ml">ml</option>
                                        <option value="stuk">stuk</option>
                                        <option value="theelepel">theelepel</option>
                                        <option value="eetlepel">eetlepel</option>
                                        <option value="teen">teen</option>
                                        <option value="tenen">tenen</option>
                                        <option value="blokje">blokje</option>
                                        <option value="blokjes">blokjes</option>
                                        <option value="bol">bol</option>
                                        <option value="bollen">bollen</option>
                                        <option value="snufje">snufje</option>
                                        <option value="handje">handje</option>
                                    </select>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </fieldset>
            </div>
            
            <div>
                <label for="beschrijving">Beschrijving:</label>
                <input type="text" name="beschrijving" id="beschrijving">
            </div>
            <div>
                <label for="stappenplan">Stappenplan:</label>
                <input type="text" name="stappenplan" id="stappenplan">
            </div>
            <div>
                <label for="tijd">Tijdsduur:</label>
                <input type="time" name="tijd" id="tijd">
            </div>
            <div>
                <label for="graad">Moeilijkheidsgraad:</label>
                <select name="graad" id="graad">
                    <option value="Makkelijk">Makkelijk</option>
                    <option value="Gemiddeld">Gemiddeld</option>
                    <option value="Moeilijk">Moeilijk</option>
                </select>
            </div>
            <div>
                <label for="image">Afbeelding:</label>
                <input type="file" name="image" id="image">
            </div>
            <button type="submit">Toevoegen</button>
        </form>
    </main>
    <script src="script.js"></script>
</body>
</html>
