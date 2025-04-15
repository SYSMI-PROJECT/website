<?php
include __DIR__ . '/../../../requiments/database.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Aucun destinataire spécifié.";
    exit();
}

$receiverID = $_GET['id'];
$loggedInUserID = $_SESSION['user_id'] ?? null;

if (!$loggedInUserID || $loggedInUserID == $receiverID) {
    echo "Vous ne pouvez pas vous envoyer un message à vous-même.";
    exit();
}

$sql_receiver = "SELECT nom, prenom, p.image_content 
                 FROM utilisateur u 
                 LEFT JOIN photos_de_profil p ON u.id = p.user_id 
                 WHERE u.id = ?";
$stmt_receiver = $conn->prepare($sql_receiver);
$stmt_receiver->execute([$receiverID]);
$receiver = $stmt_receiver->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT m.contenu, m.date_envoi, 
               u.nom AS expediteur_nom, u.prenom AS expediteur_prenom, 
               p.image_content AS expediteur_avatar
        FROM messages_prives m
        INNER JOIN utilisateur u ON m.expediteur_id = u.id
        LEFT JOIN photos_de_profil p ON u.id = p.user_id
        WHERE (m.expediteur_id = ? AND m.destinataire_id = ?) 
           OR (m.expediteur_id = ? AND m.destinataire_id = ?)
        ORDER BY m.date_envoi ASC";
$stmt = $conn->prepare($sql);
$stmt->execute([$loggedInUserID, $receiverID, $receiverID, $loggedInUserID]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/public/img/icon/Logo.png" type="image/png">
    <link rel="stylesheet" href="/public/import/css/page/message.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <title>Messages Privés</title>
</head>
<body>
<div class="chat-container">
    <div class="chat-header">
        <a href="javascript:void(0);" class="back-btn" onclick="goBack();">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2><?= htmlspecialchars($receiver['prenom'] . ' ' . $receiver['nom']) ?></h2>
    </div>

    <script>
        function goBack() {
            if (document.referrer && document.referrer !== window.location.href) {
                window.history.back();
            } else {
                window.location.href = 'MessageList.php';
            }
        }
    </script>

        <div class="chat-messages">
            <?php if (count($messages) > 0): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message <?= ($message['expediteur_nom'] === $receiver['nom']) ? 'received' : 'sent'; ?>">
                        <div class="avatar" 
                             style="background-image: url('data:image/jpeg;base64,<?= base64_encode($message['expediteur_avatar'] ?? 'default-avatar.jpg') ?>');">
                             <?php if (empty($message['expediteur_avatar'])) { echo substr($message['expediteur_prenom'], 0, 1); } ?>
                        </div>
                        <div class="message-content">
                            <div class="message-author"><?= htmlspecialchars($message['expediteur_prenom']) ?></div>
                            <div><?= htmlspecialchars($message['contenu']) ?></div>
                            <small class="message-date"><?= htmlspecialchars($message['date_envoi']) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-chat">Aucun message pour le moment. Commencez la conversation !</p>
            <?php endif; ?>
        </div>

<form class="chat-input" action="send_message.php" method="POST">
    <textarea name="message" rows="1" placeholder="Envoyez un message..." required></textarea>
    <button type="submit" class="send-button"><i class="fas fa-paper-plane"></i></button>
    <input type="hidden" name="receiver_id" value="<?= $receiverID ?>">
</form>

    </div>

    <script>
        // Défilement automatique vers le bas lorsque de nouveaux messages arrivent
        const chatMessages = document.querySelector('.chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    </script>
</body>
</html>