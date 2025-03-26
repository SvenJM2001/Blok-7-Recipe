<?php
require 'database.php';
session_start();

// Haal de types op uit de type-tabel
$query = "SELECT type FROM maaltijdtypes";
$stmt = $conn->prepare($query);
$stmt->execute();
$types = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Create</title>
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
            <div>
                <label for="tijd">Zeldzaamheid:</label>
                <select name="tijd" id="tijd">
                    <option value="common">common</option>
                    <option value="uncommon">uncommon</option>
                    <option value="rare">rare</option>
                    <option value="epic">epic</option>
                    <option value="legendary">legendary</option>
                </select>
            </div>
            <div>
                <label for="beschrijving">Beschrijving:</label>
                <input type="text" name="beschrijving" id="beschrijving">

            </div>
            <div>
                <label for="price">Prijs:</label>
                <input type="number" name="price" id="price">
            </div>
            <div>
                <label for="image">Afbeelding:</label>
                <input type="file" name="image" id="image">
            </div>
            <button type="submit">Toevoegen</button>
        </form>
    
    </main>
</body>
</html>