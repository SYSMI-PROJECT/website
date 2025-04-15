<?php
session_start();
include __DIR__ . '/../../../../../../requiments/database.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Initialisez la variable pour le contenu de l'image
$image_content = null;

try {
    // Préparez et exécutez la requête SQL pour récupérer le contenu de l'image
    $requete_sql = "SELECT image_content FROM photos_de_profil WHERE user_id = ?";
    
    $resultat = $conn->prepare($requete_sql);
    $resultat->execute([$user_id]);

    $row = $resultat->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        $image_content = $row['image_content'];
    }
} catch (PDOException $e) {
    // Gérez les erreurs de base de données si nécessaire
    echo "Error fetching image content: " . $e->getMessage();
}

// Vérifiez si le formulaire a été soumis
    try {
        // Supprimez la photo de profil de la base de données (ajustez la requête en fonction de votre schéma)
        $stmt = $conn->prepare("DELETE FROM photos_de_profil WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Redirigez l'utilisateur vers la page de profil après la suppression
        header("Location: /public/import/php/account.php?id=" . $user_id);
        exit;
    } catch (PDOException $e) {
        echo 'Erreur lors de la suppression de la photo de profil : ' . $e->getMessage();
    }


// Si le formulaire n'a pas été soumis ou s'il y a eu une erreur, redirigez l'utilisateur vers la page de profil
header('Location: ../index.php');
exit;
?>