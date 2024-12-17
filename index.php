<?php
session_start();

include 'request/UserInfo.php';
include 'request/count_friend.php';

$isUserLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="Import/icons/Logo.png">
    <title>SYSMI PROJECT</title>
    <link rel="stylesheet" href="Import/css/navbar.css?v=0.1">
    <link rel="stylesheet" href="Import/css/index.css?v=0">
    <style>


    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-logo">
        <a href="menu.php" target="_self">
            <img src="Import/icons/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
        </a>
    </div>
    <div class="navbar-icons">
        <a href="https://discord.gg/6RPQn4NKuT" target="_blank">
            <i class="fab fa-discord navbar-icon" title="Discord" style="color: #0070ff;"></i>
        </a>
        <div class="profile-container">
            <a href="PAGES/user/profile.php?id=<?php echo $user_id; ?>" target="_self">
                <?php if (!empty($image_content)): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($image_content); ?>" alt="Profile Picture" class="profile-picture">
                <?php else: ?>
                    <img src="https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg" alt="Profile Picture" class="profile-picture">
                <?php endif; ?>
            </a>
        </div>
    </div>
</div>

    <!-- Hero Section -->
    <section class="hero">
        <h2>Bienvenue sur La SYSMI PROJECT</h2>
        <p>Découvrez un réseau social innovant où les communautés se rassemblent, échangent et collaborent dans un environnement bienveillant.</p>
        <a href="/frefgrfg" class="btn">Rejoignez le Réseau</a>
    </section>

    <section class="section" id="point" style="padding: 10px 20px;">
        <h3>Vos points</h3>
            <div class="itembar">
                <?php include 'request/itembar.php'; ?>
            </div>
        </div>
    </section>

    <section class="section" id="links">
        <h3>Liens Utiles</h3>
        <div class="links">
        <div class="link-card">
                <a href="PAGES/Divers/UserList.php">
                    <img src="Import/icons/Users.png" alt="Discord Icon">
                    <h4>Liste utilisateur</h4>
                </a>
            </div>
            <div class="link-card">
                <a href="PAGES/user/MessageList.php">
                    <img src="https://cdn1.iconfinder.com/data/icons/materia-office-vol-1/24/010_021_lock_private_mail_email_envelope_message-512.png" alt="Events Icon">
                    <h4>Message Privé</h4>
                </a>
            </div>
            <div class="link-card">
            <?php if ($nbDemandes > 0): ?><span class="badge"><?php echo $nbDemandes; ?></span><?php endif; ?>
                <a href="PAGES/user/FriendList.php">
                    <img src="https://urls.fr/9qv4YB" alt="Communities Icon">
                    <h4>Demande d'amis</h4>
                </a>
            </div>
            <div class="link-card">
                <a href="PAGES/Divers/boutique.php">
                    <img src="https://th.bing.com/th/id/R.c47b4aee008882b8799e20b3b1d63d35?rik=sz%2bsSxfMg3MBBA&riu=http%3a%2f%2fgetdrawings.com%2ffree-icon-bw%2fhand-sanitizer-icon-21.png&ehk=OvUtMUFmv4ByrWA8DdmuHVo3MhX%2bRqpTUnSHrznr8oI%3d&risl=&pid=ImgRaw&r=0" alt="Events Icon">
                    <h4>Boutique</h4>
                </a>
            </div>
            <div class="link-card">
                <a href="PAGES/Games/home-menu.php">
                    <img src="https://urls.fr/CRtnto" alt="Games Icon">
                    <h4>Mini jeux</h4>
                </a>
            </div>
            <?php if (!empty($VIP)): ?>
            <div class="link-card">
                <a href="PAGES/STAFF/Dashboard.php">
                    <img src="Import/icons/VIP.png" alt="Games Icon">
                    <h4>STAFF</h4>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>


<!-- Testimonials Section -->
<section class="section" id="testimonials">
    <h3>Partagez votre avis sur La SYSMI PROJECT</h3>
    <form id="feedback-form" class="feedback-form" method="POST" action="traitements/Formulaires/feedback.php">
        <!-- Champ caché pour l'ID utilisateur -->
        <input type="hidden" id="user-id" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

        <label for="feedback">Votre avis :</label>
        <textarea id="feedback" name="feedback" placeholder="Partagez votre expérience ici..." rows="4" required></textarea>

        <label for="rating">Votre note :</label>
        <select id="rating" name="rating" required>
            <option value="">-- Choisissez une note --</option>
            <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
            <option value="4">⭐⭐⭐⭐ - Très bien</option>
            <option value="3">⭐⭐⭐ - Correct</option>
            <option value="2">⭐⭐ - Médiocre</option>
            <option value="1">⭐ - Mauvais</option>
        </select>

        <button type="submit">Envoyer mon avis</button>
    </form>


    <h3>Ce que disent les utilisateurs</h3>
        <div id="testimonials-container">
            <?php include 'request/feedback.php' ?>
        </div>
</section>

    <!-- Footer -->
    <div class="footer">
        © 2024 La SYSMI PROJECT | Tous droits réservés
    </div>

</body>
</html>
