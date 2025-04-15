<?php
include __DIR__ . '/../../../../../requiments/database.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);
    $prix = filter_var($_POST['prix'], FILTER_VALIDATE_FLOAT);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageType = mime_content_type($_FILES['image']['tmp_name']);
        if (in_array($imageType, ['image/jpeg', 'image/png', 'image/gif'])) {
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
        } else {
            $message = "Veuillez télécharger une image au format JPEG, PNG ou GIF.";
        }
    } else {
        $message = "Veuillez télécharger une image.";
    }

    if (isset($_FILES['script']) && $_FILES['script']['error'] === UPLOAD_ERR_OK) {
        $scriptContent = file_get_contents($_FILES['script']['tmp_name']);
    } else {
        $message = "Veuillez téléverser un fichier script.";
    }

    if (!empty($nom) && !empty($description) && $prix && isset($imageData) && isset($scriptContent)) {
        $sql = "INSERT INTO produits (nom, description, prix, image, script_content) VALUES (:nom, :description, :prix, :image, :script_content)";
        $params = [
            ':nom' => $nom,
            ':description' => $description,
            ':prix' => $prix,
            ':image' => $imageData,
            ':script_content' => $scriptContent
        ];
        $result = executeQuery($sql, $params);

        $message = $result ? "Produit ajouté avec succès !" : "Erreur lors de l'ajout du produit.";
    } else {
        $message = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Import/css/navbar.css?v=0.1">
    <link rel="icon" href="/Import/icons/Logo.png" type="image/png">
    <title>Ajouter un produit</title>
    <style>
        .container { max-width: 500px; }
        input, textarea, button { margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 100%; }
        button { background-color: #4CAF50; color: white; border: none; cursor: pointer; padding: 10px; }
        button:hover { background-color: #45a049; }
        .message { text-align: center; margin-top: 15px; font-weight: bold; color: green; }
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

    <div class="container">
        <h1>Ajouter un Produit</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Nom du produit :</label>
            <input type="text" name="nom" required>
            <label>Description :</label>
            <textarea name="description" rows="5" required></textarea>
            <label>Prix (€) :</label>
            <input type="number" step="0.01" name="prix" required>
            <label>Image du produit :</label>
            <input type="file" name="image" accept="image/*" required>
            <label>Script d'exécution (fichier PHP) :</label>
            <input type="file" name="script" accept=".php" required>
            <button type="submit">Ajouter le produit</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
    </div>
</body>
</html>
