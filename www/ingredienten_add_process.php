<?php
require 'database.php';
session_start();

try {
    // Zet foutmeldingen aan
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Gegevens ophalen uit formulier
    $naam = $_POST['naam'];
    $type = $_POST['type']; // Maaltijdtype naam

    // **Stap 1: Type-ID ophalen**
    $stmt = $conn->prepare("SELECT type_id FROM ingredienttypes WHERE type = :type");
    $stmt->execute([':type' => $type]);
    $typeResult = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$typeResult) {
        throw new Exception("Ongeldig ingredienttype geselecteerd.");
    }

    $type_id = $typeResult['type_id'];

    // **Stap 3: Begin transactie**
    $conn->beginTransaction();
    
    // **Stap 4: Recept toevoegen**
    $sql = "INSERT INTO ingredienten (naam, type_id) 
            VALUES (:naam, :type_id)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':naam' => $naam,
        ':type_id' => $type_id
    ]);

    // **Stap 5: Commit transactie**
    $conn->commit();

    // Redirect naar receptenpagina
    header("Location: ingredienten_index.php?success=1");
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
