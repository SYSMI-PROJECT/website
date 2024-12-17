<?php
session_start();

// Inclure le fichier de connexion à la base de données
require '../../request/DB.php';

// Vérifier si l'utilisateur est connecté et récupérer son prénom depuis la session
$prenom = "";
if(isset($_SESSION['prenom'])) {
    $prenom = $_SESSION['prenom'];
}

// Traitement de l'envoi de message
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["content"])) {
    $content = $_POST["content"];
    
    // Requête SQL pour insérer le message dans la base de données
    $sql = "INSERT INTO messages (content, user_name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$content, $prenom]);

    // Redirection vers la page d'accueil après l'envoi du message
    header("Location: Forum.php");
    exit();
}

// Fonction pour afficher les messages depuis la base de données
function displayMessages() {
    global $conn;
    $sql = "SELECT * FROM messages ORDER BY created_at ASC"; // Ordre ASC pour les messages plus anciens en premier
    $stmt = $conn->query($sql);
    while ($row = $stmt->fetch()) {
        echo "<div class='message-container'>";
        echo "<div class='message-header'>";
        // Ajouter la phrase "posté par username le date à l'heure"
        echo "<span>posté par " . $row['user_name'] . " (le) " . date('d/m/Y', strtotime($row['created_at'])) . " (à) " . date('H:i', strtotime($row['created_at'])) . "</span>";
        echo "<button class='delete-button' onclick='deleteMessage(" . $row['id'] . ")'>Supprimer</button>";
        echo "</div>";
        echo "<div class='message-content'>";
        echo "<p>" . nl2br($row['content']) . "</p>"; // Utilisation de nl2br pour les sauts de ligne
        echo "</div>";
        echo "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/nav.css?v=0">
    <link rel="icon" href="../../Logo.png" type="image/png">
    <title>Forum</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            background: linear-gradient(-45deg, #1d003d, #211b1b, #00072b);
            background-attachment: fixed;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            box-shadow: 0 0 10px 5px rgb(0 255 14);
            padding: 20px;
            border-radius: 1em;
            background-color: #4f4f4f;
        }

        h1 {
            text-align: center;
            color: white;
        }

        .message-container {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .message-header {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .message-content {
            padding: 20px;
            line-height: 1.6;
        }

        .message-content p {
            margin: 0;
        }

        .delete-button {
            background-color: transparent;
            border: none;
            color: #fff;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .delete-button:hover {
            color: #dc3545;
        }

        #messageInput {
            width: 100%;
            height: 100px;
            margin-bottom: 20px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #000;
            border-radius: 5px;
            resize: none;
            margin-left: -10px;
            background-color: black;
            color: white;
            font-family: sans-serif;
}

        button {
            padding: 10px 20px;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            background-color: #002eff;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-button {
        background-color: #ff0000;
        /* margin-left: 10px; */
}

.id {
    width: 210px;
    padding: 10px 20px;
    text-align: center;
    border-radius: 1em;
    background-color: #ff5500;
    color: white;
    font-weight: 600;
}

    .btn {
        background-color: #ff0000;
    position: absolute;
    right: 120px;
    bottom: auto;
    margin: -38px;
    }

    header {
    background-color: #333;
    color: #fff;
}

    </style>
</head>
<body>
<header>
<div class="navbar">
    <a href="../../index.php"><img src="https://i.imgur.com/JyoQK5Q.png" alt="Texte alternatif de l'image"></a>
    <div class="navbar-menu">
        <a href="../contact.php">utilisateurs</a>
        <a href="../Games/home-menu.php">Mini-Jeux</a>
        <a href="../boutique.php">Boutique</a>
        <a href="../Download.php">SYSMI-PACK</a>
        <a href="../discord/SYSMI_ZONE.php" style="background: #404eed;">discord</a>
    </div>
    <div class="mobile-menu">&#9776;</div>
</div>

</header>
<div class="container">
    <h1>Forum</h1>
    <!-- Affichage du prénom de l'utilisateur -->
    <?php if(!empty($prenom)): ?>
        <p class="id">Bienvenue, <?php echo $prenom; ?> !</p>
    <?php endif; ?>

    <div id="messages">
        <!-- Affichage des messages existants -->
        <?php displayMessages(); ?>
    </div>

    <form id="messageForm" action="" method="post">
    <!-- Formulaire d'envoi de message -->
    <textarea id="messageInput" name="content" placeholder="Entrez votre message ici"></textarea>
    <button type="submit">Envoyer</button>
</form>


<script>
function deleteMessage(id) {
    // Récupérer l'ID de l'utilisateur connecté
    var user_id = <?php echo $_SESSION['user_id']; ?>;
    
    // Envoyer directement la requête de suppression sans message de confirmation
    fetch('delete.php?id=' + id + '&user_id=' + user_id, {
        method: 'GET',
    })
    .then(response => {
        if (response.ok) {
            window.location.reload();
        } else {
            console.error('Erreur lors de la suppression du message.');
        }
    })
    .catch(error => console.error('Erreur lors de la suppression du message:', error));
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
