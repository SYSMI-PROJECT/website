<?php
session_start();
include __DIR__ . '/../../../requiments/UsersData.php';

$isUserLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/import/css/navbar.css">
    <link rel="icon" href="/public/img/icon/Logo.png" type="image/png">
    <link rel="stylesheet" href="/public/import/css/discord/discord_zone.css">
    <title>SYSMI ZONE</title>

</head>
<body>

<div class="navbar">
        <div class="navbar-logo">
            <a href="../user/account.php" target="_self">
                <img src="/Import/icons/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
            </a>
        </div>
    </div>

<div class="content">

    <div class="container">
        <div class="filter-menu">
            <label for="filter"></label>
            <select id="filter" onchange="filterCards()">
                <option value="all">Tout</option>
                <option value="rencontre">Rencontre</option>
                <option value="gaming">Gaming</option>
                <option value="anime">Animé</option>
                <option value="nsfw">NSFW</option>
                <option value="development">Développement</option>
            </select>
        </div>

        <div class="search-bar">
            <label for="search"></label>
            <input type="number" id="search" onkeyup="searchCards()" placeholder="Entrez un numéro (e.g., 0001)">
        </div>
    </div>

    <div class="cards" id="cardContainer">
        <!-- Les cartes seront ajoutées ici par JavaScript -->
    </div>
</div>

<script src="/public/import/js/discord/discord_zone.js"></script>

</body>
</html>