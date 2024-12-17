<?php
session_start(); // Démarrer la session

include '../../request/DB.php'; // Inclure le fichier DB.php pour établir la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['user_id'])) {
        // Vérifier si le contenu du message est présent
        if (isset($_POST['content']) && !empty($_POST['content'])) {
            // Nettoyer et sécuriser le contenu du message
            $content = htmlspecialchars($_POST['content']);

            // Récupérer l'utilisateur actuel et son prénom depuis la base de données
            $userID = $_SESSION['user_id'];
            $query = "SELECT prenom FROM utilisateur WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$userID]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $prenom = ($user) ? $user['prenom'] : '';

            // Créer le format du message avec l'utilisateur et la date actuelle
            $userDisplayName = ($prenom) ? $prenom : 'Utilisateur'; // Utiliser le prénom s'il est disponible, sinon 'Utilisateur'
            $message = $userDisplayName . '|' . date('Y-m-d H:i:s') . '|' . $content . PHP_EOL;

            // Enregistrer le message dans le fichier
            file_put_contents('messages.txt', $message, FILE_APPEND | LOCK_EX);

            // Retourner le HTML du nouveau message pour l'affichage instantané sur la page
            echo '<div class="message-container">';
            echo '<div class="message-header">';
            echo '<div>Posté par ' . $userDisplayName . ' - ' . date('Y-m-d H:i:s') . '</div>';
            echo '<button class="delete-button" onclick="deleteMessage(\'' . $prenom . '\')">&times;</button>'; // Passer le prénom comme argument ici
            echo '</div>';
            echo '<div class="message-content">';
            echo '<p>' . nl2br($content) . '</p>';
            echo '</div>';
            echo '</div>';
        } else {
            // Si le contenu du message est vide, retourner un message d'erreur
            echo 'Le contenu du message est vide.';
        }
    } else {
        // Si l'utilisateur n'est pas connecté, retourner un message d'erreur
        echo 'Utilisateur non connecté.';
    }
} else {
    // Si la méthode de requête n'est pas POST, retourner un message d'erreur
    echo 'Méthode de requête incorrecte.';
}
?>
