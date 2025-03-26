<?php
include "database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Haal de ingevoerde gegevens op
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];

    // Zoek naar de gebruiker in de database
    $sql = "SELECT gebruiker_id, gebruikersnaam, wachtwoord, rol FROM gebruikers WHERE gebruikersnaam = :gebruikersnaam";
    $stmt = $conn->prepare($sql);

    // Bind de parameters
    $stmt->bindValue(':gebruikersnaam', $gebruikersnaam, PDO::PARAM_STR);

    // Voer de query uit
    if ($stmt->execute()) {
        // Als de gebruiker wordt gevonden
        $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($gebruiker) {
            // Controleer of het ingevoerde wachtwoord klopt
            if (password_verify($wachtwoord, $gebruiker['wachtwoord'])) {
                // Sessie starten
                session_start();

                // Zet de sessiegegevens
                $_SESSION['gebruikersnaam'] = $gebruiker['gebruikersnaam'];
                $_SESSION['gebruiker_id'] = $gebruiker['gebruiker_id'];
                $_SESSION['rol'] = $gebruiker['rol'];

                // Redirect naar de homepagina of een andere pagina
                header("Location: index.php");
                exit();
            } else {
                echo "Onjuist wachtwoord!";
            }
        } else {
            echo "Gebruiker niet gevonden!";
        }
    } else {
        echo "Er is een fout opgetreden bij het uitvoeren van de query.";
    }
}
?>