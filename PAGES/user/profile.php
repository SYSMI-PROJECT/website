<?php
// Inclure le fichier UserPublic contenant la logique principale et la connexion à la base de données
include '../../request/UserPublic.php';

try {
    // Vérifier que $userID est défini et valide avant d'exécuter la requête
    if (isset($userID) && is_numeric($userID)) {
        // Préparer la requête pour récupérer le statut display_info
        $sql = "SELECT display_info FROM utilisateur WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userID]);

        // Récupérer le résultat
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Assigner la valeur, avec une valeur par défaut de 1 si aucun résultat n'est trouvé
        $currentDisplayInfo = $userData['display_info'] ?? 1;
    } else {
        // Gestion d'erreur si $userID n'est pas défini ou valide
        $currentDisplayInfo = 1; // Valeur par défaut
        throw new Exception("ID utilisateur non valide ou non défini.");
    }
} catch (Exception $e) {
    // En cas d'erreur, afficher un message ou logger l'erreur
    echo "Erreur : " . $e->getMessage();
    $currentDisplayInfo = 1; // Valeur par défaut en cas d'erreur
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Profil Utilisateur</title>
    <style>
        /* Styles généraux */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: radial-gradient(circle, #021265, #691430);
            color: #333;
        }

        .profile-container {
            max-width: 1200px;
            margin: 50px auto;
            background: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            overflow: hidden;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* En-tête du profil */
        .profile-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            background: linear-gradient(135deg, #0078D7, #00CFFF);
            color: #fff;
            padding: 30px 20px;
        }

        /* Conteneur de l'avatar et des boutons */
        .avatar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        /* Avatar */
        .avatar {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .avatar:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Conteneur des boutons */
        .avatar-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        /* Styles pour les boutons */
        .edit-btn {
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: background 0.3s ease;
        }

        .edit-btn:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        /* Info utilisateur */
        .user-info {
            flex: 1;
            margin-left: 20px;
        }

        .user-info h1 {
            font-size: 2.2rem;
            margin: 0;
        }

        .user-info .bio {
            font-size: 1rem;
            margin: 10px 0;
            color: rgba(255, 255, 255, 0.8);
        }

        .level-bar {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 15px;
            height: 10px;
            width: 80%;
            position: relative;
        }

        .level-bar-fill {
            height: 100%;
            background: #00FF77;
            width: 70%;
            transition: width 0.5s ease;
        }

        /* Lien modifier bio */
        .edit-bio-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background: #ff6600;
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
        }

        .edit-bio-btn:hover {
            background: #ff8c1a;
        }

        /* Statistiques */
        .stats {
            display: flex;
            justify-content: space-around;
            padding: 20px;
            background: #f9f9fc;
            border-bottom: 1px solid #ddd;
        }

        .stat {
            text-align: center;
            position: relative;
            transition: transform 0.3s ease;
        }

        .stat:hover {
            transform: scale(1.05);
        }

        .stat h2 {
            font-size: 2.2rem;
            margin: 0;
            color: #0078D7;
        }

        .stat p {
            margin: 5px 0 0;
            font-size: 0.9rem;
            color: #555;
        }

        /* Badges */
        .badges {
            padding: 20px;
            background: #fafafa;
        }

        .badge-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
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
            transition: transform 0.3s ease;
        }

        .badge i {
            color: #0041ff;
        }
        
        .badge:hover {
            transform: scale(1.2);
        }

        /* Activité récente */
        .activity {
            padding: 20px;
        }

        .activity-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .activity-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .activity-item img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
            border-radius: 50%;
        }

        .activity-item span {
            font-size: 1rem;
            color: #333;
        }

        .profile-section {
            margin: 0px 0px;
            padding: 20px;
        }
        
        .profile-section h2 {
            margin-bottom: 10px;
            font-size: 20px;
            color: #333;
        }
        
        .profile-section p {
            font-size: 16px;
            color: #666;
        }

        /* Bouton retour à l'accueil */
        .back-to-home {
            text-align: center;
            padding: 20px;
            display: flex;
            flex-direction: column-reverse;
        }

        .back-to-home a {
            background: linear-gradient(135deg, #008b18, #00651a);
            color: #fff;
            padding: 5px 10px;
            font-size: 1.1rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        }

        .back-to-home a:hover {
            background: linear-gradient(135deg, #00CFFF, #0078D7);
            transform: scale(1.01);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
        }

        .back-to-home a:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 120, 215, 0.5);
        }

        .social-media-links {
            padding: 10px;
            margin: 0px;
            box-shadow: 0 4px 6px rgb(0 0 0);
            border-radius: 5px;
            display: flex;
            gap: 20px;
        }

        .social-media-links li {
            display: inline;
         }
        
         .social-media-link img {
            width: 30px;
            height: 30px;
            display: flex;
            border-radius: 6px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

       .social-media-link img:hover {
            transform: scale(1.2);
            box-shadow: 0 4px 8px rgb(0 0 0);
        }

        .edit_profile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 15px;
        }

        .edit_profile h2 {
        font-size: 1.5rem;
        margin: 0;
        color: #333;
        }

        .edit_profile .edit-btn {
            background: #0078D7;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            transition: background 0.3s ease;
        }

        .edit_profile .edit-btn:hover {
            background: #005bb5;
            color: #fff;
        }


        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-container {
                border-radius: 0px;
                margin: 0px auto;

            }

            .avatar-container {
                flex-direction: column;
                align-items: center;
            }

            .user-info {
                margin-left: 0;
            }

            .stat {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <!-- En-tête du profil -->
        <div class="profile-header">
            <div class="avatar-container">
                <div class="avatar">
                    <?php if ($userProfilePic) : ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($userProfilePic); ?>" alt="Photo de Profil">
                    <?php else : ?>
                        <img src="https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg" alt="Photo de Profil par défaut">
                    <?php endif; ?>
                </div>
                <?php if ($userID == $loggedInUserID) : ?>
                <div class="avatar-buttons">
                    <?php if ($userProfilePic) : ?>
                        <button class="edit-btn edit-avatar-btn" onclick="window.location.href='../../formulaires/profil/change_pdp.html'">Changer</button>
                        <button class="edit-btn edit-avatar-btn" onclick="window.location.href='../../traitements/Formulaires/delete_pdp.php'">Supprimer</button>
                    <?php else : ?>
                        <button class="edit-btn edit-avatar-btn" onclick="window.location.href='../../formulaires/profil/add_pdp.html'">Ajouter</button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="user-info">
                <h1><?php echo htmlspecialchars($userName); ?></h1>
                <p class="bio"><?php echo $userBio; ?></p>
                <?php if ($userID == $loggedInUserID) : ?>
                <a href="../../formulaires/profil/bio.html" class="edit-bio-btn">Modifier ma Bio</a>
                <?php endif; ?>

                <div class="level-bar">
                    <div class="level-bar-fill"></div>
                </div>
            </div>
        </div>

        <div class="back-to-home">
            <a href="../../index.php" class="edit-btn">Retour à l'accueil</a>
        </div>

        <!-- Statistiques -->
        <div class="stats">
            <div class="stat">
                <h2><?php echo $nombreAmis; ?></h2>
                <p>Amis</p>
            </div>
            <div class="stat">
                <h2>42</h2>
                <p>Projets</p>
            </div>
            <div class="stat">
                <h2>12</h2>
                <p>Badges</p>
            </div>
        </div>

        <!-- Badges -->
        <div class="badges">
            <h2 class="activity-title">Mes Badges</h2>
            <div class="badge-container">
                <?php if ($accountActive) : ?>
                    <div class="badge"><i class="fas fa-check-circle"></i> Vérifié</div>
                <?php endif; ?>
                <?php if ($VIP) : ?>
                    <div class="badge"><i class="fas fa-crown"></i> VIP</div>
                <?php endif; ?>
            </div>
        </div>

<div class="profile-section">
    <div class="edit_profile">
        <h2>Informations</h2>
        <?php if ($userID == $loggedInUserID) : ?>
            <a href="../../formulaires/profil/informations.php?id=<?php echo htmlspecialchars($userID); ?>" class="edit-btn">
                <i class="fa-solid fa-pen-to-square"></i> Modifier
            </a>
        <?php endif; ?>
    </div>

    <?php if ($currentDisplayInfo == 1) : ?>
        <p>Inscrit le : <?php echo htmlspecialchars($userInscription); ?></p>
        <?php if (!empty($userEmail)) : ?>
            <p>Email : <?php echo htmlspecialchars($userEmail); ?></p>
        <?php endif; ?>
    <?php else : ?>
        <p>Ces informations sont actuellement cachées.</p>
    <?php endif; ?>
</div>



        <div class="profile-section social-media">
    <div class="edit_profile">
        <h2>Réseaux Sociaux</h2>
        <?php if ($userID == $loggedInUserID) : ?>
        <a href="../../formulaires/profil/sociaux.html" class="edit-btn">
            <i class="fa-solid fa-pen-to-square"></i> Modifier
        </a>
        <?php endif; ?>
    </div>
    <?php if ($userID == $loggedInUserID) : ?>
        <!-- Ajout d'autres contenus pour l'utilisateur connecté -->
    <?php endif; ?>
    <?php if ($userTikTok || $userYouTube || $userSnapchat || $userInstagram || $userDiscord) : ?>
        <ul class="social-media-links">
            <?php if ($userTikTok) : ?>
                <li><a class="social-media-link" href="<?php echo $userTikTok; ?>" target="_blank"><img src="https://logodownload.org/wp-content/uploads/2019/08/tiktok-logo-icon.png" alt="TikTok"></a></li>
            <?php endif; ?>
            <?php if ($userYouTube) : ?>
                <li><a class="social-media-link" href="<?php echo $userYouTube; ?>" target="_blank"><img src="https://clipartcraft.com/images/youtube-icon-clipart-square-4.png" alt="YouTube"></a></li>
            <?php endif; ?>
            <?php if ($userSnapchat) : ?>
                <li><a class="social-media-link" href="<?php echo $userSnapchat; ?>" target="_blank"><img src="https://pngimg.com/uploads/snapchat/snapchat_PNG15.png" alt="Snapchat"></a></li>
            <?php endif; ?>
            <?php if ($userInstagram) : ?>
                <li><a class="social-media-link" href="<?php echo $userInstagram; ?>" target="_blank"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Instagram_logo_2016.svg/1200px-Instagram_logo_2016.svg.png" alt="Instagram"></a></li>
            <?php endif; ?>
            <?php if ($userDiscord) : ?>
                <li><a class="social-media-link" href="<?php echo $userDiscord; ?>" target="_blank"><img src="https://wallpaperaccess.com/full/765574.jpg" alt="Discord"></a></li>
            <?php endif; ?>
            <?php if ($userTwitch) : ?>
            <li><a class="social-media-link" href="<?php echo $userTwitch; ?>" target="_blank"><img src="https://is4-ssl.mzstatic.com/image/thumb/Purple114/v4/3b/9c/11/3b9c112f-3d13-c7ab-5631-d425c97a400a/source/512x512bb.jpg" alt="Discord"></a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
    </div>  
</body>
</html>
