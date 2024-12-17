<?php
// Inclure le fichier de connexion à la base de données
require_once('../../request/DB.php');

// Vérifier si l'ID de l'utilisateur est passé en paramètre
if(isset($_GET['id']) && !empty($_GET['id'])) {
    // Récupérer l'ID de l'utilisateur depuis la requête
    $id_utilisateur = $_GET['id'];

    try {
        // Préparer la requête pour supprimer l'utilisateur de la base de données
        $stmt = $conn->prepare("DELETE FROM utilisateur WHERE id = :id");

        // Liaison des paramètres
        $stmt->bindParam(':id', $id_utilisateur);

        // Exécuter la requête
        $stmt->execute();

        // Redirection vers la page précédente après la suppression du compte
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
