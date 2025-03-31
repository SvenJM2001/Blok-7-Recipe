<?php
require 'database.php';
session_start();

// Controleer of de bezoeker is ingelogd
if (!isset($_SESSION['bezoeker_id'])) {
    header("Location: login.php");
    exit();
}

$bezoeker_id = $_SESSION['bezoeker_id'];

try {
    // Haal de oude gegevens op voor de validatie
    $stmt = $conn->prepare("SELECT g.wachtwoord, g.gebruiker_id
                            FROM gebruikers g
                            JOIN bezoekers b ON g.gebruiker_id = b.gebruiker_id
                            WHERE b.bezoeker_id = :bezoeker_id");
    $stmt->execute([':bezoeker_id' => $bezoeker_id]);
    $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$gebruiker) {
        throw new Exception("Bezoeker niet gevonden.");
    }

    // Haal de nieuwe gegevens uit het formulier
    $voornaam = $_POST['voornaam'];
    $tussenvoegsel = $_POST['tussenvoegsel'];
    $achternaam = $_POST['achternaam'];
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $email = $_POST['email'];
    $landcode = $_POST['landcode'];
    $plaats = $_POST['plaats'];
    $straat = $_POST['straat'];
    $huisnummer = $_POST['huisnummer'];
    $postcode = $_POST['postcode'];
    $wachtwoord = $_POST['wachtwoord'];
    $wachtwoord_herhalen = $_POST['wachtwoord_herhalen'];

    // Controleer of het wachtwoord wordt gewijzigd
    if (!empty($wachtwoord)) {
        // Controleer of de wachtwoorden overeenkomen
        if ($wachtwoord !== $wachtwoord_herhalen) {
            throw new Exception("De wachtwoorden komen niet overeen.");
        }

        // Hash het nieuwe wachtwoord
        $wachtwoord = password_hash($wachtwoord, PASSWORD_DEFAULT);
        $stmtWachtwoord = $conn->prepare("UPDATE gebruikers SET wachtwoord = :wachtwoord WHERE gebruiker_id = :gebruiker_id");
        $stmtWachtwoord->execute([
            ':wachtwoord' => $wachtwoord,
            ':gebruiker_id' => $gebruiker['gebruiker_id']
        ]);
    }

    // Update de rest van de gegevens in de bezoekers en gebruikers tabel
    $stmtUpdate = $conn->prepare("
        UPDATE gebruikers g
        JOIN bezoekers b ON g.gebruiker_id = b.gebruiker_id
        SET
            g.voornaam = :voornaam,
            g.tussenvoegsel = :tussenvoegsel,
            g.achternaam = :achternaam,
            g.gebruikersnaam = :gebruikersnaam,
            g.email = :email,
            b.landcode = :landcode,
            b.plaats = :plaats,
            b.straat = :straat,
            b.huisnummer = :huisnummer,
            b.postcode = :postcode
        WHERE b.bezoeker_id = :bezoeker_id
    ");
    $stmtUpdate->execute([
        ':voornaam' => $voornaam,
        ':tussenvoegsel' => $tussenvoegsel,
        ':achternaam' => $achternaam,
        ':gebruikersnaam' => $gebruikersnaam,
        ':email' => $email,
        ':landcode' => $landcode,
        ':plaats' => $plaats,
        ':straat' => $straat,
        ':huisnummer' => $huisnummer,
        ':postcode' => $postcode,
        ':bezoeker_id' => $bezoeker_id
    ]);

    // Redirect naar de bezoeker detailpagina
    header("Location: bezoeker_detail.php?success=1");
    exit();

} catch (Exception $e) {
    // Foutmelding weergeven
    echo "Fout bij bijwerken gegevens: " . $e->getMessage();
    exit();
}
?>
