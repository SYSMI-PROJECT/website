<?php
include __DIR__ . '/../../../../../requiments/UsersData.php';
include __DIR__ . '/../../../../../requiments/database.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'redirect' => '/Import/Error/No_connected.php', 'message' => "Vous devez être connecté pour ajouter un commentaire."]);
    exit;
}

$user_id = $_SESSION['user_id'];

$query_user = "SELECT prenom FROM utilisateur WHERE id = :user_id";
$stmt_user = $conn->prepare($query_user);
$stmt_user->execute([':user_id' => $user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if ($user && isset($user['prenom'])) {
    $username = $user['prenom'];
} else {
    $username = 'Utilisateur inconnu';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $publication_id = $_POST['publication_id'] ?? null;
    $comment = trim($_POST['comment'] ?? '');

    if (empty($publication_id) || empty($comment)) {
        echo json_encode(['success' => false, 'message' => "Le commentaire ne peut pas être vide."]);
        exit;
    }

    try {
        $query = "
            INSERT INTO comments (publication_id, user_id, contenu, username, date_creation) 
            VALUES (:publication_id, :user_id, :contenu, :username, NOW())
        ";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            'publication_id' => $publication_id,
            'user_id' => $user_id,
            'contenu' => htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'),
            'username' => htmlspecialchars($username, ENT_QUOTES, 'UTF-8')
        ]);
        
        $lastCommentQuery = "SELECT * FROM comments WHERE publication_id = :publication_id ORDER BY id DESC LIMIT 1";
        $stmt_last_comment = $conn->prepare($lastCommentQuery);
        $stmt_last_comment->execute(['publication_id' => $publication_id]);
        $lastComment = $stmt_last_comment->fetch(PDO::FETCH_ASSOC);

        if ($lastComment) {
            $commentHtml = "
                <div class='comment'>
                    <div class='comment-avatar'>
                        <img style='width: 40px; height: 40px;' src='" . htmlspecialchars($commentAvatar ?? 'https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg') . "' alt='Avatar du commentateur'>
                    </div>
                    <div class='comment-text'>
                        <div>" . htmlspecialchars($lastComment['username']) . " :</div> 
                        <div>" . nl2br(htmlspecialchars($lastComment['contenu'])) . "</div>
                    </div>
                </div>
            ";

            echo json_encode([
                'success' => true,
                'commentHtml' => $commentHtml
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => "Une erreur est survenue lors de l'ajout du commentaire."]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de l'insertion du commentaire: " . $e->getMessage()]);
    }

    exit;
}

echo json_encode(['success' => false, 'message' => "Requête invalide."]);
exit;
?>
