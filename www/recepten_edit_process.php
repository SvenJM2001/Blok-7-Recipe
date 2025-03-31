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

// Verkrijg de gegevens uit het formulier
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $naam = $_POST['naam'];
    $beschrijving = $_POST['beschrijving'];
    $bereidingstijd = $_POST['bereidingstijd'];
    $type_id = $_POST['type_id'];
    $stappenplan = $_POST['stappenplan'];
    $ingredienten = isset($_POST['ingredienten']) ? $_POST['ingredienten'] : [];

    try {
        // Start de transactie
        $conn->beginTransaction();

        // Update het recept
        $sql = "UPDATE recepten SET naam = :naam, beschrijving = :beschrijving, bereidingstijd = :bereidingstijd, type_id = :type_id, stappenplan = :stappenplan WHERE recept_code = :recept_code";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':naam', $naam);
        $stmt->bindParam(':beschrijving', $beschrijving);
        $stmt->bindParam(':bereidingstijd', $bereidingstijd);
        $stmt->bindParam(':type_id', $type_id);
        $stmt->bindParam(':stappenplan', $stappenplan);
        $stmt->bindParam(':recept_code', $receptCode);
        $stmt->execute();

        // Verwijder de oude ingrediënten
        $sql = "DELETE FROM recepten_ingredienten WHERE recept_code = :recept_code";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':recept_code', $receptCode);
        $stmt->execute();

        // Voeg de nieuwe geselecteerde ingrediënten toe
        if (!empty($ingredienten)) {
            $sql = "INSERT INTO recepten_ingredienten (recept_code, ingredient_id) VALUES (:recept_code, :ingredient_id)";
            $stmt = $conn->prepare($sql);

            foreach ($ingredienten as $ingredient_naam) {
                $stmtIng = $conn->prepare("SELECT ingredient_id FROM ingredienten WHERE naam = :naam");
                $stmtIng->execute([':naam' => $ingredient_naam]);
                $ingredient = $stmtIng->fetch(PDO::FETCH_ASSOC);

                if ($ingredient) {
                    $stmt->execute([
                        ':recept_code' => $receptCode,
                        ':ingredient_id' => $ingredient['ingredient_id']
                    ]);
                }
            }
        }

        // Commit de transactie
        $conn->commit();

        // Redirect naar de recepten overzichtpagina
        header("Location: recepten_index.php");
        exit;
    } catch (PDOException $e) {
        // Foutafhandelingsbericht
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo "Fout bij het bijwerken van het recept: " . $e->getMessage();
    }
}
?>
