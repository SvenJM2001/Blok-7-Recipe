<?php
include 'database.php';
session_start();

if (!isset($_POST['recept_code']) || !isset($_SESSION['bezoeker_id'])) {
    die("Onjuiste toegang.");
}

$recept_code = $_POST['recept_code'];
$bezoeker_id = $_SESSION['bezoeker_id'];
$rating = $_POST['rating'];
$tekst = $_POST['tekst'];

// Beperkingen en validaties
if ($rating < 1 || $rating > 5) {
    die("Ongeldige beoordeling.");
}

if (empty($tekst)) {
    die("Reviewtekst mag niet leeg zijn.");
}

// Voeg de review toe aan de database
$sql = "INSERT INTO reviews (recept_code, bezoeker_id, rating, tekst) 
        VALUES (:recept_code, :bezoeker_id, :rating, :tekst)";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':recept_code' => $recept_code,
    ':bezoeker_id' => $bezoeker_id,
    ':rating' => $rating,
    ':tekst' => $tekst
]);

// Redirect terug naar de recept detailpagina
header("Location: recepten_detail.php?id=$recept_code");
exit;
