<?php
// Inclure le fichier de connexion à la base de données
include 'database.php';

// Démarrer la session
session_start();

try {
    // Vérifier si l'id de l'utilisateur à afficher est spécifié dans l'URL
    if (isset($_GET['id'])) {
        $userID = $_GET['id'];

        // Récupérer l'ID de l'utilisateur connecté
        $loggedInUserID = $_SESSION['user_id']; // Assurez-vous d'avoir cette variable dans votre session

        // Récupérer les informations de l'utilisateur
        $sql = "SELECT u.id, u.nom, u.prenom, u.date_inscription, u.bio, u.email, u.lien_tiktok, u.lien_youtube, u.lien_snapchat, u.lien_instagram, u.lien_discord, u.lien_twitch,
                       u.verified, u.role, u.statut, p.image_content
                FROM utilisateur u 
                LEFT JOIN photos_de_profil p ON u.id = p.user_id 
                WHERE u.id = ?";
        $stmt = $conn->prepare($sql);

        // Exécution de la requête
        if ($stmt->execute([$userID])) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                // Récupérer les données de l'utilisateur
                $userName = htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']);
                $userInscription = isset($userData['date_inscription']) ? htmlspecialchars($userData['date_inscription']) : 'Non disponible';
                $userBio = isset($userData['bio']) ? nl2br(htmlspecialchars($userData['bio'])) : 'Non défini';
                $userProfilePic = isset($userData['image_content']) ? $userData['image_content'] : null;
                $userTikTok = isset($userData['lien_tiktok']) ? htmlspecialchars($userData['lien_tiktok']) : null;
                $userYouTube = isset($userData['lien_youtube']) ? htmlspecialchars($userData['lien_youtube']) : null;
                $userSnapchat = isset($userData['lien_snapchat']) ? htmlspecialchars($userData['lien_snapchat']) : null;
                $userInstagram = isset($userData['lien_instagram']) ? htmlspecialchars($userData['lien_instagram']) : null;
                $userDiscord = isset($userData['lien_discord']) ? htmlspecialchars($userData['lien_discord']) : null;
                $userEmail = isset($userData['email']) ? htmlspecialchars($userData['email']) : 'Non disponible';
                $userTwitch = isset($userData['lien_twitch']) ? htmlspecialchars($userData['lien_twitch']) : null;
                $accountActive = isset($userData['verified']) ? (bool)$userData['verified'] : false;
                $role = isset($userData['role']) ? htmlspecialchars($userData['role']) : 'standard'; // Remplace VIP par role
                $status = isset($userData['statut']) ? $userData['statut'] : 'actif'; // Récupérer le statut

                // Vérifier le statut de l'utilisateur
                if ($status === 'banni') {
                    header("Location: ../../Import/Error/Account_banned.php");
                    exit; // Arrêter l'exécution si l'utilisateur est banni
                }

                // Requête SQL pour compter le nombre d'amis
                $sql_count_amis = "SELECT 
                                    (SELECT COUNT(*) FROM relation WHERE demandeur = ? AND statut = 1) + 
                                    (SELECT COUNT(*) FROM relation WHERE receveur = ? AND statut = 1) AS nombre_amis";
                $stmt_count_amis = $conn->prepare($sql_count_amis);
                $stmt_count_amis->execute([$userID, $userID]);
                $result_count_amis = $stmt_count_amis->fetch(PDO::FETCH_ASSOC);
                $nombreAmis = $result_count_amis['nombre_amis'];

                // Vérifier le statut de la relation
                $sql_relation = "SELECT demandeur, receveur, statut FROM relation
                                 WHERE (demandeur = ? AND receveur = ?) OR (demandeur = ? AND receveur = ?)";
                $stmt_relation = $conn->prepare($sql_relation);
                $stmt_relation->execute([$loggedInUserID, $userID, $userID, $loggedInUserID]);
                $relation = $stmt_relation->fetch(PDO::FETCH_ASSOC);

                // Déterminer l'état de la relation
                $status = isset($relation['statut']) ? intval($relation['statut']) : null;
                $isRequester = isset($relation['demandeur']) && $relation['demandeur'] == $loggedInUserID;

                // Récupérer les publications de l'utilisateur
                $sql_publications = "SELECT id, auteur, contenu, avatar, date_creation, media_path, hashtags 
                                     FROM publications 
                                     WHERE user_id = ?
                                     ORDER BY date_creation DESC";
                $stmt_publications = $conn->prepare($sql_publications);
                $stmt_publications->execute([$userID]);
                $publications = $stmt_publications->fetchAll(PDO::FETCH_ASSOC);

            } else {
                echo "<p>L'utilisateur avec l'ID $userID n'existe pas.</p>";
            }
        } else {
            echo "<p>Erreur lors de la récupération des informations de l'utilisateur.</p>";
        }
    } else {
        echo "<p>Paramètre d'ID d'utilisateur manquant dans l'URL.</p>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
