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

// Verwerk het formulier als het is verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verkrijg de geposte waarden
    $voornaam = $_POST['voornaam'];
    $tussenvoegsel = $_POST['tussenvoegsel'];
    $achternaam = $_POST['achternaam'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $landcode = $_POST['landcode'];
    $plaats = $_POST['plaats'];
    $straat = $_POST['straat'];
    $huisnummer = $_POST['huisnummer'];
    $postcode = $_POST['postcode'];

    try {
        // Begin een transactie
        $conn->beginTransaction();

        // Update de gebruikersgegevens
        $stmt = $conn->prepare("UPDATE gebruikers SET 
                                voornaam = :voornaam, 
                                tussenvoegsel = :tussenvoegsel, 
                                achternaam = :achternaam, 
                                email = :email, 
                                rol = :rol 
                                WHERE gebruiker_id = :gebruiker_id");

        $stmt->execute([
            ':voornaam' => $voornaam,
            ':tussenvoegsel' => $tussenvoegsel,
            ':achternaam' => $achternaam,
            ':email' => $email,
            ':rol' => $rol,
            ':gebruiker_id' => $gebruiker_id
        ]);

        // Update de adresgegevens
        $stmt = $conn->prepare("UPDATE bezoekers SET 
                                landcode = :landcode, 
                                plaats = :plaats, 
                                straat = :straat, 
                                huisnummer = :huisnummer, 
                                postcode = :postcode 
                                WHERE gebruiker_id = :gebruiker_id");

        $stmt->execute([
            ':landcode' => $landcode,
            ':plaats' => $plaats,
            ':straat' => $straat,
            ':huisnummer' => $huisnummer,
            ':postcode' => $postcode,
            ':gebruiker_id' => $gebruiker_id
        ]);

        // Commit de transactie
        $conn->commit();

        // Redirect naar een andere pagina of geef een succesmelding
        header("Location: gebruikers_index.php?update=success");
        exit();

    } catch (Exception $e) {
        // Als er een fout optreedt, maak de transactie ongedaan
        $conn->rollBack();
        echo "Er is een fout opgetreden: " . $e->getMessage();
        exit();
    }
}
?>
