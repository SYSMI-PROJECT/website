<?php
// Inclure le fichier de connexion à la base de données
include __DIR__ . '/../../../requiments/PublicsData.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    // Vérifier si l'id de l'utilisateur à afficher est spécifié dans l'URL
    if (isset($_GET['id'])) {
        $userID = $_GET['id'];

        // Récupérer l'ID de l'utilisateur connecté depuis la session
        $loggedInUserID = $_SESSION['user_id']; // Assurez-vous que cette variable est présente dans la session

        // Récupérer les informations de l'utilisateur
        $sql = "SELECT u.id, u.nom, u.prenom, u.date_inscription, u.bio, u.email, u.lien_tiktok, u.lien_youtube, u.lien_snapchat, u.lien_instagram, u.lien_discord, u.lien_twitch,
                       u.verified, u.role, u.statut, p.image_content
                FROM utilisateur u 
                LEFT JOIN photos_de_profil p ON u.id = p.user_id 
                WHERE u.id = ?";
        $stmt = $conn->prepare($sql);

        // Exécuter la requête
        if ($stmt->execute([$userID])) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                // Récupérer et sécuriser les données de l'utilisateur
                $userName = htmlspecialchars((string)(($userData['prenom'] ?? '') . ' ' . ($userData['nom'] ?? '')));
                $userInscription = htmlspecialchars((string)($userData['date_inscription'] ?? 'Non disponible'));
                $userBio = nl2br(htmlspecialchars((string)($userData['bio'] ?? 'Non défini')));
                $userProfilePic = $userData['image_content'] ?? null;
                $userTikTok = htmlspecialchars((string)($userData['lien_tiktok'] ?? ''));
                $userYouTube = htmlspecialchars((string)($userData['lien_youtube'] ?? ''));
                $userSnapchat = htmlspecialchars((string)($userData['lien_snapchat'] ?? ''));
                $userInstagram = htmlspecialchars((string)($userData['lien_instagram'] ?? ''));
                $userDiscord = htmlspecialchars((string)($userData['lien_discord'] ?? ''));
                $userTwitch = htmlspecialchars((string)($userData['lien_twitch'] ?? ''));
                $userEmail = htmlspecialchars((string)($userData['email'] ?? 'Non disponible'));
                $accountActive = isset($userData['verified']) ? (bool)$userData['verified'] : false;
                $role = htmlspecialchars((string)($userData['role'] ?? 'standard'));
                $status = $userData['statut'] ?? 'actif';

                // Vérifier si l'utilisateur est banni
                if ($status === 'banni') {
                    header("Location: ../../Import/Error/Account_banned.php");
                    exit;
                }

                // Requête SQL pour compter le nombre d'amis
                $sql_count_amis = "SELECT 
                                    (SELECT COUNT(*) FROM relation WHERE demandeur = ? AND statut = 1) + 
                                    (SELECT COUNT(*) FROM relation WHERE receveur = ? AND statut = 1) AS nombre_amis";
                $stmt_count_amis = $conn->prepare($sql_count_amis);
                $stmt_count_amis->execute([$userID, $userID]);
                $result_count_amis = $stmt_count_amis->fetch(PDO::FETCH_ASSOC);
                $nombreAmis = $result_count_amis['nombre_amis'] ?? 0;

                // Vérifier le statut de la relation entre l'utilisateur connecté et celui affiché
                $sql_relation = "SELECT demandeur, receveur, statut FROM relation
                                 WHERE (demandeur = ? AND receveur = ?) OR (demandeur = ? AND receveur = ?)";
                $stmt_relation = $conn->prepare($sql_relation);
                $stmt_relation->execute([$loggedInUserID, $userID, $userID, $loggedInUserID]);
                $relation = $stmt_relation->fetch(PDO::FETCH_ASSOC);

                // Pour éviter le conflit de variable, on stocke le statut de la relation dans $relationStatus
                $relationStatus = isset($relation['statut']) ? intval($relation['statut']) : null;
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
                echo "<p>L'utilisateur avec l'ID " . htmlspecialchars((string)$userID) . " n'existe pas.</p>";
            }
        } else {
            echo "<p>Erreur lors de la récupération des informations de l'utilisateur.</p>";
        }
    } else {
        echo "<p>Paramètre d'ID d'utilisateur manquant dans l'URL.</p>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars((string)$e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Profil de l'utilisateur avec publications, informations et réseaux sociaux.">
    <link rel="icon" href="/Import/icons/Logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/public/import/css/navbar.css">
    <link rel="stylesheet" href="/public/import/css/pages/account.css">
    <title>Profil Utilisateur</title>
</head>
<body>
    <div class="container">
        <!-- En-tête du Profil -->
        <header class="profile-header" role="banner">
            <div class="avatar-container">
                <div class="avatar">
                    <?php if (!empty($userProfilePic)) : ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($userProfilePic); ?>" alt="Photo de Profil de <?= htmlspecialchars((string)$userName); ?>">
                    <?php else : ?>
                        <img src="https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg" alt="Photo de Profil par défaut">
                    <?php endif; ?>
                </div>
                <?php if ($userID == $loggedInUserID) : ?>
                    <div class="avatar-buttons">
                        <?php if (!empty($userProfilePic)) : ?>
                            <button class="edit-btn" onclick="window.location.href='/public/forms/account/change_pdp.html'">Changer</button>
                            <button class="edit-btn" onclick="window.location.href='/public/import/php/script/forms/account/delete_pdp.php'">Supprimer</button>
                        <?php else : ?>
                            <button class="edit-btn" onclick="window.location.href='/public/forms/account/add_pdp.html'">Ajouter</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="user-info">
                <h1><?= htmlspecialchars((string)$userName); ?></h1>
                <p class="bio"><?= htmlspecialchars((string)$userBio); ?></p>
                <p class="account-status <?= ($userData['lien_tiktok'] ?? '' ? 'private-status' : 'public-status'); ?>">
                    <i class="fas <?= ($userData['lien_tiktok'] ?? '' ? 'fa-lock' : 'fa-globe'); ?>"></i>
                    <?= ($userData['lien_tiktok'] ?? '' ? 'Compte Privé' : 'Compte Public'); ?>
                </p>
                <?php if ($userID == $loggedInUserID) : ?>
                    <a href="/public/forms/account/add_bio.html" class="edit-bio-btn">Modifier ma Bio</a>
                <?php endif; ?>
                <div class="level-bar">
                    <div class="level-bar-fill"></div>
                </div>
            </div>
        </header>

        <!-- Navigation par onglets -->
        <div class="tab-navigation" aria-label="Navigation des onglets">
            <button id="globalTab" class="tab active" onclick="showTab('global')" tabindex="0">Globale</button>
            <button id="publicationTab" class="tab" onclick="showTab('publication')" tabindex="0">Publication</button>
        </div>
		
        <div class="back-to-home">
            <a href="/index.php">Retour à l'accueil</a>
        </div>

        <!-- Contenu Global -->
        <main id="globalContainer" role="main">
            <!-- Statistiques -->
            <section class="stats">
                <div class="stat">
                    <h2><?= htmlspecialchars((string)$nombreAmis); ?></h2>
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
            </section>

            <!-- Badges -->
            <section class="badges">
                <h2 class="activity-title">Mes Badges</h2>
                <div class="badge-container">
                    <?php if ($accountActive) : ?>
                        <div class="badge"><i class="fas fa-check-circle"></i> Vérifié</div>
                    <?php endif; ?>
                    <?php if ($status === 'banni') : ?>
                        <div class="badge"><i class="fas fa-star"></i> Banni</div>
                    <?php endif; ?>
                    <?php if ($role === 'standard') : ?>
                        <div class="badge"><i class="fas fa-user" style="color: green;"></i> Standard</div>
                    <?php endif; ?>
                    <?php if ($role === 'staff') : ?>
                        <div class="badge"><i class="fas fa-user-shield" style="color: #ff4900;"></i> Staff</div>
                    <?php endif; ?>
                    <?php if ($role === 'premium') : ?>
                        <div class="badge" style="background-color: goldenrod;"><i class="fas fa-crown" style="color: #654200;"></i> Premium</div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Informations -->
            <section class="profile-section">
                <div class="edit_profile">
                    <h2>Informations</h2>
                    <?php if ($userID == $loggedInUserID) : ?>
                        <a href="/public/import/php/script/forms/account/info_visibility.php?id=<?= htmlspecialchars((string)$userID); ?>" class="edit-btn">
                            <i class="fa-solid fa-pen-to-square"></i> Modifier
                        </a>
                    <?php endif; ?>
                </div>
                <p>Inscrit le : <?= htmlspecialchars((string)$userInscription); ?></p>
                <?php if (!empty($userEmail)) : ?>
                    <p>Email : <?= htmlspecialchars((string)$userEmail); ?></p>
                <?php endif; ?>
            </section>

            <!-- Réseaux Sociaux -->
            <section class="profile-section social-media">
                <div class="edit_profile">
                    <h2>Réseaux Sociaux</h2>
                    <?php if ($userID == $loggedInUserID) : ?>
                        <a href="/public/forms/account/add_network.html" class="edit-btn">
                            <i class="fa-solid fa-pen-to-square"></i> Modifier
                        </a>
                    <?php endif; ?>
                </div>
                <?php if ($userTikTok || $userYouTube || $userSnapchat || $userInstagram || $userDiscord || $userTwitch) : ?>
                    <ul class="social-media-links">
                        <?php if ($userTikTok) : ?>
                            <li><a class="social-media-link" href="<?= htmlspecialchars((string)$userTikTok); ?>" target="_blank"><img src="https://logodownload.org/wp-content/uploads/2019/08/tiktok-logo-icon.png" alt="TikTok"></a></li>
                        <?php endif; ?>
                        <?php if ($userYouTube) : ?>
                            <li><a class="social-media-link" href="<?= htmlspecialchars((string)$userYouTube); ?>" target="_blank"><img src="https://clipartcraft.com/images/youtube-icon-clipart-square-4.png" alt="YouTube"></a></li>
                        <?php endif; ?>
                        <?php if ($userSnapchat) : ?>
                            <li><a class="social-media-link" href="<?= htmlspecialchars((string)$userSnapchat); ?>" target="_blank"><img src="https://pngimg.com/uploads/snapchat/snapchat_PNG15.png" alt="Snapchat"></a></li>
                        <?php endif; ?>
                        <?php if ($userInstagram) : ?>
                            <li><a class="social-media-link" href="<?= htmlspecialchars((string)$userInstagram); ?>" target="_blank"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Instagram_logo_2016.svg/1200px-Instagram_logo_2016.svg.png" alt="Instagram"></a></li>
                        <?php endif; ?>
                        <?php if ($userDiscord) : ?>
                            <li><a class="social-media-link" href="<?= htmlspecialchars((string)$userDiscord); ?>" target="_blank"><img src="https://wallpaperaccess.com/full/765574.jpg" alt="Discord"></a></li>
                        <?php endif; ?>
                        <?php if ($userTwitch) : ?>
                            <li><a class="social-media-link" href="<?= htmlspecialchars((string)$userTwitch); ?>" target="_blank"><img src="https://is4-ssl.mzstatic.com/image/thumb/Purple114/v4/3b/9c/11/3b9c112f-3d13-c7ab-5631-d425c97a400a/source/512x512bb.jpg" alt="Twitch"></a></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </main>

        <!-- Section Publications -->
        <section id="publicationContainer" class="hidden" aria-live="polite">
            <div class="publication-header">
                <?php if ($userID == $loggedInUserID) : ?>
                    <button class="new-publication-btn" onclick="window.location.href='../../formulaires/profil/nouvelle_publication.html'">
                        <i class="fas fa-plus"></i> Nouvelle Publication
                    </button>
                <?php endif; ?>
                <select class="sort-dropdown" aria-label="Trier les publications" id="sortDropdown" onchange="sortPublications()">
                    <option value="recent">Les plus récentes</option>
                    <option value="popular">Les plus populaires</option>
                </select>
            </div>
            <div class="profile-section">
                <h2>Publications</h2>
                <div class="publication-grid" id="publicationGrid">
                    <?php if (!empty($publications)) : ?>
                        <?php foreach ($publications as $publication) : 
                            // Récupérer les données de la publication
                            $mediaPath = htmlspecialchars((string)($publication['media_path'] ?? ''));
                            $extension = strtolower(pathinfo($mediaPath, PATHINFO_EXTENSION));
                        ?>
                            <div class="video-card">
                                <a href="../../publication.php?id=<?= htmlspecialchars((string)$userID); ?>&publication_id=<?= htmlspecialchars((string)$publication['id']); ?>" target="_blank">
                                    <?php if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) : ?>
                                        <img src="<?= $mediaPath; ?>" alt="Contenu Image" class="post-media">
                                    <?php elseif (in_array($extension, ['mp4', 'webm'])) : ?>
                                        <video autoplay loop muted class="post-media">
                                            <source src="<?= $mediaPath; ?>" type="video/<?= $extension; ?>">
                                            Votre navigateur ne supporte pas la lecture de vidéos.
                                        </video>
                                    <?php endif; ?>
                                </a>
                                <div class="overlay">
                                    <p>Publié le : <?= htmlspecialchars((string)($publication['date_creation'] ?? 'Date inconnue')); ?></p>
                                    <p><?= htmlspecialchars((string)($publication['description'] ?? '')); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>Aucune publication disponible.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <!-- Script de gestion des onglets et du tri -->
    <script>
        function showTab(tabName) {
            const globalContainer = document.getElementById('globalContainer');
            const publicationContainer = document.getElementById('publicationContainer');
            const globalTab = document.getElementById('globalTab');
            const publicationTab = document.getElementById('publicationTab');

            if (tabName === 'global') {
                globalContainer.style.display = 'block';
                publicationContainer.classList.add('hidden');
                globalTab.classList.add('active');
                publicationTab.classList.remove('active');
            } else {
                globalContainer.style.display = 'none';
                publicationContainer.classList.remove('hidden');
                publicationTab.classList.add('active');
                globalTab.classList.remove('active');
            }
        }

        // Gestion du tri des publications
        document.getElementById('sortDropdown').addEventListener('change', function() {
            const sortBy = this.value;
            console.log('Tri des publications par : ' + sortBy);
            // Vous pouvez ici déclencher une requête AJAX ou rediriger vers une URL avec un paramètre de tri.
        });
    </script>
</body>
</html>
