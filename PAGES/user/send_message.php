<?php
session_start();
include '../../request/DB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['receiver_id'], $_POST['message'])) {
        $receiver_id = (int)$_POST['receiver_id']; // Sécuriser l'entrée
        $message = trim($_POST['message']); // Nettoyer le message

        if (empty($message)) {
            echo "Le message ne peut pas être vide.";
            exit();
        }

        $sender_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

        // Insérer le message dans la base de données
        $sql = "INSERT INTO messages_prives (expediteur_id, destinataire_id, contenu, date_envoi)
                VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$sender_id, $receiver_id, $message])) {
            // Rediriger vers la page de conversation
            header("Location: message.php?id=$receiver_id");
            exit();
        } else {
            echo "Erreur lors de l'envoi du message.";
        }
    } else {
        echo "Données POST manquantes.";
    }
} else {
    echo "Requête invalide.";
}
?>
