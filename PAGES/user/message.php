<?php
include '../../request/DB.php';
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
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <title>Messages Privés</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .chat-container {
            width: 100%;
            height: 100vh; /* Utilisation de la hauteur complète de l'écran */
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .chat-header {
            background: linear-gradient(90deg, #b400ff, #001c83);
            padding: 15px;
            color: white;
            text-align: center;
            position: relative;
        }

        .chat-header h2 {
            margin: 0;
            font-size: 1.5em;
            text-transform: capitalize;
        }

        .chat-header .back-btn {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 1.5em;
            cursor: pointer;
        }

        .chat-messages {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            background: #f3f3f3;
            display: flex;
            flex-direction: column;
            gap: 10px;
            scroll-behavior: smooth;
        }

        .message {
            display: flex;
            gap: 10px;
            max-width: 80%;
            padding: 10px;
            border-radius: 12px;
            font-size: 0.9em;
            line-height: 1.4;
            box-shadow: 0 2px 8px rgb(0 0 0 / 67%);
            transition: transform 0.2s ease;
        }

        .message.sent {
            align-self: flex-end;
        }

        .message.received {
            align-self: flex-start;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            background-color: #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.2em;
        }

        .message-content {
            display: flex;
            flex-direction: column;
        }

        .message-author {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.8em;
            color: #666;
        }

        .message-date {
            font-size: 0.7em;
            color: #aaa;
        }

        .chat-input {
            padding: 10px;
            display: flex;
            gap: 10px;
            background: linear-gradient(90deg, #b400ff, #001c83);
            border-top: 1px solid #eee;
        }

        .chat-input textarea {
            flex-grow: 1;
            border: none;
            padding: 10px;
            border-radius: 8px;
            resize: none;
            font-size: 0.9em;
            background: #f3f3f3;
            font-family: sans-serif;
        }

        .chat-input textarea:focus {
            outline: none;
        }

        .send-button {
            width: 40px;
            height: 40px;
            background: #FF4757;
            color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background 0.3s;
        }

        .send-button:hover {
            background: #ff6f61;
        }

        .empty-chat {
            text-align: center;
            font-size: 1.2em;
            color: #888;
        }

        /* Adaptation mobile */
        @media (max-width: 600px) {
            .chat-header h2 {
                font-size: 1.2em;
            }

            .message {
                font-size: 0.8em;
            }

            .chat-input textarea {
                font-size: 0.85em;
            }

            .message-content {
                font-size: 0.85em;
            }

            .message-author {
                font-size: 0.75em;
            }
        }

    </style>
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
            // Vérifier si le référent est disponible et ne correspond pas à l'URL actuelle
            if (document.referrer && document.referrer !== window.location.href) {
                window.history.back();
            } else {
                // Redirection vers la liste des messages si le référent n'est pas disponible
                window.location.href = 'MessageList.php';  // Assurez-vous que le chemin est correct
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
            <input type="hidden" name="receiver_id" value="<?= $receiverID ?>">
            <button type="submit" class="send-button"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>

    <script>
        // Défilement automatique vers le bas lorsque de nouveaux messages arrivent
        const chatMessages = document.querySelector('.chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    </script>
</body>
</html>
