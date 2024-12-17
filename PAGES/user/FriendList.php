<?php
// Démarrer la session
session_start();

// Inclure le fichier de connexion à la base de données
include '../../request/DB.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <title>Demandes d'amis</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background: radial-gradient(circle, #021265, #691430);
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .request {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s ease;
        }

        .request:hover {
            background-color: #f9f9f9;
        }

        .info {
            display: flex;
            align-items: center;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            background-color: #ddd;
            background-size: cover;
            background-position: center;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .user-name a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .user-name a:hover {
            color: #4CAF50;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }

        .button {
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease;
            text-align: center;
        }

        .button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(100, 150, 255, 0.5);
        }

        .accept {
            background-color: #4CAF50;
            color: white;
        }

        .accept:hover {
            background-color: #45a049;
        }

        .decline {
            background-color: #f44336;
            color: white;
        }

        .decline:hover {
            background-color: #e41e1e;
        }

        .no-requests {
            text-align: center;
            margin-top: 20px;
            color: #888;
        }

        .back-button {
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
            border: none;
            cursor: pointer;
        }

        .contraineur {
            text-align: center;
            margin-bottom: 20px;
        }

        .contraineur a {
            display: inline-block;
            background-color: #0c9b0c;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .contraineur a:hover {
            background-color: #0a860a;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            .request {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }

            .avatar {
                margin-bottom: 10px;
            }

            .buttons {
                gap: 10px;
                margin-left: 65px;
            }

            .button {
                width: 100%;
            }
        }
        </style>
</head>
<body>
<div class="container">
<button class="back-button" onclick="goBack()">Accueil</button>

<script>
function goBack() {
    // Vérifier si l'URL précédente n'est pas la même que l'URL actuelle
    if (document.referrer && document.referrer !== window.location.href) {
        window.history.back();
    } else {

        window.location.href = '../../index.php';
    }
}
</script>

    <div class="header">
        <h1>Demandes d'amis</h1>
    </div>

    <?php
    try {
        // Récupérer l'ID de l'utilisateur connecté
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("L'utilisateur n'est pas connecté.");
        }
        $loggedInUserID = $_SESSION['user_id'];

        // Requête SQL pour récupérer les demandes d'amis en attente
        $sql = "SELECT u.id, u.nom, u.prenom, p.image_content
                FROM relation r
                INNER JOIN utilisateur u ON r.demandeur = u.id
                LEFT JOIN photos_de_profil p ON u.id = p.user_id
                WHERE r.receveur = ? AND r.statut = 0";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$loggedInUserID]);
        $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Afficher les demandes
        if ($demandes) {
            foreach ($demandes as $demande) {
                $userID = $demande['id'];
                $userName = htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']);
                $userProfilePic = $demande['image_content'] ? 'data:image/jpeg;base64,' . base64_encode($demande['image_content']) : 'https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg';
                ?>
                <div class="request">
                    <div class="info">
                        <div class="avatar" style="background-image: url('<?php echo $userProfilePic; ?>');"></div>
                        <div class="user-info">
                            <p class="user-name"><a href="profile.php?id=<?php echo $userID; ?>"><?php echo $userName; ?></a></p>
                            <p>Demande d'ami en attente</p>
                        </div>
                    </div>
                    <div class="buttons">
                        <form action="../../traitements/Friend_list/accept_friend.php" method="post">
                            <input type="hidden" name="requester_id" value="<?php echo $userID; ?>">
                            <button class="button accept" type="submit">Accepter</button>
                        </form>
                        <form action="../traitements/Friend_list/reject_friend.php" method="post">
                            <input type="hidden" name="requester_id" value="<?php echo $userID; ?>">
                            <button class="button decline" type="submit">Refuser</button>
                        </form>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="no-requests">Vous n\'avez aucune demande d\'ami en attente.</p>';
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
    ?>
</div>
</body>
</html>
