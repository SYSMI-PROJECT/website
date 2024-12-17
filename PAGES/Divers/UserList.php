<?php
session_start();

include '../../request/DB.php';


// Vérifier si la session est démarrée
if (isset($_SESSION['user_id'])) {
    $userID = $_SESSION['user_id'];

    // Clé secrète pour générer le hachage
    $secret_key = 'votre_cle_secrete';

    // Récupérer l'ID de l'utilisateur connecté
    $loggedInUserID = $_SESSION['user_id']; // Assurez-vous d'avoir cette variable dans votre session
    // Fonction pour générer le hachage de l'ID
    function generateHashedId($id, $key)
    {
        return hash_hmac('sha256', $id, $key);
    }


    // Rechercher un prénom spécifique si la recherche est soumise
    $searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';

    // Modifier la requête SQL pour inclure la recherche par prénom
    $query = "SELECT u.id, u.VIP, u.verified, u.nom, u.bio, u.prenom, p.image_content, u.lien_tiktok, u.lien_youtube, u.lien_snapchat, u.lien_instagram, u.lien_discord, u.lien_twitch
              FROM utilisateur u
              LEFT JOIN photos_de_profil p ON u.id = p.user_id
              WHERE u.prenom LIKE ?";
    $stmt = $conn->prepare($query);
    $stmt->execute(["%$searchKeyword%"]);

    $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Gérer le cas où la clé 'user_id' n'est pas définie dans la session
    header('Location: ../../formulaires/login.html'); // Rediriger l'utilisateur vers la page de connexion
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Liste utilisateurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            background: linear-gradient(-45deg, #a30000, #7500ff);
            background-attachment: fixed;
        }

        .search-container {
            margin: 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-container input {
            width: 80%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-shadow: -1px 5px 8px rgb(26 26 26);
        }

        .search-container .btn {
            display: inline-flex;
            align-items: center;
            padding: 10px;
            margin-left: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-size: 1em;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: -1px 5px 8px rgb(26 26 26);
        }

        .search-container .btn:hover {
            background-color: #0056b3;
        }


        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
            margin: auto;
            border-radius: 5px;
        }

        .user-card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 20px;
            box-shadow: -1px 5px 8px rgb(26 26 26);
            overflow: hidden;
            transition: transform 0.2s;
            margin: 10px;
            border: solid 1px;
            width: 600px;
            position: relative;
        }

        .user-card.hidden {
            display: none;
        }

        .user-card:hover {
            transform: scale(1.02);
        }

        .profile-pic {
            display: flex;
            align-items: center;
            background: #eee;
            padding: 20px;
            position: relative;
        }

        .profile-pic a {
            display: block;
        }

        .profile-pic img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: solid 2px dodgerblue;
            padding: 2px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-pic img:hover {
            transform: scale(1.10);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .user-name {
            margin-left: 20px;
            position: relative;
        }

        .user-name h2 {
            font-size: 1.2em;
            color: #a0f;
        }

        .user-info {
            padding: 20px;
        }

        .bio {
            margin: 10px 0;
        }

        .social-links {
            display: flex;
            gap: 10px;
        }

        .social-icon img {
            width: 30px;
            height: 30px;
            transition: transform 0.3s ease;
            border-radius: 6px;
            background-color: #0007ff66;
            border: solid 1px red;
            transition: transform 0.3s ease;
        }

        .social-icon img:hover {
            transform: scale(1.2);
            box-shadow: 0 4px 8px rgb(0 0 0);
        }

        .view-profile {
            display: inline-block;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #007bff;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            text-align: center;
            font-size: 0.9em;
            font-weight: bold;
            position: absolute;
            right: 20px;
            bottom: 20px;
        }

        .view-profile:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        @media (max-width: 600px) {

            .profile-pic img {
                width: 80px;
                height: 80px;
            }
        }

        .badges-container {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .badge {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 5px 10px;
            font-size: 13px;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: -1px 5px 8px rgb(26 26 26);
        }

        .badge i {
            color: #0041ff;
        }

/* Conteneur des boutons d'action */
.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    align-items: center;
}

/* Styles généraux pour les boutons */
.icon {
    color: #007bff;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.2em;
    transition: transform 0.3s ease, color 0.3s ease;
}

.icon:hover {
    transform: translateY(-3px);
}

/* Classes de variante */
.icon.success {
    color: green;
}

.icon.success:hover {
    color: #0a6616;
}

.icon.danger {
    color: red;
}

.icon.danger:hover {
    color: #a80000;
}

.icon.info {
    color: blue;
}

.icon.info:hover {
    color: #0056b3;
}

.icon.warning {
    color: gray;
}

.icon.warning:hover {
    color: #505050;
}

.icon.pending {
    color: orange;
}

.icon.blocked {
    color: gray;
    font-style: italic;
}

    </style>
</head>

<body>
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Rechercher par prénom, nom ou réseau social...">
        <a href="../../index.php" class="btn">
            <i class="fas fa-home"></i>
        </a>
    </div>
    <div class="container">
        <?php foreach ($usersData as $userData) : ?>
            <div class="user-card">
                <div class="profile-pic">
                    <!-- Lien autour de l'image de profil -->
                    <a href="../user/profile.php?id=<?= htmlspecialchars($userData['id']) ?>">
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
                                <div class="badge"><i class="fas fa-check-circle"></i> Vérifié</div>
                            <?php endif; ?>
                            <?php if (!empty($userData['VIP'])) : ?>
                                <div class="badge"><i class="fas fa-crown"></i> VIP</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="user-info">
                    <p class="bio"><?= htmlspecialchars($userData['bio']) ?></p>
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
    // Vérification de la relation entre les utilisateurs
    $sql_relation = "SELECT demandeur, receveur, statut, bloqueur 
                     FROM relation 
                     WHERE (demandeur = ? AND receveur = ?) OR (demandeur = ? AND receveur = ?)";
    $stmt_relation = $conn->prepare($sql_relation);
    $stmt_relation->execute([$loggedInUserID, $userData['id'], $userData['id'], $loggedInUserID]);
    $relation = $stmt_relation->fetch(PDO::FETCH_ASSOC);

    $relationStatus = $relation['statut'] ?? null;
    $blocker = $relation['bloqueur'] ?? null;

    // Si aucune relation n'existe
    if (is_null($relationStatus)) : ?>
        <form action="../../traitements/Friend_list/send_friend.php" method="POST" class="action-icon">
            <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($userData['id']) ?>">
            <button type="submit" class="icon success" aria-label="Ajouter un ami">
                <i class="fas fa-user-plus"></i>
            </button>
        </form>
    <?php elseif ($relationStatus === 0) : ?>
        <?php if ($loggedInUserID == $relation['receveur']) : ?>
            <!-- Boutons pour accepter ou refuser la demande -->
            <form action="../../traitements/Friend_list/accept_friend.php" method="POST" class="action-icon">
                <input type="hidden" name="requester_id" value="<?= htmlspecialchars($userData['id']) ?>">
                <button type="submit" class="icon success" aria-label="Accepter la demande">
                    <i class="fas fa-check"></i>
                </button>
            </form>
            <form action="../../traitements/Friend_list/reject_friend.php" method="POST" class="action-icon">
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
            <form action="../../traitements/Friend_list/cancel_friend.php" method="POST" class="action-icon">
                <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($userData['id']) ?>">
                <button type="submit" class="icon danger" aria-label="Annuler la demande">
                    <i class="fas fa-times-circle"></i>
                </button>
            </form>
        <?php endif; ?>
    <?php elseif ($relationStatus === 1) : ?>
        <!-- Formulaire pour supprimer un ami et envoyer un message -->
        <form action="../../traitements/Friend_list/cancel_friend.php" method="POST" class="action-icon">
            <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($userData['id']) ?>">
            <button type="submit" class="icon danger" aria-label="Supprimer l'ami">
                <i class="fas fa-user-times"></i>
            </button>
        </form>
        <form action="../user/message.php" class="action-icon">
            <input type="hidden" name="id" value="<?= htmlspecialchars($userData['id']) ?>">
            <button type="submit" class="icon info" aria-label="Envoyer un message">
                <i class="fas fa-envelope"></i>
            </button>
        </form>
        <!-- Formulaire pour bloquer l'utilisateur -->
        <form action="../../traitements/Friend_list/block_friend.php" method="POST" class="action-icon">
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
            <form action="../../traitements/Friend_list/block_friend.php" method="POST" class="action-icon">
    <input type="hidden" name="friend_id" value="<?= htmlspecialchars($userData['id']) ?>"> <!-- ID de l'ami -->
    <input type="hidden" name="action" value="unblock"> <!-- Action pour débloquer -->
    <button type="submit" class="icon success" aria-label="Débloquer l'utilisateur">
        <i class="fas fa-unlock"></i> Débloquer</button>
</form>
        <?php else : ?>
            <!-- Message pour l'utilisateur bloqué -->
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
    <script src="../../Import/js/UserSearch.js"></script>
</body>

</html>