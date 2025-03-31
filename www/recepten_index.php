<?php
include 'database.php';
session_start();

// Verkrijg de geselecteerde type voor filter (als er een is)
$selectedType = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Haal de beschikbare maaltijdtypes op voor de filter
$sql = "SELECT * FROM maaltijdtypes";
$stmt = $conn->prepare($sql);
$stmt->execute();
$types = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Haal de recepten op met type-informatie
$sql = "SELECT recepten.*, maaltijdtypes.type AS type_name FROM recepten
        LEFT JOIN maaltijdtypes ON recepten.type_id = maaltijdtypes.type_id"; 

// Voeg filters toe aan de query als ze zijn ingesteld
$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "recepten.naam LIKE :search";
    $params[':search'] = "%" . $search . "%";
}

if (!empty($selectedType)) {
    $conditions[] = "recepten.type_id = :type_id";
    $params[':type_id'] = $selectedType;
}

// Voeg de WHERE clause toe als er filters zijn
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$recepten = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepten Overzicht</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <?php require 'header.php'; ?>
    <main>
        <div>
            <form class="recept_search" method="GET" action="recepten_index.php">
                <div>
                    <label for="search">Zoeken op naam:</label>
                    <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div>
                    <label for="type">Filter op maaltijdtype:</label>
                    <select name="type" id="type">
                        <option value="">Alle types</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?php echo $type['type_id']; ?>" <?php echo ($type['type_id'] == $selectedType) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($type['type']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Zoeken en Filteren</button>
            </form>
        </div>
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Maaltijdtype</th>
                        <th>Beschrijving</th>
                        <th>Bereidingstijd</th>
                        <th>Afbeelding</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recepten as $recept) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($recept['naam']); ?></td>
                            <td><?php echo htmlspecialchars($recept['type_name']); ?></td>
                            <td><?php echo htmlspecialchars($recept['beschrijving']); ?></td>
                            <td><?php echo htmlspecialchars($recept['bereidingstijd']); ?></td>
                            <td>
                                <img src="./uploads/<?php echo htmlspecialchars($recept['afbeelding']); ?>" alt="<?php echo htmlspecialchars($recept['naam']); ?>" style="width: 100px;">
                            </td>
                            <td>
                                <a href="recepten_detail.php?id=<?php echo $recept['recept_code']; ?>">Bekijk</a>
                                <a href="recepten_edit.php?id=<?php echo $recept['recept_code']; ?>">Wijzig</a>
                                <a href="recepten_delete.php?id=<?php echo $recept['recept_code']; ?>">Verwijder</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
