<?php
include __DIR__ . '/../../../requiments/UsersData.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/Import/icons/Logo.png" type="image/png">
    <title>Tableau de bord</title>
    <!-- Inclure les CDN pour les bibliothèques CSS et JavaScript -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.css">
    <link rel="stylesheet" href="/public/import/css/navbar.css">
    <link rel="stylesheet" href="/public/import/css/pages/dashboard.css">
</head>
<body>

    <div class="navbar">
        <div class="navbar-logo">
            <a href="/index.php" target="_self">
                <img src="/public/img/icon/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
            </a>
        </div>
    </div>

    <?php if ($role === 'staff') : ?>
    <div class="container">
        <div class="dashboard" id="dashboard">
            <div class="widget system-category">
                <h2>Gestionnaire</h2>
                <button class="system-button" id="btnBanUser"><a href="/public/pages/dashboard/staff/user_dashboard.php">Utilisateurs</a></button>
				<button class="system-button" id="btnBanUser"><a href="/public/pages/dashboard/staff/post_gestion.php">Publications</a></button>
            </div>
            <!-- Espaces pour les différentes sections -->
            <!-- Section pour le personnel -->
            <div class="widget staff-section" id="staff">
                <h2>Discord : Salons vocaux</h2>
                <button class="system-button" id="btnBanUser" style="background-color: #007bff;"><a href="/public/pages/dashboard/staff/discord_voice.php">Rejoindre un salon vocal</a></button>
            </div>
            <!-- Section pour la cybersécurité -->
            <div class="widget cybersecurity-section" id="cybersecurity">
                <h2>Service Utilisateurs</h2>
                <button class="system-button" id="btnBanUser"><a href="/public/import/php/script/staff/add_service.php">Ouvrir et gérer</a></button>
            </div>
            <!-- Section pour les développeurs web -->
            <div class="widget web-developers-section" id="developers">
                <h2>Recompenses</h2>
                <button class="system-button" id="btnBanUser"><a href="/public/import/php/script/staff/remove_item.php">En retirer</a></button>
                <button class="system-button" id="btnBanUser"><a href="/public/import/php/script/staff/add_item.php">En ajouter</a></button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="container">
        <div class="dashboard" id="dashboard">
            <div class="widget system-category">
                <h2>Gestionnaire utilisateur</h2>
                <button class="system-button" id="btnBanUser"><a href="">En ajouter</a></button>
                <button class="system-button" id="btnBanUser"><a href="">En ajouter</a></button>
            </div>
            <div class="widget web-developers-section" id="developers">
                <h2>Recompenses</h2>
                <button class="system-button" id="btnBanUser"><a href="">En ajouter</a></button>
                <button class="system-button" id="btnBanUser"><a href="">En ajouter</a></button>
            </div>
            <div class="widget web-developers-section" id="developers">
                <h2>Recompenses</h2>
                <button class="system-button" id="btnBanUser"><a href="">En ajouter</a></button>
                <button class="system-button" id="btnBanUser"><a href="">En ajouter</a></button>
            </div>
        </div>
    </div>
</body>
</html>

