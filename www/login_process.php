<?php
include "database.php";
session_start(); // Zet de sessie aan

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Controleer of gebruikersnaam en wachtwoord zijn ingevuld
    if (empty($_POST['gebruikersnaam']) || empty($_POST['wachtwoord'])) {
        die("Vul zowel gebruikersnaam als wachtwoord in.");
    }

    // Haal de ingevoerde gegevens op
    $gebruikersnaam = trim($_POST['gebruikersnaam']);
    $wachtwoord = $_POST['wachtwoord'];

    // Zoek naar de gebruiker in de database
    $sql = "SELECT gebruiker_id, gebruikersnaam, wachtwoord, rol FROM gebruikers WHERE gebruikersnaam = :gebruikersnaam";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':gebruikersnaam', $gebruikersnaam, PDO::PARAM_STR);
    $stmt->execute();
    $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($gebruiker) {
        // Controleer of het wachtwoord klopt
        if (password_verify($wachtwoord, $gebruiker['wachtwoord'])) {
            // Zoek de bezoeker_id op die bij de gebruiker hoort
            $sql = "SELECT bezoeker_id FROM bezoekers WHERE gebruiker_id = :gebruiker_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':gebruiker_id', $gebruiker['gebruiker_id'], PDO::PARAM_INT);
            $stmt->execute();
            $bezoeker = $stmt->fetch(PDO::FETCH_ASSOC);

            // Beveilig de sessie verder
            session_regenerate_id(true);

            // Zet de sessiegegevens
            $_SESSION['gebruikersnaam'] = $gebruiker['gebruikersnaam'];
            $_SESSION['gebruiker_id'] = $gebruiker['gebruiker_id'];
            $_SESSION['rol'] = $gebruiker['rol'];
            $_SESSION['bezoeker_id'] = $bezoeker['bezoeker_id'] ?? null; // Als er geen bezoeker_id is, zet null

            // Redirect naar de homepagina
            header("Location: index.php");
            exit();
        } else {
            echo "Onjuist wachtwoord!";
        }
    } else {
        echo "Gebruiker niet gevonden!";
    }
}
?>
