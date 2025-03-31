<?php
include 'database.php';
session_start();

if (!isset($_SESSION['gebruiker_id'])) {
    header("Location: login.php");
    exit;
}

$bezoeker_id = $_SESSION['bezoeker_id'];
$recept_code = $_POST['recept_code'];

// Check of het recept al in de favorieten zit
$sql = "SELECT * FROM favorieten WHERE bezoeker_id = :bezoeker_id AND recept_code = :recept_code";
$stmt = $conn->prepare($sql);
$stmt->execute([':bezoeker_id' => $bezoeker_id, ':recept_code' => $recept_code]);

if ($stmt->fetch()) {
    // Recept verwijderen uit favorieten
    $sql = "DELETE FROM favorieten WHERE bezoeker_id = :bezoeker_id AND recept_code = :recept_code";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':bezoeker_id' => $bezoeker_id, ':recept_code' => $recept_code]);
} else {
    // Recept toevoegen aan favorieten
    $sql = "INSERT INTO favorieten (bezoeker_id, recept_code) VALUES (:bezoeker_id, :recept_code)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':bezoeker_id' => $bezoeker_id, ':recept_code' => $recept_code]);
}

header("Location: recepten_detail.php?id=" . $recept_code);
exit;
?>
