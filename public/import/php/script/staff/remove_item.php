<?php
include __DIR__ . '/../../../../../requiments/database.php';

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    $sql = "DELETE FROM produits WHERE id = :id";
    $params = [':id' => $delete_id];
    executeQuery($sql, $params);
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$sql = "SELECT id, nom, description, prix, image FROM produits";
$produits = executeQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Import/css/navbar.css?v=0.1">
    <link rel="icon" href="/Import/icons/Logo.png" type="image/png">
    <link rel="icon" href="../../Logo.png" type="image/png">
    <link href="../../Import/css/shop.css?v=0" rel="stylesheet">
    <title>Gestion des Produits</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        td {
            background-color: #fff;
        }
        .btn {
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            background-color: red;
            color: white;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: darkred;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">
            <a href="/PAGES/STAFF/Dashboard.php" target="_self">
                <img src="/Import/icons/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
            </a>
        </div>
    </div>

    <h1 style="text-align: center; margin-top: 20px;">Gestion des Produits</h1>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Nom du Produit</th>
                <th>Description</th>
                <th>Prix (€)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($produits)) : ?>
                <?php foreach ($produits as $produit) : ?>
                    <tr>
                        <td>
                            <?php if (!empty($produit['image'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($produit['image']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>" class="product-image">
                            <?php else: ?>
                                <p>Image non disponible</p>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                        <td><?php echo htmlspecialchars($produit['description']); ?></td>
                        <td><?php echo number_format($produit['prix'], 2); ?> €</td>
                        <td>
                            <a href="?delete_id=<?php echo $produit['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                <button class="btn">Supprimer</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Aucun produit disponible pour le moment.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
