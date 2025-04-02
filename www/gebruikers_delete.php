<?php
require 'database.php';
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['gebruiker_id'])) {
    header("Location: login.php"); // Als de gebruiker niet is ingelogd, doorsturen naar de loginpagina
    exit();
}

// Verkrijg de gebruiker_id uit de URL
if (isset($_GET['id'])) {
    $gebruiker_id = $_GET['id'];
} else {
    // Als geen id is meegegeven, stuur dan de gebruiker naar de gebruikerslijst of een foutpagina
    echo "Geen gebruiker_id opgegeven.";
    exit();
}

try {
    // Begin een transactie zodat alle stappen als één geheel uitgevoerd worden
    $conn->beginTransaction();

    // Verwijder eerst de reviews die aan deze gebruiker gekoppeld zijn
    $stmtReviews = $conn->prepare("DELETE FROM reviews WHERE bezoeker_id = (SELECT bezoeker_id FROM bezoekers WHERE gebruiker_id = :gebruiker_id)");
    $stmtReviews->execute([':gebruiker_id' => $gebruiker_id]);

    // Verwijder de bezoeker uit de 'bezoekers' tabel
    $stmtBezoeker = $conn->prepare("DELETE FROM bezoekers WHERE gebruiker_id = :gebruiker_id");
    $stmtBezoeker->execute([':gebruiker_id' => $gebruiker_id]);

    // Verwijder de gebruiker uit de 'gebruikers' tabel
    $stmtGebruiker = $conn->prepare("DELETE FROM gebruikers WHERE gebruiker_id = :gebruiker_id");
    $stmtGebruiker->execute([':gebruiker_id' => $gebruiker_id]);

    // Commit de transactie (bevestig de wijzigingen)
    $conn->commit();

    // Succesbericht
    echo "De gebruiker is succesvol verwijderd.";
    // Optioneel: Redirect naar de gebruikerslijst of een andere pagina
    header("Location: gebruikers_index.php"); // Vervang dit met de pagina waar je de gebruiker naartoe wilt sturen
    exit();

} catch (Exception $e) {
    // Als er een fout optreedt, rolt de transactie terug
    $conn->rollBack();
    echo "Er is een fout opgetreden bij het verwijderen van de gebruiker: " . $e->getMessage();
    exit();
}
?>
