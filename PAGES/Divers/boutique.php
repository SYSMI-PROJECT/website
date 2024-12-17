<?php
// Inclure le fichier de configuration de la base de données
include '../../request/UserInfo.php';

// Récupérer les produits depuis la base de données
$sql = "SELECT id, nom, description, prix, image FROM produits";
$produits = executeQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Import/css/navbar.css?v=0.1">
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <link href="../../Import/css/shop.css?v=0" rel="stylesheet">
    <title>Boutique</title>
</head>
<div class="navbar">
    <div class="navbar-logo">
        <a href="../../index.php" target="_self">
            <img src="../../Import/icons/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
        </a>
    </div>
    <div class="navbar-icons">
        <a href="https://discord.gg/6RPQn4NKuT" target="_blank">
            <i class="fab fa-discord navbar-icon" title="Discord" style="color: #0070ff;"></i>
        </a>
        <div class="profile-container">
            <a href="../../PAGES/user/profile.php?id=<?php echo $user_id; ?>" target="_self">
                <?php if (!empty($image_content)): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($image_content); ?>" alt="Profile Picture" class="profile-picture">
                <?php else: ?>
                    <i class="fas fa-user-circle navbar-icon" title="Profil" style="font-size: 40px;"></i>
                <?php endif; ?>
            </a>
        </div>
    </div>
</div>

<body>
    <h1 style="text-align: center; margin-top: 20px;">Boutique</h1>
    <div class="container">
        <?php if (!empty($produits)) : ?>
            <?php foreach ($produits as $produit) : ?>
                <div class="card">
                    <?php if (!empty($produit['image'])): ?>
                        <!-- Affichage de l'image en base64 -->
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($produit['image']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                    <?php else: ?>
                        <p>Image non disponible</p>
                    <?php endif; ?>
                    <div class="info">
                        <h2><?php echo htmlspecialchars($produit['nom']); ?></h2>
                        <p class="price"><?php echo number_format($produit['prix'], 2); ?> €</p>
                    </div>
                    <button class="btn">Échanger</button>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p style="text-align: center;">Aucun produit disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>
