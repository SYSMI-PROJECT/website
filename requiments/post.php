<?php
// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'database.php';
require_once 'Counter/messages.php';
require_once 'Auth.php';

$user_id = $_SESSION['user_id'] ?? null; // ID de l'utilisateur connecté

// Fonction pour convertir les hashtags en liens
function convertHashtagsToLinks($text) {
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    return preg_replace('/#(\w+)/', '<a href="/PAGES/user/hashtags.php?tag=$1">#$1</a>', $text);
}

// Fonction pour récupérer les publications en fonction du filtre et de la visibilité du compte
function getPublications($pdo, $filterUserId = null, $publicationId = null, $currentUserId = null) {
    // Cas sans filtre : on affiche uniquement les publications d'utilisateurs publics
    if (!$filterUserId) {
        $stmt = $pdo->prepare("
            SELECT p.*
            FROM publications p 
            INNER JOIN user_settings us ON p.user_id = us.user_id 
            WHERE us.visibility = 'public' 
            ORDER BY RAND()
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Si on filtre par un utilisateur précis
        // Si le visiteur n'est pas le propriétaire du compte, on vérifie la visibilité
        if ($filterUserId != $currentUserId) {
            $stmt = $pdo->prepare("SELECT visibility FROM user_settings WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $filterUserId, PDO::PARAM_INT);
            $stmt->execute();
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($settings && $settings['visibility'] !== 'public') {
                // Le compte est privé : aucune publication n'est retournée
                return [];
            }
        }
        // Récupération des publications pour l'utilisateur filtré
        if ($publicationId) {
            // Si une publication spécifique est demandée, on la récupère en priorité
            $stmt = $pdo->prepare("
                (SELECT * FROM publications WHERE id = :publication_id)
                UNION ALL
                (SELECT * FROM publications WHERE user_id = :user_id AND id != :publication_id ORDER BY date_creation DESC)
            ");
            $stmt->bindParam(':publication_id', $publicationId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $filterUserId, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM publications WHERE user_id = :user_id ORDER BY date_creation DESC");
            $stmt->bindParam(':user_id', $filterUserId, PDO::PARAM_INT);
        }
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les paramètres de l'URL
$filterUserId = $_GET['id'] ?? null;           // Filtre par utilisateur
$publicationId = $_GET['publication_id'] ?? null; // Publication spécifique, si applicable

// Récupérer les publications en passant l'ID du visiteur pour comparer avec la visibilité
$publications = getPublications($conn, $filterUserId, $publicationId, $user_id);

// Gestion des interactions (likes et commentaires)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gestion des likes
    if (isset($_POST['like'])) {
        $publicationId = $_POST['publication_id'] ?? null;
        $action = $_POST['action'] ?? '';
        if ($publicationId && $user_id) {
            if ($action === 'like') {
                executeQuery(
                    "INSERT INTO likes (publication_id, user_id) VALUES (:publication_id, :user_id)",
                    ['publication_id' => $publicationId, 'user_id' => $user_id]
                );
            } elseif ($action === 'unlike') {
                executeQuery(
                    "DELETE FROM likes WHERE publication_id = :publication_id AND user_id = :user_id",
                    ['publication_id' => $publicationId, 'user_id' => $user_id]
                );
            }
        }
    }
    // Gestion des commentaires
    if (isset($_POST['add_comment'])) {
        $publicationId = $_POST['publication_id'] ?? null;
        $comment = $_POST['comment'] ?? '';
        if ($publicationId && $comment && $user_id) {
            executeQuery(
                "INSERT INTO comments (publication_id, user_id, contenu) VALUES (:publication_id, :user_id, :contenu)",
                ['publication_id' => $publicationId, 'user_id' => $user_id, 'contenu' => $comment]
            );
        }
    }
}
?>