<?php
include '../../request/DB.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour jouer.");
}

$loggedInUserID = $_SESSION['user_id'];
$date = date('Y-m-d H:i:s');

// Ajouter une entrée dans la base de données pour indiquer que le joueur a lancé le jeu
$sql = "INSERT INTO game_stats (user_id, game_name, date_played) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt->execute([$loggedInUserID, 'slideranime', $date])) {
    echo "Statistiques enregistrées.";
} else {
    echo "Erreur lors de l'enregistrement des statistiques.";
}
?>
