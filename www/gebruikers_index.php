<?php
include 'database.php';
session_start();

// Verkrijg de zoekterm (als die er is)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Haal de gebruikers en hun adressen op met een JOIN tussen de gebruikers en bezoekers tabel
$sql = "SELECT gebruikers.*, bezoekers.*
        FROM gebruikers
        LEFT JOIN bezoekers ON gebruikers.gebruiker_id = bezoekers.gebruiker_id";

// Voeg een zoekfilter toe aan de query als er een zoekterm is
if (!empty($search)) {
    $sql .= " WHERE gebruikers.achternaam LIKE :search";
    $params = [':search' => "%" . $search . "%"];
} else {
    $params = [];
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$gebruikers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruikers Overzicht</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <?php 'header.php'; ?>
    <main>
        <div>
            <form class="gebruikers_search" method="GET" action="gebruikers_index.php">
                <div>
                    <label for="search">Zoeken op achternaam:</label>
                    <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button type="submit">Zoeken</button>
            </form>
        </div>
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>Gebruikersnaam</th>
                        <th>Volledige Naam</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Adres</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gebruikers as $gebruiker) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($gebruiker['gebruikersnaam']); ?></td>
                            <td>
                                <?php 
                                    // Volledige naam (voornaam, tussenvoegsel, achternaam)
                                    echo htmlspecialchars($gebruiker['voornaam']) . ' ';
                                    echo htmlspecialchars($gebruiker['tussenvoegsel']) . ' ';
                                    echo htmlspecialchars($gebruiker['achternaam']);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($gebruiker['email']); ?></td>
                            <td><?php echo htmlspecialchars($gebruiker['rol']); ?></td>
                            <td>
                                <?php 
                                    // Adres (plaats, straat, huisnummer, postcode)
                                    echo htmlspecialchars($gebruiker['plaats']) . ', ';
                                    echo htmlspecialchars($gebruiker['straat']) . ' ';
                                    echo htmlspecialchars($gebruiker['huisnummer']) . ', ';
                                    echo htmlspecialchars($gebruiker['postcode']);
                                ?>
                            </td>
                            <td>
                                <a href="gebruikers_detail.php?id=<?php echo $gebruiker['gebruiker_id']; ?>">Bekijk</a>
                                <a href="gebruikers_edit.php?id=<?php echo $gebruiker['gebruiker_id']; ?>">Wijzig</a>
                                <a href="gebruikers_delete.php?id=<?php echo $gebruiker['gebruiker_id']; ?>">Verwijder</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>
