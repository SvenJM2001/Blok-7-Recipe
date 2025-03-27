<?php
require 'database.php';
session_start();

try {
    // Zet foutmeldingen aan
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Gegevens ophalen uit formulier
    $naam = $_POST['naam'];
    $beschrijving = $_POST['beschrijving'];
    $stappenplan = $_POST['stappenplan'];
    $tijd = $_POST['tijd'];
    $graad = $_POST['graad'];
    $type = $_POST['type']; // Maaltijdtype naam

    // **Stap 1: Type-ID ophalen**
    $stmt = $conn->prepare("SELECT type_id FROM maaltijdtypes WHERE type = :type");
    $stmt->execute([':type' => $type]);
    $typeResult = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$typeResult) {
        throw new Exception("Ongeldig maaltijdtype geselecteerd.");
    }

    $type_id = $typeResult['type_id'];

    // **Stap 2: Afbeelding uploaden**
    $uploadDir = "uploads/";
    $imageName = basename($_FILES["image"]["name"]);
    $uploadFile = $uploadDir . $imageName;

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $uploadFile)) {
        throw new Exception("Fout bij uploaden van afbeelding.");
    }

    // **Stap 3: Begin transactie**
    $conn->beginTransaction();
    
    // **Stap 4: Recept toevoegen**
    $sql = "INSERT INTO recepten (naam, beschrijving, stappenplan, bereidingstijd, moeilijkheidsgraad, afbeelding, type_id) 
            VALUES (:naam, :beschrijving, :stappenplan, :tijd, :graad, :afbeelding, :type_id)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':naam' => $naam,
        ':beschrijving' => $beschrijving,
        ':stappenplan' => $stappenplan,
        ':tijd' => $tijd,
        ':graad' => $graad,
        ':afbeelding' => $imageName,
        ':type_id' => $type_id
    ]);

    // **Stap 5: Recept ID ophalen**
    $recept_code = $conn->lastInsertId();

    // **Stap 6: Ingrediënten koppelen (indien geselecteerd)**
    if (!empty($_POST['ingredienten'])) {
        $sql = "INSERT INTO recepten_ingredienten (recept_code, ingredient_id) VALUES (:recept_code, :ingredient_id)";
        $stmt = $conn->prepare($sql);

        foreach ($_POST['ingredienten'] as $ingredient_naam) {
            // Haal het ingredient_id op
            $stmtIng = $conn->prepare("SELECT ingredient_id FROM ingredienten WHERE naam = :naam");
            $stmtIng->execute([':naam' => $ingredient_naam]);
            $ingredient = $stmtIng->fetch(PDO::FETCH_ASSOC);

            if ($ingredient) {
                $stmt->execute([
                    ':recept_code' => $recept_code,
                    ':ingredient_id' => $ingredient['ingredient_id']
                ]);
            }
        }
    }

    // **Stap 7: Commit transactie**
    $conn->commit();

    // Redirect naar receptenpagina
    header("Location: recepten_index.php?success=1");
    exit();
} catch (Exception $e) {
    // **Controleer of transactie is gestart vóór rollback**
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    // Foutmelding weergeven (voor debugging)
    echo "Fout bij toevoegen recept: " . $e->getMessage();
}
?>
