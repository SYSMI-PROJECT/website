<?php
include __DIR__ . '/../../../requiments/UsersData.php';

$sql = "SELECT id, nom, description, prix, image FROM produits";
$produits = executeQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/import/css/navbar.css">
    <link rel="icon" href="/public/img/icon/Logo.png" type="image/png">
    <link href="/public/import/css/pages/shop.css" rel="stylesheet">
    <title>Boutique</title>
</head>

<div class="navbar">
        <div class="navbar-logo">
            <a href="/index.php" target="_self">
                <img src="/public/img/icon/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
            </a>
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
