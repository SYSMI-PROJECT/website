<?php
session_start();
include 'request/UserInfo.php'; // Assurez-vous que ce fichier est accessible et que la session y est correctement manipulée.
$isUserLoggedIn = isset($_SESSION['user_id']);
$prenom = $isUserLoggedIn ? (isset($_SESSION['prenom']) ? $_SESSION['prenom'] : 'Invité') : 'Invité';
$VIP = isset($_SESSION['VIP']) ? $_SESSION['VIP'] : false; // Vérification explicite de la variable VIP
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="Import/icons/Logo.png">
    <title>Menu</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, rgb(0 223 71) 0%, rgb(0 20 255) 100%);
            position: relative;
            color: #333;
            background-attachment: fixed;
            overflow-x: hidden;
        }

        /* Effet de flou pour donner de la profondeur */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('Import/icons/banner.png') no-repeat center center;
            background-size: cover;
            filter: blur(15px);
            opacity: 0.7;
            z-index: -1;
        }

        .greeting-container {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-bottom: 40px;
            padding: 0 20px;
        }

        .user-greeting {
            color: #fff;
            font-size: 2em;
            font-weight: 700;
            background-color: rgb(0 0 0 / 63%);
            padding: 20px 40px;
            border-radius: 30px;
            box-shadow: 0 12px 24px rgb(0 0 0);
            display: inline-block;
            text-shadow: 2px 2px 4px rgb(0 0 0);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .user-greeting:hover {
            background-color: rgba(0, 0, 0, 0.8);
            transform: scale(1.05);
        }

        .user-greeting.vip-greeting {
            color: goldenrod;
        }

        .action-container {
            position: relative;
            z-index: 2;
            margin: 20px 0;
            text-align: center;
            padding: 0 20px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-btn, .logout-btn {
            padding: 15px 30px;
            font-size: 1.2em;
            cursor: pointer;
            color: #fff;
            border: none;
            border-radius: 25px;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);
            margin: 5px;
            white-space: nowrap;
            text-shadow: 3px 3px 5px rgb(0 0 0);
        }

        .action-btn {
            background-color: #007bff;
        }

        .logout-btn {
            background-color: #dc3545;
        }

        .action-btn:hover, .logout-btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.5);
        }

        .action-btn:active, .logout-btn:active {
            transform: scale(0.98);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            z-index: 2;
            padding: 0 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .box {
            width: 100%;
            max-width: 300px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border: solid #000000;
            border-radius: 15px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            box-sizing: border-box;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            color: inherit;
            position: relative;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.6));
                }

        .box img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 15px;
            transition: transform 0.3s ease;
        }

        .box:hover img {
            transform: scale(1.1);
        }

        .box h2 {
            margin-top: 10px;
            font-size: 1.4em;
            font-weight: 600;
            color: #333;
        }

        .box.disabled {
            pointer-events: none;
            opacity: 0.6;
            cursor: not-allowed;
        }

        .box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            transition: background 0.3s ease;
            z-index: 1;
        }

        .box:hover::before {
            background: rgba(0, 0, 0, 0.2);
        }

        .box:hover {
            transform: scale(1.05);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.4);
            border-color: rgb(255 242 0 / 80%);
            background: #000000a6;
            
        }

        .box h2:hover {
            color: white;
        }

        /* Media Queries pour les petits écrans */
        @media (max-width: 425px) {
            .user-greeting {
                font-size: 1.5em;
                padding: 15px 30px;
            }

            .greeting-container {
                margin-bottom: 10px;
            }

            .action-btn, .logout-btn {
                padding: 12px 25px;
                font-size: 1em;
            }

            .box {
                max-width: 100%;
            }

            .box h2 {
                font-size: 1.2em;
            }

            .container {
                flex-direction: column;
                align-items: center;
            }
        }

        @media (max-width: 480px) {
            .user-greeting {
                font-size: 1.2em;
                padding: 10px 20px;
            }

            .action-btn, .logout-btn {
                padding: 10px 20px;
                font-size: 0.9em;
            }

            .box h2 {
                font-size: 1em;
            }

            .button-container {
                flex-wrap: nowrap;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
<div class="greeting-container">
        <?php if ($isUserLoggedIn): ?>
            <?php if ($VIP): ?>
                <div class="user-greeting vip-greeting">Bienvenue, <?php echo htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8'); ?> !</div>
            <?php else: ?>
                <div class="user-greeting">Bienvenue, <?php echo htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8'); ?> !</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="user-greeting">Bienvenue, cher utilisateur !</div>
        <?php endif; ?>
    </div>

    <div class="action-container">
        <div class="button-container">
            <?php if ($isUserLoggedIn): ?>
                <a href="traitements/Formulaires/logout.php" class="logout-btn">Déconnexion</a>
            <?php else: ?>
                <a href="formulaires/login.html" class="action-btn">Connexion</a>
                <a href="formulaires/Signup.html" class="action-btn">Inscription</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
    <!-- Site internet -->
    <a href="<?php echo $isUserLoggedIn ? 'index.php' : 'guest.html'; ?>" class="box">
        <img 
            src="<?php echo $isUserLoggedIn ? 'Import/icons/Logo.png' : 'Import/icons/invite.png'; ?>" 
            alt="<?php echo $isUserLoggedIn ? 'Page principale' : 'Mode invité'; ?>">
        <h2><?php echo $isUserLoggedIn ? 'Site internet' : '(Mode invité)'; ?></h2>
    </a>
    
    <!-- À propos -->
    <a href="presentation.html" class="box">
        <img src="https://th.bing.com/th/id/R.aedb4375473b5386b39eecba5874dd47?rik=e7MS1KvDFBqm4Q&riu=http%3a%2f%2fwww.toried.com%2ffr%2fpics%2finfo_icon.png&ehk=1UnHdBP3Oh%2bpAZoWwdqnKcJTceC11BszFgMr6VE5Hs4%3d&risl=&pid=ImgRaw&r=0" alt="Présentation du site">
        <h2>À propos</h2>
    </a>
    
    <!-- SYSMI ZONE -->
    <a href="<?php echo $isUserLoggedIn ? 'PAGES/discord/SYSMI_ZONE.php' : '#'; ?>" class="box <?php echo !$isUserLoggedIn ? 'disabled' : ''; ?>">
        <img src="https://logodownload.org/wp-content/uploads/2017/11/discord-logo-0.png" alt="SYSMI ZONE">
        <h2><?php echo $isUserLoggedIn ? 'SYSMI ZONE' : 'Connexion nécessaire'; ?></h2>
    </a>
</div>
</body>
</html>

