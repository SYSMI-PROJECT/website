<?php
// Inclure le fichier de connexion à la base de données
require_once('../../request/DB.php');

// Vérifier si l'ID de l'utilisateur est passé en paramètre
if(isset($_GET['id']) && !empty($_GET['id'])) {
    // Récupérer l'ID de l'utilisateur depuis la requête
    $id_utilisateur = $_GET['id'];

    try {
        // Préparer la requête pour mettre à jour la colonne "banni" de l'utilisateur à 0 (pour le débannir)
        $stmt = $conn->prepare("UPDATE utilisateur SET banni = 0 WHERE id = :id");

        // Liaison des paramètres
        $stmt->bindParam(':id', $id_utilisateur);

        // Exécuter la requête
        $stmt->execute();

        // Redirection vers la page précédente après débannissement
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } catch (PDOException $e) {
        // En cas d'erreur, afficher un message d'erreur
        echo "Erreur : " . $e->getMessage();
    }
} else {
    // Si l'ID de l'utilisateur n'est pas spécifié, afficher un message d'erreur
    echo "ID de l'utilisateur non spécifié.";
}
?>
