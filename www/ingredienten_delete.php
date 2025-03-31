<?php
require 'database.php';
session_start();

try {
    // Zet foutmeldingen aan
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Zorg ervoor dat een id is meegegeven
    if (!isset($_GET['id'])) {
        throw new Exception("Geen ingrediënt geselecteerd.");
    }

    $ingredient_id = $_GET['id'];

    // **Stap 1: Controleer of het ingrediënt bestaat**
    $stmt = $conn->prepare("SELECT naam FROM ingredienten WHERE ingredient_id = :ingredient_id");
    $stmt->execute([':ingredient_id' => $ingredient_id]);
    $ingredient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ingredient) {
        throw new Exception("Ingrediënt niet gevonden.");
    }

    // **Stap 2: Begin transactie**
    $conn->beginTransaction();

    // **Stap 3: Verwijder ingrediënt**
    $sql = "DELETE FROM ingredienten WHERE ingredient_id = :ingredient_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':ingredient_id' => $ingredient_id]);

    // **Stap 4: Commit transactie**
    $conn->commit();

    // Redirect naar de pagina met succesbericht
    header("Location: ingredienten_index.php?success=1");
    exit();
} catch (Exception $e) {
    // **Controleer of transactie is gestart vóór rollback**
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    // Foutmelding weergeven (voor debugging)
    echo "Fout bij verwijderen ingrediënt: " . $e->getMessage();
}
?>
