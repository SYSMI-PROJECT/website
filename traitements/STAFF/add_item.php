<?php
// Inclure le fichier de configuration de la base de données
include '../../request/DB.php';

// Initialisation des variables pour les messages
$message = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données du formulaire
    $nom = sanitizeInput($_POST['nom']);
    $description = sanitizeInput($_POST['description']);
    $prix = filter_var($_POST['prix'], FILTER_VALIDATE_FLOAT);

    // Vérifier si un fichier a été téléchargé
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Vérifier le type de fichier et la taille
        $imageType = mime_content_type($_FILES['image']['tmp_name']);
        if (in_array($imageType, ['image/jpeg', 'image/png', 'image/gif'])) {
            // Lire le contenu du fichier image
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
        } else {
            $message = "Veuillez télécharger une image au format JPEG, PNG ou GIF.";
        }
    } else {
        $message = "Veuillez télécharger une image.";
    }

    // Vérifier que tous les champs sont remplis
    if (!empty($nom) && !empty($description) && $prix && isset($imageData)) {
        // Insérer les données dans la base de données
        $sql = "INSERT INTO produits (nom, description, prix, image) VALUES (:nom, :description, :prix, :image)";
        $params = [
            ':nom' => $nom,
            ':description' => $description,
            ':prix' => $prix,
            ':image' => $imageData
        ];
        $result = executeQuery($sql, $params);

        // Vérifier si l'ajout a réussi
        if ($result) {
            $message = "Produit ajouté avec succès !";
        } else {
            $message = "Erreur lors de l'ajout du produit.";
        }
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
    <link rel="icon" href="../../Logo.png" type="image/png">
    <title>Ajouter un produit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .form-container {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea, button {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            font-weight: bold;
            color: green;
        }
        .message.error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Ajouter un Produit</h1>
        <?php if (!empty($message)) : ?>
            <p class="message <?php echo strpos($message, 'succès') !== false ? '' : 'error'; ?>">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="nom">Nom du produit :</label>
            <input type="text" name="nom" id="nom" required>

            <label for="description">Description :</label>
            <textarea name="description" id="description" rows="5" required></textarea>

            <label for="prix">Prix (€) :</label>
            <input type="number" step="0.01" name="prix" id="prix" required>

            <label for="image">Image du produit :</label>
            <input type="file" name="image" id="image" accept="image/*" required>

            <button type="submit">Ajouter le produit</button>
        </form>
    </div>
</body>
</html>
