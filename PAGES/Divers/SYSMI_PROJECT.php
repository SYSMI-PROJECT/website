<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../Logo.png">
    <link rel="stylesheet" type="text/css" href="../css/nav.css?v=">
    <title>SYSMI PROJECT (More)</title>
    <style>
        :root {
            --color-red: #ff6666;
            --color-red-hover: #ff9999;
            --color-green: #66ff99;
            --color-blue: #66a3ff;
            --background-dark: rgba(30, 30, 30, 0.8);
            --background-light: rgba(255, 255, 255, 0.5);
        }

        body {
            background-image: url(./PNG/Walpaper/Walpmor.png);
            background-attachment: fixed;
            background-color: black;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-size: cover;
            color: white;
        }


        .audio-player {
            position: absolute;
            right: 20px;
        }

        .voir {
            width: 90%;
            background-color: var(--background-dark);
            border-radius: 1em;
            padding: 20px;
            margin: 50px auto;
            box-shadow: 9px 9px 9px 0px rgb(255 0 0 / 30%);
        }

        .voir h2 {
            font-weight: bold;
            color: var(--color-red);
            text-decoration: underline;
            text-align: center;
            animation: fadeInUp 1s ease-in-out;
        }

        .voir h3 {
            font-weight: bold;
            color: var(--color-green);
            margin-top: 30px;
            border-bottom: 2px solid var(--color-green);
            padding-bottom: 5px;
            animation: fadeInUp 1s ease-in-out;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        a {
            color: var(--color-red);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: var(--color-red-hover);
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 10px;
        }

        li:before {
            content: "•";
            color: var(--color-blue);
            display: inline-block;
            width: 1em;
            margin-right: 5px;
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
        }



        .navbar a:hover {
            color: var(--color-red-hover);
        }

        @media only screen and (max-width: 768px) {
            .navbar {
                padding: 5px;
            }

            .navbar a {
                margin: 0 5px;
            }

            .voir {
                width: 95%;
                padding: 10px;
            }

            .Title-line img {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../index.php">Accueil</a>
        <a href="#comment">Comment</a>
        <a href="#discord">Discord</a>
        <a href="#themes">Thèmes</a>
        <a href="#participer">Participer</a>
        <a href="#contact">Contact</a>
        <a href="#site_internet">Site Web</a>
        <a href="#serveur_officiel">Serveur Officiel</a>
        <a href="#SYSMI_Club">SYSMI Club</a>
    </div>
    <div id="comment" class="voir">
        <h2>Comment la SYSMI Club pourra réunir plusieurs grandes communautés ?</h2>
        <h3>Pour cela, j'ai créé un endroit nommé "SYSMI Zone", mais quel est son but ?</h3>
        <h3>La SYSMI Zone a été conçue afin que les utilisateurs puissent rejoindre divers serveurs Discord grâce à des cartes de serveurs et des numéros à 3 chiffres.</h3>
        <h3>tous les serveurs doivent respecter les règlements qui sont affichés dans la page <a href="../PAGES/discord/règlement.php">Reglement</a>.</h3>
        <h3>Une fois que toutes les règles sont respectées, chaque salon de serveurs à thème différent sera connecté via un réseau relié à un serveur officiel unique dédié à ce thème (le réseau sera créé grâce à un bot Discord).</h3>
        <h3>Cela signifie que les membres pourront contacter les utilisateurs d'un autre serveur, mais seuls les membres du staff d'un serveur officiel auront la permission d'activer ou de désactiver la connexion.</h3>
        <h3>Et pour finir, elle offrira également plusieurs avantages, tels que :</h3>
        <ul>
            <li>Il sera ouvert au grand public</li>
            <li>Permettra de faire encore plus de nouvelles rencontres</li>
            <li>Le serveur pourra être facilement retrouvé</li>
        </ul>
    </div>
    <div id="discord" class="voir">
        <h2>En parlant du bot Discord, que va-t-il faire plus précisément ?</h2>
        <h3>Pour répondre à cette question, une connexion privée sera réalisée dans un serveur via un bot Discord.</h3>
        <h3>Son but principal est de créer une connexion entre un salon textuel et le bot via un réseau. Une fois cette opération accomplie, nous allons renommer la connexion. En dernière étape, il faut qu'un membre du staff vienne sur votre serveur afin d'établir la connexion avec le bot, grâce au nom que nous lui avons donné. (il faut que votre serveur ait le bot)</h3>
        <h3>Ensuite, vous pouvez enfin parler avec un membre, même si la personne n'est pas dans le même serveur.</h3>
    </div>
    <div id="themes" class="voir">
        <h2>De quoi va parler ce projet ? A-t-il un thème spécifique ?</h2>
        <h3>En général, ce projet abordera différents thèmes selon les intérêts de la communauté.</h3>
    </div>
    <div id="participer" class="voir">
        <h2>Que pourrons-nous faire dans ce projet ?</h2>
        <h3>Dans ce projet, les membres auront l'opportunité de participer à diverses activités telles que des discussions, des jeux, des événements spéciaux, etc.</h3>
    </div>
    <div id="contact" class="voir">
        <h2>Comment va se dérouler la création du projet ?</h2>
        <h3>Le projet a déjà commencé avec la mise en place des serveurs Discord et la création du site web. Les prochaines étapes incluront l'ajout de fonctionnalités supplémentaires et l'organisation d'événements pour la communauté.</h3>
    </div>
    <div id="site_internet" class="voir">
        <h2>À quoi va servir le site internet ?</h2>
        <h3>Le site web a été créé afin que les utilisateurs puissent se connecter, écouter de la musique, jouer à des mini-jeux, etc.</h3>
        <h3>Il a aussi été créé pour rassembler ce qui a été réalisé et pour attendre que tout soit prêt.</h3>
    </div>
    <div id="serveur_officiel" class="voir">
        <h2>Qu'est-ce qu'un serveur officiel ?</h2>
        <h3>C'est tout simplement un serveur centré sur un thème unique tel que :</h3>
        <ul>
            <li>La Rencontre</li>
            <li>Le Gaming</li>
            <li>Les Animés, etc.</li>
        </ul>
        <h3>Cependant, ces serveurs seront :</h3>
        <ol>
            <li>gérés par la SYSMI ASSISTANCE</li>
            <li>les staff auront pour fonction de gérer leur propre connexion et les salons,</li>
            <li>enfin, le serveur sera équipé d'une catégorie qui s'affichera ci-dessous.</li>
        </ol>
    </div>
    <div id="SYSMI_Club" class="voir">
        <h2>C'est quoi la SYSMI Club ?</h2>
        <h3>La SYSMI Club englobera l'ensemble des serveurs de la SYSMI Zone, ainsi qu'un pack téléchargeable via le site web, afin de les faire profiter d'une grande activité.</h3>
        <h3>Ce pack est actuellement en cours de création...</h3>
        <h3>De nombreuses activités seront aussi proposées, telles que :</h3>
        <ul>
            <li>Participation à des mini-jeux collaboratifs et imaginatifs</li>
            <li>Opportunité de faire de nouvelles rencontres</li>
            <li>Possibilité de gagner des récompenses et bien d'autres choses encore...</li>
            <li>La création de la SYSMI Club vise également à lutter contre l'ennui.</li>
        </ul>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            window.addEventListener('scroll', reveal);

            function reveal() {
                var reveals = document.querySelectorAll('.voir');
                for (var i = 0; i < reveals.length; i++) {
                    var windowHeight = window.innerHeight;
                    var revealTop = reveals[i].getBoundingClientRect().top;
                    var revealPoint = 150;

                    if (revealTop < windowHeight - revealPoint) {
                        reveals[i].classList.add('active');
                    }
                }
            }

            document.querySelectorAll('.navbar a').forEach(anchor => {
                // Ignorer le lien "Accueil"
                if (anchor.getAttribute('href') === "../index.php") return;

                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            reveal(); // Révéler les éléments au chargement
        });
    </script>
</body>
</html>
