<?php
session_start();
include __DIR__ . '/../../../../../../requiments/database.php';

// Récupérer l'ID de l'utilisateur depuis la session
$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si tous les champs du formulaire sont remplis
    if (!empty($_POST['social']) && !empty($_POST['link'])) {
        $social = $_POST['social'];
        $link = $_POST['link'];

        // Valider le format du lien en fonction du réseau social
        switch ($social) {
            case 'YouTube':
                // Vérifier si le lien correspond au format de YouTube
                if (!preg_match('~^(?:https?://)?(?:www\.)?youtube\.com/.+$~', $link)) {
                    header("Location: /public/import/Error/Unknown_network.php?message=Le+format+du+lien+YouTube+est+invalide.");
                    exit;
                }
                break;
            case 'TikTok':
                // Vérifier si le lien correspond au format de TikTok
                if (!preg_match('~^(?:https?://)?(?:www\.)?tiktok\.com/.+$~', $link)) {
                    header("Location: /public/import/Error/Unknown_network.php?message=Le+format+du+lien+TikTok+est+invalide.");
                    exit;
                }
                break;
            case 'Instagram':
                // Vérifier si le lien correspond au format d'Instagram
                if (!preg_match('~^(?:https?://)?(?:www\.)?instagram\.com/.+$~', $link)) {
                    header("Location: /public/import/Error/Unknown_network.php?message=Le+format+du+lien+Instagram+est+invalide.");
                    exit;
                }
                break;
            case 'Snapchat':
                // Vérifier si le lien correspond au format de Snapchat
                if (!preg_match('~^(?:https?://)?(?:www\.)?snapchat\.com/add/.+$~', $link)) {
                    header("Location: /public/import/Error/Unknown_network.php?message=Le+format+du+lien+Snapchat+est+invalide.");
                    exit;
                }
                break;
            case 'Discord':
                // Vérifier si le lien correspond au format de Discord
                if (!preg_match('~^(?:https?://)?(?:www\.)?(?:discord\.gg/|discord\.com/invite/).+$~', $link)) {
                    header("Location: /public/import/Error/Unknown_network.php?message=Le+format+du+lien+Discord+est+invalide.");
                    exit;
                }
                break;
            case 'Twitch':
                // Vérifier si le lien correspond au format de Twitch
                if (!preg_match('~^(?:https?://)?(?:www\.)?twitch\.tv/.+$~', $link)) {
                    header("Location: /public/import/Error/Unknown_network.php?message=Le+format+du+lien+Twitch+est+invalide.");
                    exit;
                }
                break;
            // Ajoutez d'autres cas pour les autres réseaux sociaux ici...
            default:
                die("Réseau social non reconnu.");
        }


        // Vérifier si l'ID de l'utilisateur est disponible
        if ($user_id !== null) {
            // Insérer les données dans la base de données en fonction du réseau social choisi
            switch ($social) {
                case 'TikTok':
                    $stmt = $conn->prepare("UPDATE utilisateur SET lien_tiktok = :link WHERE id = :user_id");
                    break;
                case 'Snapchat':
                    $stmt = $conn->prepare("UPDATE utilisateur SET lien_snapchat = :link WHERE id = :user_id");
                    break;
                case 'Instagram':
                    $stmt = $conn->prepare("UPDATE utilisateur SET lien_instagram = :link WHERE id = :user_id");
                    break;
                case 'YouTube':
                    $stmt = $conn->prepare("UPDATE utilisateur SET lien_youtube = :link WHERE id = :user_id");
                    break;
                case 'Discord':
                    $stmt = $conn->prepare("UPDATE utilisateur SET lien_discord = :link WHERE id = :user_id");
                    break;
                case 'Twitch':
                    $stmt = $conn->prepare("UPDATE utilisateur SET lien_twitch = :link WHERE id = :user_id");
                    break;
            }

            // Lier les paramètres et exécuter la requête
            $stmt->bindParam(':link', $link);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            header("Location: /public/import/Error/succes_network.php");
        } else {
            // Gérer le cas où l'ID de l'utilisateur n'est pas disponible
            echo "Erreur : ID de l'utilisateur non disponible.";
        }
    } else {
        echo "Veuillez remplir tous les champs du formulaire.";
    }
}
?>
