<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="../../css/nav.css?v=0">
    <title>3DS, DS, MODS LIST</title>
    <style>
        /* Reset des styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Styles généraux */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(-45deg, #4e19bc, #000000, #006402);
            color: #fff;
            line-height: 1.6;
            overflow-x: hidden;
            background-attachment: fixed;
        }

        header {
            background-color: #5d0000;
            padding: 20px 0;
            text-align: center;
        }

        h1 {
            margin: 0;
            animation: colorChange 1.5s infinite;
            font-size: 36px;
            letter-spacing: 2px;
        }

        @keyframes colorChange {
            0% { color: #33ff00; }
            33% { color: #007bff; }
            66% { color: #00cc00; }
            100% { color: #ff00f7; }
        }

        nav {
            background-color: #2c3e50;
            overflow: hidden;
            padding: 10px 0;
            text-align: center;
        }

        nav a {
            display: inline-block;
            color: yellow;
            text-align: center;
            padding: 14px 50px;
            margin: 0 5px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
            border: solid 1px white;
        }

        nav a:hover {
            background-color: black;
            color: white;
        }

        section {
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            margin: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            display: none;
            animation: fadeIn 0.5s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        h2 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 15px;
            font-size: 24px;
            text-transform: uppercase;
        }

        ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        li {
            margin: 10px;
            background-color: rgba(0, 111, 255, 0.4);
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            width: 200px;
        }

        li:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }

        a {
            display: block;
            text-decoration: none;
            color: white;
            font-weight: bold;
            padding: 10px;
            text-align: center;
        }

        img {
            width: 44%;
            height: auto;
            display: block;
            transition: transform 0.3s ease;
            border-radius: 5px;
        }

        li:hover img {
            transform: scale(1.1);
        }

        footer {
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        @media (max-width: 768px) {
            li {
        flex: 0 0 calc(50% - 20px);
    }
    nav {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-content: center;
    }

    nav a {
        font-size: 10px;
        padding: 5px;
        width: 78px;
        margin: 2px;
    }
}

@media (max-width: 380px) {
    nav a {
    font-size: 10px;
    padding: 3px;
    width: 70px;
    margin: 2px;
    }
}


    </style>
</head>
<body>
    <div class="navbar">
        <a href="../../index.php"><img src="https://i.imgur.com/JyoQK5Q.png" alt="Texte alternatif de l'image"></a>
        <div class="navbar-menu">
        <a href="#3DS">N-3DS</a>
        <a href="#Wii">N-Wii</a>
        <a href="#Mii">QR CODE</a>
        <a href="#OST">Sound</a>
</div>
    <div class="mobile-menu">&#9776;</div>
</div>


    <section id="3DS">
        <h2>Nintendo 3DS</h2>
        <ul>
            <li><a href="#jeu3ds1"><img src="PNG/Games/3DS/NSMB2.png" alt="Jeu 1"></a></li>
            <li><a href="#jeu3ds2"><img src="PNG/Games/3DS/M3DL.png" alt="Jeu 2"></a></li>
            <li><a href="#jeu3ds3"><img src="PNG/Games/3DS/SMM3DS.png" alt="Jeu 3"></a></li>
            <li><a href="#jeu3ds4"><img src="PNG/Games/3DS/TL.png" alt="Jeu 4"></a></li>
        </ul>
    </section>

    <section id="jeu3ds1">
        <h2>Jeu Nintendo 3DS 1</h2>
        <p>Description du jeu 1 pour Nintendo 3DS...</p>
    </section>

    <section id="jeu3ds2">
        <h2>Jeu Nintendo 3DS 2</h2>
        <p>Description du jeu 2 pour Nintendo 3DS...</p>
    </section>

    <section id="jeu3ds3">
        <h2>Jeu Nintendo 3DS 3</h2>
        <p>Description du jeu 3 pour Nintendo 3DS...</p>
    </section>

    <section id="jeu3ds4">
        <h2>Jeu Nintendo 3DS 4</h2>
        <p>Description du jeu 4 pour Nintendo 3DS...</p>
    </section>

    <section id="Wii">
        <h2>Nintendo Wii</h2>
        <ul>
            <li><a href="#jeu1"><img src="PNG/Games/Wii/MarioBrosWii.png" alt="Jeu 1"></a></li>
            <li><a href="#jeu2"><img src="PNG/Games/Wii/MarioKartWii.png" alt="Jeu 2"></a></li>
            <li><a href="#jeu3"><img src="PNG/Games/Wii/GameWii3.png" alt="Jeu 3"></a></li>
        </ul>
    </section>

    <section id="jeu1">
        <h2>Jeu Wii 1</h2>
        <p>Description du jeu 1...</p>
    </section>

    <section id="jeu2">
        <h2>Jeu Wii 2</h2>
        <p>Description du jeu 2...</p>
    </section>

    <section id="jeu3">
        <h2>Jeu Wii 3</h2>
        <p>Description du jeu 3...</p>
    </section>

    <section id="menu">
        <h2>Menu</h2>
        <ul>
            <li><a href="../index.php">Index</a></li>
            <li><a href="https://3dsrpc.com/">RPC</a></li>
            <li><a href="DSI.php">DSI Shop</a></li>
        </ul>
    </section>

    <footer>
        &copy; 2024 Page de Téléchargement. Tous droits réservés.
    </footer>

    <script>
// Fonction pour afficher la section appropriée lors de la navigation
function afficherSection(sectionId) {
    // Masquer toutes les sections par défaut
    var sections = document.querySelectorAll('section');
    sections.forEach(function(section) {
        section.style.display = 'none';
    });

    // Afficher la section spécifiée
    var sectionToShow = document.getElementById(sectionId);
    if (sectionToShow) {
        sectionToShow.style.display = 'block';
    }
}

// Écouteurs d'événements pour les liens de la barre de navigation
var navLinks = document.querySelectorAll('div a');
navLinks.forEach(function(link) {
    link.addEventListener('click', function(event) {
        event.preventDefault();
        var sectionId = link.getAttribute('href').substring(1);
        afficherSection(sectionId);
    });
});

// Ajouter un gestionnaire d'événements pour l'image redirigeant vers "index.php"
var imageLink = document.querySelector('.navbar img');
if (imageLink) {
    imageLink.addEventListener('click', function(event) {
        // Redirection vers "../index.php"
        window.location.href = "../../index.php";
    });
}
</script>

<script>
    // JavaScript pour le menu hamburger
    const mobileMenu = document.querySelector('.mobile-menu');
    const nav = document.querySelector('.navbar-menu');

    mobileMenu.addEventListener('click', function() {
        if (nav.style.display === 'block') {
            nav.style.display = 'none';
        } else {
            nav.style.display = 'block';
        }
    });
</script>


</body>
</html>
