<?php
// Inclure le fichier de connexion à la base de données
include '../../request/DB.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "Veuillez vous connecter pour voir vos conversations.";
    exit();
}

// Récupérer l'ID de l'utilisateur connecté
$loggedInUserID = $_SESSION['user_id'];

// Récupérer la liste des utilisateurs avec lesquels des messages ont été échangés
$sql = "
    SELECT DISTINCT 
        u.id AS user_id, 
        u.nom, 
        u.prenom, 
        p.image_content 
    FROM utilisateur u
    LEFT JOIN photos_de_profil p ON u.id = p.user_id
    INNER JOIN messages_prives m 
        ON (u.id = m.expediteur_id OR u.id = m.destinataire_id)
    WHERE (m.expediteur_id = ? OR m.destinataire_id = ?)
      AND u.id != ?
";
$stmt = $conn->prepare($sql);
$stmt->execute([$loggedInUserID, $loggedInUserID, $loggedInUserID]);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <title>Vos Conversations</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle, #021265, #691430);
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1 {
            font-size: 1.8em;
            color: #333;
            text-align: center;
        }

        .user-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .user-list li {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .user-list li:last-child {
            border-bottom: none;
        }

        .user-list li:hover {
            background-color: #f0f8ff;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
        }

        .user-info {
            flex-grow: 1;
        }

        .user-info h2 {
            margin: 0;
            font-size: 1.2em;
            color: #333;
        }

        .user-info p {
            margin: 0;
            font-size: 0.9em;
            color: #777;
        }

        .fa-arrow-right {
            color: #777;
            transition: transform 0.3s ease;
        }

        .user-list li:hover .fa-arrow-right {
            transform: translateX(5px);
        }

        /* Style pour le bouton retour */
        .back-btn {
            display: inline-block;
            background-color: #ff4757;
            color: white;
            padding: 10px 0px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            width: 100%;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #ff6f61;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="../../index.php" class="back-btn">Accueil</a> <!-- Bouton pour retourner à l'index -->

    <h1>Vos Conversations</h1>
    <ul class="user-list">
        <?php if ($users): ?>
            <?php foreach ($users as $user): ?>
                <li onclick="window.location.href='message.php?id=<?= htmlspecialchars($user['user_id']) ?>'">
                    <div class="avatar" style="background-image: url('data:image/jpeg;base64,<?= base64_encode($user['image_content']) ?>');"></div>
                    <div class="user-info">
                        <h2><?= htmlspecialchars($user['prenom']) . ' ' . htmlspecialchars($user['nom']) ?></h2>
                        <p>ID: <?= htmlspecialchars($user['user_id']) ?></p>
                    </div>
                    <i class="fa-solid fa-arrow-right"></i>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune conversation trouvée.</p>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>
