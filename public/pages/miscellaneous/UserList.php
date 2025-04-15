<?php
session_start();
include __DIR__ . '/../../../requiments/UsersData.php';

if (isset($_SESSION['user_id'])) {
    $userID = $_SESSION['user_id'];

    $secret_key = 'votre_cle_secrete';

    $loggedInUserID = $_SESSION['user_id'];
    function generateHashedId($id, $key)
    {
        return hash_hmac('sha256', $id, $key);
    }
    
    $searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';

    $query = "SELECT u.id, u.verified, u.nom, u.bio, u.prenom, u.role, p.image_content, u.lien_tiktok, u.lien_youtube, u.lien_snapchat, u.lien_instagram, u.lien_discord, u.lien_twitch
    FROM utilisateur u
    LEFT JOIN photos_de_profil p ON u.id = p.user_id
    WHERE u.prenom LIKE ?";
    $stmt = $conn->prepare($query);
    $stmt->execute(["%$searchKeyword%"]);

    $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    header('Location: ../../formulaires/login.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/public/img/icon/Logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="/public/import/css/pages/users.css">
    <title>Liste utilisateurs</title>
</head>
<body>
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Rechercher par prénom, nom ou réseau social...">
        <a href="/index.php" class="btn">
            <i class="fas fa-home"></i>
        </a>
    </div>
    <div class="container">
        <?php foreach ($usersData as $userData) : ?>
            <div class="user-card">
                <div class="profile-pic">
                    <!-- Lien autour de l'image de profil -->
                    <a href="/public/import/php/account.php?id=<?= htmlspecialchars($userData['id']) ?>">
                        <?php if ($userData['image_content']) : ?>
                            <img src="<?= $userData['image_content'] ? 'data:image/jpeg;base64,' . base64_encode($userData['image_content']) : 'https://via.placeholder.com/100' ?>" alt="Photo de profil">
                        <?php else : ?>
                            <img src="https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg" alt="Avatar utilisateur">
                        <?php endif; ?>
                    </a>
                    <div class="user-name text">
                        <h2><?= htmlspecialchars($userData['prenom']) ?></h2>
                        <p><?= htmlspecialchars($userData['nom']) ?></p>
                        <div class="badges-container">
                            <?php if (!empty($userData['verified'])) : ?>
                                <div class="badge verified"><i class="fas fa-check-circle"></i> Vérifié</div>
                            <?php endif; ?>
                            <?php if (!empty($userData['role'])) : ?>
                                <?php if ($userData['role'] == 'standard') : ?>
                                    <div class="badge standard"><i class="fas fa-user" style="color: green;"></i> Standard</div>
                                <?php elseif ($userData['role'] == 'premium') : ?>
                                    <div class="badge premium" style="background-color: goldenrod;"><i class="fas fa-crown" style="color: #654200;"></i> Premium</div>
                                <?php elseif ($userData['role'] == 'staff') : ?>
                                    <div class="badge staff"><i class="fas fa-user-shield" style="color: #ff4900;"></i> Staff</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="user-info">
                    <p class="bio"><?= htmlspecialchars($userData['bio'] ?? 'Aucune bio') ?></p>
                    <div class="social-links">
                        <?php if ($userData['lien_tiktok']) : ?>
                            <a href="<?= htmlspecialchars($userData['lien_tiktok']) ?>" class="social-icon reseau-link" aria-label="TikTok">
                                <img src="https://purepng.com/public/uploads/large/tik-tok-logo-6fh.png" alt="TikTok">
                            </a>
                        <?php endif; ?>
                        <?php if ($userData['lien_youtube']) : ?>
                            <a href="<?= htmlspecialchars($userData['lien_youtube']) ?>" class="social-icon reseau-link" aria-label="YouTube">
                                <img src="https://clipartcraft.com/images/youtube-icon-clipart-square-4.png" alt="YouTube">
                            </a>
                        <?php endif; ?>
                        <?php if ($userData['lien_snapchat']) : ?>
                            <a href="<?= htmlspecialchars($userData['lien_snapchat']) ?>" class="social-icon reseau-link" aria-label="Snapchat">
                                <img src="https://pngimg.com/uploads/snapchat/snapchat_PNG15.png" alt="Snapchat">
                            </a>
                        <?php endif; ?>
                        <?php if ($userData['lien_instagram']) : ?>
                            <a href="<?= htmlspecialchars($userData['lien_instagram']) ?>" class="social-icon reseau-link" aria-label="Instagram">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Instagram_logo_2016.svg/1200px-Instagram_logo_2016.svg.png" alt="Instagram">
                            </a>
                        <?php endif; ?>
                        <?php if ($userData['lien_discord']) : ?>
                            <a href="<?= htmlspecialchars($userData['lien_discord']) ?>" class="social-icon reseau-link" aria-label="Discord">
                                <img src="https://wallpaperaccess.com/full/765574.jpg" alt="Discord">
                            </a>
                        <?php endif; ?>
                        <?php if ($userData['lien_twitch']) : ?>
                            <a href="<?= htmlspecialchars($userData['lien_twitch']) ?>" class="social-icon reseau-link" aria-label="Twitch">
                                <img src="https://is4-ssl.mzstatic.com/image/thumb/Purple114/v4/3b/9c/11/3b9c112f-3d13-c7ab-5631-d425c97a400a/source/512x512bb.jpg" alt="Twitch">
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="action-buttons">
    <?php
    $sql_relation = "SELECT demandeur, receveur, statut, bloqueur 
                     FROM relation 
                     WHERE (demandeur = ? AND receveur = ?) OR (demandeur = ? AND receveur = ?)";
    $stmt_relation = $conn->prepare($sql_relation);
    $stmt_relation->execute([$loggedInUserID, $userData['id'], $userData['id'], $loggedInUserID]);
    $relation = $stmt_relation->fetch(PDO::FETCH_ASSOC);

    $relationStatus = $relation['statut'] ?? null;
    $blocker = $relation['bloqueur'] ?? null;

    if (is_null($relationStatus)) : ?>
        <form action="/public/import/php/script/Friends/send_friend.php" method="POST" class="action-icon">
            <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($userData['id']) ?>">
            <button type="submit" class="icon success" aria-label="Ajouter un ami">
                <i class="fas fa-user-plus"></i>
            </button>
        </form>
    <?php elseif ($relationStatus === 0) : ?>
        <?php if ($loggedInUserID == $relation['receveur']) : ?>
            <!-- Boutons pour accepter ou refuser la demande -->
            <form action="/public/import/php/script/Friends/accept_friend.php" method="POST" class="action-icon">
                <input type="hidden" name="requester_id" value="<?= htmlspecialchars($userData['id']) ?>">
                <button type="submit" class="icon success" aria-label="Accepter la demande">
                    <i class="fas fa-check"></i>
                </button>
            </form>
            <form action="/public/import/php/script/Friends/reject_friend.php" method="POST" class="action-icon">
                <input type="hidden" name="requester_id" value="<?= htmlspecialchars($userData['id']) ?>">
                <button type="submit" class="icon danger" aria-label="Refuser la demande">
                    <i class="fas fa-times"></i>
                </button>
            </form>
        <?php elseif ($loggedInUserID == $relation['demandeur']) : ?>
            <!-- Message de demande en attente ou annulation -->
            <span class="icon pending" title="Demande en attente">
                <i class="fas fa-hourglass-half"></i>
            </span>
            <form action="/public/import/php/script/Friends/cancel_friend.php" method="POST" class="action-icon">
                <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($userData['id']) ?>">
                <button type="submit" class="icon danger" aria-label="Annuler la demande">
                    <i class="fas fa-times-circle"></i>
                </button>
            </form>
        <?php endif; ?>
    <?php elseif ($relationStatus === 1) : ?>
        <!-- Formulaire pour supprimer un ami et envoyer un message -->
        <form action="/public/import/php/script/Friends/remove_friend.php" method="POST" class="action-icon">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($userData['id']) ?>">
            <button type="submit" class="icon danger" aria-label="Supprimer l'ami">
                <i class="fas fa-user-times"></i>
            </button>
        </form>
        <form action="message.php" class="action-icon">
            <input type="hidden" name="id" value="<?= htmlspecialchars($userData['id']) ?>">
            <button type="submit" class="icon info" aria-label="Envoyer un message">
                <i class="fas fa-envelope"></i>
            </button>
        </form>
        <!-- Formulaire pour bloquer l'utilisateur -->
        <form action="/public/import/php/script/Friends/block_friend.php" method="POST" class="action-icon">
            <input type="hidden" name="friend_id" value="<?= htmlspecialchars($userData['id']) ?>">
            <input type="hidden" name="action" value="block"> <!-- Action pour bloquer -->
            <button type="submit" class="icon warning" aria-label="Bloquer l'utilisateur">
                <i class="fas fa-ban"></i>
            </button>
        </form>
    <?php elseif ($relationStatus === 2) : ?>
        <!-- Si la relation est bloquée -->
        <?php if ($blocker == $loggedInUserID) : ?>
            <!-- Bouton pour débloquer si l'utilisateur a bloqué l'autre -->
            <form action="/public/import/php/script/Friends/block_friend.php" method="POST" class="action-icon">
    <input type="hidden" name="friend_id" value="<?= htmlspecialchars($userData['id']) ?>"> <!-- ID de l'ami -->
    <input type="hidden" name="action" value="unblock"> <!-- Action pour débloquer -->
    <button type="submit" class="icon success" aria-label="Débloquer l'utilisateur">
        <i class="fas fa-unlock"></i> Débloquer</button>
            </form>
        <?php else : ?>
            <span class="icon blocked" title="Vous avez été bloqué par cet utilisateur">
                Vous avez été bloqué
            </span>
        <?php endif; ?>
    <?php endif; ?>
</div>

</div>
    </div>
        <?php endforeach; ?>
    </div>
    <script src="/public/import/js/UserSearch.js"></script>
</body>

</html>