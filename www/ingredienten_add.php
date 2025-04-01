<?php
require 'database.php';
session_start();

// Haal de types op uit de type-tabel
$query = "SELECT type FROM ingredienttypes";
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
        <form action="ingredienten_add_process.php" method="post" enctype="multipart/form-data">
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
            <button type="submit">Toevoegen</button>
        </form>
    
    </main>
    <script src="script.js"></script>
</body>
</html>