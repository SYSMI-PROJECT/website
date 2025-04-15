<?php
include __DIR__ . '/../../../requiments/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /Import/Error/No_connected.php");
    exit();
}

$loggedInUserID = $_SESSION['user_id'];

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
    <link rel="icon" href="/Import/icons/Logo.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/import/css/navbar.css">
    <link rel="stylesheet" href="/public/import/css/pages/messagelist.css">
    <title>Vos Conversations</title>
</head>
<body>

<div class="navbar">
        <div class="navbar-logo">
            <a href="/publication.php" target="_self">
                <img src="/Import/icons/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
            </a>
        </div>
    </div>

<div class="container">
    <h1>Vos Conversations</h1>
    <ul class="user-list">
        <?php if ($users): ?>
            <?php foreach ($users as $user): ?>
                <li onclick="window.location.href='message.php?id=<?= htmlspecialchars($user['user_id']) ?>'">
					<div class="avatar" 
						style="background-image: url('<?php echo !empty($image_content) ? 'data:image/jpeg;base64,' . base64_encode($image_content) : 'https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg'; ?>');">
					</div>
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
