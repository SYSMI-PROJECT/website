<?php
session_start();
include __DIR__ . '/../../../../../requiments/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['receiver_id'], $_POST['message'])) {
        // Sécuriser les données d'entrée
        $receiver_id = (int)$_POST['receiver_id']; // Assurez-vous que receiver_id est un entier
        $message = trim($_POST['message']); // Nettoyer le message

        if (empty($message)) {
            echo "Le message ne peut pas être vide.";
            exit();
        }

        // Sécuriser le message contre les attaques XSS (évasion des caractères HTML)
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        // ID de l'utilisateur connecté
        if (!isset($_SESSION['user_id'])) {
            echo "Utilisateur non connecté.";
            exit();
        }
        $sender_id = $_SESSION['user_id'];

        // Vérification si l'utilisateur peut envoyer un message à ce destinataire
        // Par exemple, vous pouvez ajouter une vérification pour vérifier si l'utilisateur est autorisé à envoyer un message au destinataire.

        // Insérer le message dans la base de données
        try {
            $sql = "INSERT INTO messages_prives (expediteur_id, destinataire_id, contenu, date_envoi)
                    VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);

            // Exécuter la requête et vérifier si l'insertion a réussi
            if ($stmt->execute([$sender_id, $receiver_id, $message])) {
                // Rediriger vers la page de conversation
                header("Location: message.php?id=$receiver_id");
                exit();
            } else {
                echo "Erreur lors de l'envoi du message. Veuillez réessayer plus tard.";
            }
        } catch (PDOException $e) {
            echo "Erreur de base de données : " . $e->getMessage();
        }
    } else {
        echo "Données POST manquantes. Assurez-vous d'envoyer le message et le destinataire.";
    }
} else {
    echo "Requête invalide. La méthode doit être POST.";
}
?>
