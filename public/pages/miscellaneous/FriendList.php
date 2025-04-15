<?php
// Démarrer la session
session_start();

// Inclure le fichier de connexion à la base de données
include __DIR__ . '/../../../requiments/database.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/Import/icons/Logo.png" type="image/png">
    <link rel="stylesheet" href="/public/import/css/navbar.css">
    <link rel="stylesheet" href="/public/import/css/pages/friendlist.css">
    <title>Demandes d'amis</title>
</head>
<body>

<div class="navbar">
        <div class="navbar-logo">
            <a href="/index.php" target="_self">
                <img src="/public/img/icon/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
            </a>
        </div>
    </div>

<div class="container">
    <div class="header">
        <h1>Demandes d'amis</h1>
    </div>

    <?php
    try {
        // Récupérer l'ID de l'utilisateur connecté
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("<p style=\"color: red; text-align: center; font-weight: 600;\">L'utilisateur n'est pas connecté.</p>");
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
                        <form action="/traitements/Friend_list/accept_friend.php" method="post">
                            <input type="hidden" name="requester_id" value="<?php echo $userID; ?>">
                            <button class="button accept" type="submit">Accepter</button>
                        </form>
                        <form action="/traitements/Friend_list/reject_friend.php" method="post">
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
