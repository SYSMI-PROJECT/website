<?php
session_start();
include '../../request/UserInfo.php';

$isUserLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <title>Dashboard (STAFF)</title>
    <!-- Inclure les CDN pour les bibliothèques CSS et JavaScript -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.css">
    <link rel="stylesheet" href="../../Import/css/navbar.css?v=0.1">
    <style>
        body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    height: 100vh;
    overflow-y: auto;
    position: relative;
    background: linear-gradient(-45deg, #4e19bc, #000000, #006402);
    animation: gradientAnimation 10s ease infinite alternate;
}

        .container {
            max-width: 1500px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            grid-gap: 20px;
            margin-top: 20px;
        }
        .widget {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            
        }
        .widget h2 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 20px;
            color: #333;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        canvas {
            max-width: 100%;
            height: auto;
        }
        /* Styles spécifiques aux membres VIP */
        .vip-widget {
            background-color: #ffd700; /* Couleur jaune */
        }
        .vip-widget h2 {
            color: #333; /* Couleur noire */
        }
        .vip-button {
            background-color: #ff4500; /* Couleur orange */
        }
        .vip-button:hover {
            background-color: #e63900; /* Couleur rouge */
        }
        /* Styles pour les catégories de système */
        .system-category {
            background-color: #ff9999;
        }
        .system-category h2 {
            color: #333;
        }
        .system-button {
            background-color: #dc3545; /* Couleur rouge */
        }
        .system-button:hover {
            background-color: #bd2130; /* Couleur rouge foncé */
        }
        /* Styles pour les différentes sections */
        .staff-section {
            background-color: #b3d9ff; /* Couleur bleu clair */
        }
        .staff-section h2 {
            color: #333;
        }
        .cybersecurity-section {
            background-color: #ffcc99; /* Couleur orange clair */
        }
        .cybersecurity-section h2 {
            color: #333;
        }
        .web-developers-section {
            background-color: #c2f0c2; /* Couleur verte clair */
        }
        .web-developers-section h2 {
            color: #333;
        }
        /* Styles pour la barre de navigation */
        nav {
            background-color: #333;
            padding: 10px;
            text-align: center;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-right: 10px;
        }
        nav a:hover {
            background-color: #555;
        }

        .system-button a {
            text-decoration: none;
            color: white;
        }
        
    </style>
</head>
<body>
    <!-- Barre de navigation -->
    <div class="navbar">
    <div class="navbar-logo">
        <a href="../../index.php" target="_self">
            <img src="../../Import/icons/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
        </a>
    </div>
    <div class="navbar-icons">
        <a href="https://discord.gg/6RPQn4NKuT" target="_blank">
            <i class="fab fa-discord navbar-icon" title="Discord" style="color: #0070ff;"></i>
        </a>
        <div class="profile-container">
            <a href="../../PAGES/profile.php?id=<?php echo $user_id; ?>" target="_self">
                <?php if (!empty($image_content)): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($image_content); ?>" alt="Profile Picture" class="profile-picture">
                <?php else: ?>
                    <i class="fas fa-user-circle navbar-icon" title="Profil" style="font-size: 40px;"></i>
                <?php endif; ?>
            </a>
        </div>
    </div>
</div>

    <div class="container">
        <div class="dashboard" id="dashboard">
            <div class="widget system-category">
                <h2>Gestionnaire utilisateur (Staff)</h2>
                <button class="system-button" id="btnBanUser"><a href="Dashboard/user_gerstion.php">Ouvrir et gérer</a></button>
                <button class="system-button" id="btnViewBannedUsers"><a href="Dashboard/user_banned.php">Utilisateurs Bannis</a></button>
            </div>
            <!-- Espaces pour les différentes sections -->
            <!-- Section pour le personnel -->
            <div class="widget staff-section" id="staff">
                <h2>Discord : Salons vocaux</h2>
                <button class="system-button" id="btnBanUser" style="background-color: #007bff; margin-left: 55px;"><a href="../STAFF/voice_id.php">Rejoindre un salon vocal</a></button>
            </div>
            <!-- Section pour la cybersécurité -->
            <div class="widget cybersecurity-section" id="cybersecurity">
                <h2>Ajouter un service</h2>
                <button class="system-button" id="btnBanUser"><a href="../../../traitements/STAFF/add_service.php">En retirer</a></button>
                <button class="system-button" id="btnBanUser"><a href="../../../traitements/STAFF/add_service.php">En ajouter</a></button>
                </div>
            <!-- Section pour les développeurs web -->
            <div class="widget web-developers-section" id="developers">
                <h2>Recompenses</h2>
                <button class="system-button" id="btnBanUser"><a href="../../../traitements/STAFF/remove_item.php">En retirer</a></button>
                <button class="system-button" id="btnBanUser"><a href="../../../traitements/STAFF/add_item.php">En ajouter</a></button>
                </div>
                <!-- Contenu pour les développeurs web -->
            </div>
        </div>
    </div>

</body>
</html>

