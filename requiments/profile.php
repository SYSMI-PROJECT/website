<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'database.php';

// Récupérez l'ID de l'utilisateur depuis la session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Initialisez la variable pour le contenu de l'image
$image_content = null;

if ($user_id !== null) {
    // Préparez et exécutez la requête SQL pour récupérer le contenu de l'image
    $requete_sql = "SELECT image_content FROM photos_de_profil WHERE user_id = ?";
    
    try {
        $resultat = $conn->prepare($requete_sql);
        $resultat->execute([$user_id]);

        $row = $resultat->fetch(PDO::FETCH_ASSOC);

        if ($row !== false) {
            $image_content = $row['image_content'];
        }
    } catch (PDOException $e) {
        // Gérez les erreurs de base de données si nécessaire
        echo "Erreur lors de la récupération de l'image : " . $e->getMessage();
    }
}
?>
