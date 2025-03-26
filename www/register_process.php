<?php
include "database.php";

try {
    // Verbinding openen en foutmodus instellen
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Gegevens uit POST request ophalen
    $voornaam = $_POST['voornaam'];
    $tussenvoegsel = $_POST['tussenvoegsel'];
    $achternaam = $_POST['achternaam'];
    $email = $_POST['email'];
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = password_hash($_POST['wachtwoord'], PASSWORD_DEFAULT);
    $rol = 'bezoeker';

    $landnaam = $_POST['land'];  // Dit is de landnaam uit het formulier
    $plaats = $_POST['plaats'];
    $straat = $_POST['straat'];
    $huisnummer = $_POST['huisnummer'];
    $postcode = $_POST['postcode'];

    // Start transactie
    $conn->beginTransaction();

    // Stap 1: Haal de landcode op
    $sqlLand = "SELECT landcode FROM landen WHERE landnaam = :landnaam";
    $stmtLand = $conn->prepare($sqlLand);
    $stmtLand->execute([':landnaam' => $landnaam]);
    $landcode = $stmtLand->fetchColumn();

    // Controleer of het land gevonden is
    if (!$landcode) {
        throw new Exception("Ongeldig land geselecteerd.");
    }

    // Stap 2: Gebruiker invoegen
    $sql1 = "INSERT INTO gebruikers (voornaam, tussenvoegsel, achternaam, email, gebruikersnaam, wachtwoord, rol) 
             VALUES (:voornaam, :tussenvoegsel, :achternaam, :email, :gebruikersnaam, :wachtwoord, :rol)";

    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute([
        ':voornaam' => $voornaam,
        ':tussenvoegsel' => $tussenvoegsel,
        ':achternaam' => $achternaam,
        ':email' => $email,
        ':gebruikersnaam' => $gebruikersnaam,
        ':wachtwoord' => $wachtwoord,
        ':rol' => $rol
    ]);

    // Laatst ingevoegde gebruiker ID ophalen
    $gebruiker_id = $conn->lastInsertId();

    // Stap 3: Adres invoegen in bezoekers tabel met landcode
    $sql2 = "INSERT INTO bezoekers (gebruiker_id, landcode, plaats, straat, huisnummer, postcode) 
             VALUES (:gebruiker_id, :landcode, :plaats, :straat, :huisnummer, :postcode)";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute([
        ':gebruiker_id' => $gebruiker_id,
        ':landcode' => $landcode,
        ':plaats' => $plaats,
        ':straat' => $straat,
        ':huisnummer' => $huisnummer,
        ':postcode' => $postcode
    ]);

    // Transactie bevestigen
    $conn->commit();

    // Doorsturen naar login pagina
    header("Location: login.php");
    exit();

} catch (Exception $e) {
    // Fout afhandelen en transactie terugdraaien
    $conn->rollBack();
    echo "Fout bij registreren: " . $e->getMessage();
}
