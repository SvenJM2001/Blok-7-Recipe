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
    $hoeveelheid = isset($_POST['hoeveelheid']) ? $_POST['hoeveelheid'] : [];
    $eenheid = isset($_POST['eenheid']) ? $_POST['eenheid'] : [];

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

        // Loop door de geselecteerde ingredienten
        foreach ($ingredienten as $ingredient_id) {
            // Kijk of het ingrediënt al gekoppeld is aan het recept
            $stmtCheck = $conn->prepare("SELECT * FROM recepten_ingredienten WHERE recept_code = :recept_code AND ingredient_id = :ingredient_id");
            $stmtCheck->execute([
                ':recept_code' => $receptCode,
                ':ingredient_id' => $ingredient_id
            ]);

            if ($stmtCheck->rowCount() > 0) {
                // Als het ingrediënt al bestaat, werk de hoeveelheid en eenheid bij
                $sqlUpdate = "UPDATE recepten_ingredienten 
                              SET hoeveelheid = :hoeveelheid, eenheid = :eenheid
                              WHERE recept_code = :recept_code AND ingredient_id = :ingredient_id";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    ':hoeveelheid' => $hoeveelheid[$ingredient_id],
                    ':eenheid' => $eenheid[$ingredient_id],
                    ':recept_code' => $receptCode,
                    ':ingredient_id' => $ingredient_id
                ]);
            } else {
                // Als het ingrediënt nog niet gekoppeld is, voeg het dan toe
                $sqlInsert = "INSERT INTO recepten_ingredienten (recept_code, ingredient_id, hoeveelheid, eenheid) 
                              VALUES (:recept_code, :ingredient_id, :hoeveelheid, :eenheid)";
                $stmtInsert = $conn->prepare($sqlInsert);
                $stmtInsert->execute([
                    ':recept_code' => $receptCode,
                    ':ingredient_id' => $ingredient_id,
                    ':hoeveelheid' => $hoeveelheid[$ingredient_id],
                    ':eenheid' => $eenheid[$ingredient_id]
                ]);
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
