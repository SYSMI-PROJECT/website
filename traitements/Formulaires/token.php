<?php
// Connexion à la base de données
require '../../request/DB.php';

// Récupération du jeton de vérification depuis l'URL
$verificationToken = $_GET['token'];

// Requête SQL pour mettre à jour le statut verified
$sql = "UPDATE utilisateur SET verified = 1 WHERE verification_token = :token";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':token', $verificationToken);
$stmt->execute();

// Rediriger vers une page de confirmation
header("Location: ../../Import/Error/Mail/Mail_Verified.php");
exit;
?>
