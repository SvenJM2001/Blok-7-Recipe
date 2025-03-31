<?php
require 'database.php';
session_start();

try {
    // Zet foutmeldingen aan
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Zorg ervoor dat een id is meegegeven
    if (!isset($_GET['id'])) {
        throw new Exception("Geen recept geselecteerd.");
    }

    $recept_code = $_GET['id'];

    // **Stap 1: Controleer of het recept bestaat**
    $stmt = $conn->prepare("SELECT naam FROM recepten WHERE recept_code = :recept_code");
    $stmt->execute([':recept_code' => $recept_code]);
    $recept = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recept) {
        throw new Exception("Recept niet gevonden.");
    }

    // **Stap 2: Begin transactie**
    $conn->beginTransaction();

    // **Stap 3: Verwijder de favorieten van het recept**
    $sql = "DELETE FROM favorieten WHERE recept_code = :recept_code";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':recept_code' => $recept_code]);

    // **Stap 4: Verwijder de reviews van het recept**
    $sql = "DELETE FROM reviews WHERE recept_code = :recept_code";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':recept_code' => $recept_code]);

    // **Stap 5: Verwijder de gekoppelde ingrediënten**
    $sql = "DELETE FROM recepten_ingredienten WHERE recept_code = :recept_code";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':recept_code' => $recept_code]);

    // **Stap 6: Verwijder het recept zelf**
    $sql = "DELETE FROM recepten WHERE recept_code = :recept_code";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':recept_code' => $recept_code]);

    // **Stap 7: Commit transactie**
    $conn->commit();

    // Redirect naar de receptenpagina met succesmelding
    header("Location: recepten_index.php?success=1");
    exit();
} catch (Exception $e) {
    // **Controleer of transactie is gestart vóór rollback**
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    // Foutmelding weergeven (voor debugging)
    echo "Fout bij verwijderen recept: " . $e->getMessage();
}
?>
