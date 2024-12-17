<?php
session_start();

// Inclure le fichier de connexion à la base de données
require '../../request/DB.php';

// Vérifier si l'utilisateur est connecté et récupérer son ID depuis la session
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
    http_response_code(403);
    exit();
}

// Vérifier si l'ID du message à supprimer est présent dans la requête GET
if(isset($_GET['id'])) {
    $message_id = $_GET['id'];

    // Supprimer le message de la base de données
    $sql_delete = "DELETE FROM messages WHERE id = :id";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':id', $message_id);
    $stmt_delete->execute();

    if ($stmt_delete->rowCount() > 0) {
        // Le message a été supprimé avec succès
        http_response_code(200);
    } else {
        // L'utilisateur n'est pas autorisé à supprimer ce message ou le message n'existe pas
        http_response_code(403);
    }
} else {
    // Si l'ID du message n'est pas présent dans la requête GET, renvoyer une erreur
    http_response_code(400);
}
?>
