<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/import/css/navbar.css">
    <link rel="stylesheet" href="/public/import/css/pages/minigames.css">
    <link rel="icon" href="/public/img/icon/Logo.png" type="image/png">
    <title>Mini-Jeux - La SYSMI PROJECT</title>
</head>

<body>

    <div class="navbar">
        <div class="navbar-logo">
            <a href="/index.php" target="_self">
                <img src="/public/img/icon/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
            </a>
        </div>
    </div>

    <main class="main-container">
        <h2 class="section-title">Explorez nos jeux interactifs</h2>
		        <div class="category-container">
            <h2 class="category-title">🎮 Jeux d'Arcade</h2>
            <div class="games-container">
                <?php
                $arcade = [
                    ["Snake", "/public/pages/games/rétro/snake.html", "https://wallpapers.com/images/hd/snake-game-1280-x-720-background-ecf2va39zdslaydt.jpg", "Un jeu d'arcade où vous contrôlez un serpent qui grandit à chaque repas."],
                    ["Pacman", "/public/pages/games/rétro/pacman.html", "https://images7.alphacoders.com/503/thumb-1920-503083.png", "Évitez les fantômes et mangez les pac-gommes."],
                    ["Ping Pong", "/public/pages/games/rétro/pong2.html", "https://s3.amazonaws.com/gs.apps.screenshots/00000138-c4eb-cab1-9637-f6fcc971d29c.png", "Un jeu classique de ping-pong."],
                    ["Bomberman", "/public/pages/games/rétro/bomberman.html", "https://images.launchbox-app.com/d73c1ad8-4485-43b5-b4cf-0fc5f3e07f00.png", "Posez des bombes pour détruire vos ennemis."]
                ];
                foreach ($arcade as $jeu) {
                    echo "<div class='game-box'>
                            <div class='game-image' style='background-image: url($jeu[2]);'></div>
                            <div class='game-content'>
                                <h3 class='game-title'>$jeu[0]</h3>
                                <button class='game-button' onclick=\"window.location.href='$jeu[1]';\">Jouer</button>
                            </div>
                            <div class='description'>$jeu[3]</div>
                          </div>";
                }
                ?>
            </div>
        </div>

        <div class="category-container">
            <h2 class="category-title">🎯 Jeux de Réflexion</h2>
            <div class="games-container">
                <?php
                $jeux = [
                    ["Tic Tac Toe", "/public/pages/games/réflexion/morpion.html", "https://th.bing.com/th/id/R.6830ca7eb0011248350863b07b1e4b71?rik=HyXzBQSem6AP6A&riu=http%3a%2f%2fimga999.5054399.com%2fupload_pic%2f2010%2f12%2f24%2f4399_11513417196.jpg&ehk=lw3zDrN7iWT1TlcrG6yf5ha3TcOu3pZt%2fj9FCM5a9LI%3d&risl=&pid=ImgRaw&r=0", "Un jeu classique de morpion à deux joueurs."],
                    ["Puissance 4", "/public/pages/games/réflexion/puissance4.html", "https://th.bing.com/th/id/OIP.-XEdApHpWH726xALAnLoEAHaGW?rs=1&pid=ImgDetMain", "Empilez quatre jetons avant votre adversaire."],
                    ["Pendu", "/public/pages/games/réflexion/pendu.html", "https://is4-ssl.mzstatic.com/image/thumb/Purple126/v4/c0/37/06/c0370653-7aa6-5e9f-615b-ed25e7bfaeb0/AppIcon-0-0-1x_U007emarketing-0-0-0-7-0-0-sRGB-0-0-0-GLES2_U002c0-512MB-85-220-0-0.png/1200x630wa.png", "Devinez les lettres d'un mot avant que le bonhomme soit pendu."],
                    ["Pierre Feuille Ciseaux", "/public/pages/games/réflexion/pfc.html", "https://tse3.mm.bing.net/th?id=OIP.ueR86X9V4fPzokF_nykInwHaHa&pid=Api&P=0&h=180", "Le classique jeu de pierre, feuille, ciseaux."],
					["Dames", "/public/pages/games/réflexion/dames.html", "https://tse3.mm.bing.net/th?id=OIP.ueR86X9V4fPzokF_nykInwHaHa&pid=Api&P=0&h=180", "Le classique jeu de pierre, feuille, ciseaux."]
                ];
                foreach ($jeux as $jeu) {
                    echo "<div class='game-box'>
                            <div class='game-image' style='background-image: url($jeu[2]);'></div>
                            <div class='game-content'>
                                <h3 class='game-title'>$jeu[0]</h3>
                                <button class='game-button' onclick=\"window.location.href='$jeu[1]';\">Jouer</button>
                            </div>
                            <div class='description'>$jeu[3]</div>
                          </div>";
                }
                ?>
            </div>
        </div>

        <div class="category-container">
            <h2 class="category-title">🧠 Jeux de Hasard & Estimation</h2>
            <div class="games-container">
                <?php
                $hasard = [
                    ["Guess The Number", "/public/pages/games/réflexion/numberTest.html", "https://image-url.com/guess.jpg", "Devinez le nombre entre 1 et 100."],
                    ["Calcul Mental", "/public/pages/games/réflexion/calcule.html", "https://image-url.com/calculate.jpg", "Testez vos compétences en calcul mental."]
                ];
                foreach ($hasard as $jeu) {
                    echo "<div class='game-box'>
                            <div class='game-image' style='background-image: url($jeu[2]);'></div>
                            <div class='game-content'>
                                <h3 class='game-title'>$jeu[0]</h3>
                                <button class='game-button' onclick=\"window.location.href='$jeu[1]';\">Jouer</button>
                            </div>
                            <div class='description'>$jeu[3]</div>
                          </div>";
                }
                ?>
            </div>
        </div>

    </main>

    <footer>
        <p>© 2025 La SYSMI PROJECT - Tous droits réservés.</p>
    </footer>

</body>

</html>