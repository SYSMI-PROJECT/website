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
    <link rel="stylesheet" href="../../Import/css/navbar.css?v=0.1">
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <title>SYSMI ZONE</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(-45deg, #7900ff, #4e003e, #001169);
            background-attachment: fixed;
            color: aliceblue;
    }

        .content {
            padding: 20px;
        }

        .filter-menu label {
            font-size: 14px;
            font-weight: bold;
            margin-right: 10px;
        }

        .filter-menu select {
            padding: 3px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 120px;
        }

        .search-bar label {
            font-size: 14px;
            font-weight: bold;
            margin-right: 10px;
        }

        .search-bar input {
            padding: 4px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 160px;
        }

        .container {
        max-width: 600px;
        margin: 100px auto;
        padding: 5px;
        background-color: #696969;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: center;
        flex-direction: row-reverse;
        align-items: center;
        margin-bottom: 30px;
        }

        .cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            border-radius: 20px;
            padding: 20px;
            width: calc(33.333% - 20px);
            box-shadow: 10px 10px 10px rgb(0 0 0 / 54%);
            box-sizing: border-box;
            margin-bottom: 60px;
            background-color: rgba(255, 255, 255, 0.08);
            transition: transform 0.3s ease;
            background-color: #0000008c;
            border: solid 1px #00aaff;
            height: -webkit-fill-available;
        }

        .card .description {
            overflow: hidden;
        }

        .card .description.expand {
            max-height: none;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 4px 7px 10px rgb(4 255 0 / 54%);
        }

        .card h3 {
            margin-top: 0;
            color: white;
            text-align: center;
        }

        p {
            color: aliceblue;
        }

        .form-container {
            margin-bottom: 20px;
            display: none;
        }

        .form-container.show {
            display: block;
        }

        .card-details {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            margin-top: 12px;
        }

        .server-tag {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            background-position: center;
            background-size: cover;
        }

        .card-details-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .card-details-text p {
            margin: 5px;
            text-align: left;
            font-size: 12px;
        }

        .description {
            box-sizing: border-box;
            position: relative;
            text-align: center;
        }

        .description h1 {
            font-size: 12px;
            color: white;
            text-align: justify;
            margin-bottom: 30px;
            margin-top: 15px;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-weight: 100;
        }

        .join-button {
            display: block;
            background-color: #b54100b0;
            color: #ffffff;
            text-decoration: none;
            text-align: center;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            border-radius: 10px;
            padding: 5px;
            font-weight: bold;
        }

        .join-button:hover {
            background-color: #00670b;
        }

        .read-more {
            display: none;
            margin-top: 10px;
            color: #FF3400;
            text-decoration: underline;
            cursor: pointer;
        }

        .description.expand .read-more {
            display: block;
        }

        .server-tag img {
            width: 40px;
            height: auto;
        }

        @media screen and (max-width: 768px) {
            .card {
                width: calc(50% - 20px);
            }
        }

        @media screen and (max-width: 480px) {
            .card {
                width: calc(100% - 0px);
            }
        }

    </style>
</head>
<body>

<div class="navbar">
    <a href="../../menu.php" target="_self">
        <img src="../../Import/icons/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
    </a>
    <div class="navbar-icons">
        <a href="https://discord.gg/6RPQn4NKuT" target="_blank">
            <i class="fab fa-discord navbar-icon" title="Discord" style="color: #0070ff;"></i>
        </a>
        <a href="../../PAGES/user/profile.php?id=<?php echo $user_id; ?>" target="_self">
            <?php if (!empty($image_content)): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($image_content); ?>" alt="Profile Picture" class="profile-picture">
            <?php else: ?>
                <i class="fas fa-user-circle navbar-icon" title="Profil" style="font-size: 40px;"></i>
            <?php endif; ?>
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
            <input type="text" id="search" onkeyup="searchCards()" placeholder="Entrez un numéro (e.g., 0001)">
        </div>
    </div>

    <div class="cards" id="cardContainer">
        <!-- Les cartes seront ajoutées ici par JavaScript -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Récupérer les cartes depuis le serveur
        fetchCards();
    });

    function fetchCards() {
        fetch('fetch_cards.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    generateCardNumbers(data.cards); // Générer les numéros de carte
                    displayCards(data.cards); // Afficher les cartes
                } else {
                    console.error("Erreur de récupération des cartes : " + data.message);
                }
            })
            .catch(error => {
                console.error("Erreur de requête : ", error);
            });
    }

    function generateCardNumbers(data) {
        var categories = {};
        data.forEach(function(card) {
            if (!categories[card.category]) {
                categories[card.category] = 1;
            } else {
                categories[card.category]++;
            }
            card.number = ("000" + categories[card.category]).slice(-3);
        });
    }

    function displayCards(cards) {
    var cardContainer = document.getElementById("cardContainer");
    cardContainer.innerHTML = "";
    cards.forEach(function(card) {
        var cardElement = document.createElement("div");
        cardElement.classList.add("card");
        cardElement.setAttribute("data-category", card.category);
        cardElement.setAttribute("data-number", card.number);

        var cardInnerHTML = `
            <h3>${card.cardName}</h3>
            <div class="card-details">
                <div class="server-tag" style="background-image: url('${card.image}')"></div>
                <div class="card-details-text">
                    <p>Numéro: ${card.number}</p>
                    <p>Catégorie: ${card.category.charAt(0).toUpperCase() + card.category.slice(1)}</p>
                </div>
            </div>
            <div class="description">
                <h1>${card.description}</h1>
            </div>
            <a href="${card.link}" target="_blank" class="join-button">Rejoindre</a>
        `;

        cardElement.innerHTML = cardInnerHTML.trim().split('\n').map(line => line.trim()).join('\n');
        cardContainer.appendChild(cardElement);
    });
}


    function filterCards() {
        var filterValue = document.getElementById("filter").value;
        var cards = document.getElementsByClassName("card");

        for (var i = 0; i < cards.length; i++) {
            if (filterValue === "all") {
                cards[i].style.display = "block";
            } else if (cards[i].getAttribute("data-category") === filterValue) {
                cards[i].style.display = "block";
            } else {
                cards[i].style.display = "none";
            }
        }
    }

    function searchCards() {
        var searchValue = document.getElementById("search").value;
        var cards = document.getElementsByClassName("card");

        for (var i = 0; i < cards.length; i++) {
            if (cards[i].getAttribute("data-number").includes(searchValue)) {
                cards[i].style.display = "block";
            } else {
                cards[i].style.display = "none";
            }
        }
    }
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const menuToggle = document.querySelector(".menu-toggle");
        const menu = document.querySelector(".menu");

        menuToggle.addEventListener("click", function() {
            menu.classList.toggle("active");
        });
    });
</script>

</body>
</html>