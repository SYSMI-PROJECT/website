<?php
session_start();
include __DIR__ . '/../../../../../../requiments/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm'])) {
    $user_id = $_SESSION['user_id'];

    // Supprimer l'utilisateur de la base de données
    $sql = "DELETE FROM utilisateur WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":user_id", $user_id);

    if ($stmt->execute()) {
        // Détruire la session de l'utilisateur
        session_unset();
        session_destroy();
        
        // Rediriger vers la page d'accueil ou une autre page
        header("Location: /index.php");
        exit();
    } else {
        echo "Une erreur s'est produite lors de la suppression de votre compte. Veuillez réessayer.";
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
