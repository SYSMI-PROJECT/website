<?php
header('Content-Type: application/json'); // Définir le type de contenu en JSON

require_once('../../request/DB.php'); // Inclure le fichier de connexion à la base de données

$response = array(); // Initialiser le tableau de réponse

// Vérifier la connexion à la base de données
if (!$conn) {
    $response["success"] = false;
    $response["message"] = "La connexion a échoué : " . $conn->errorInfo()[2];
    echo json_encode($response);
    exit();
}

// Taille maximale du fichier en octets (par exemple, 2 Mo)
define('MAX_FILE_SIZE', 2 * 1024 * 1024);

// Vérifier si des fichiers ont été envoyés
if (isset($_FILES['image'])) {
    $image = $_FILES['image']; // Obtenir les informations de l'image

    // Vérifier la taille du fichier
    if ($image['size'] > MAX_FILE_SIZE) {
        $response["success"] = false;
        $response["message"] = "La taille de l'image ne doit pas dépasser 2 Mo.";
        echo json_encode($response);
        exit();
    }

    // Vérifier si le fichier est une image
    $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $response["success"] = false;
        $response["message"] = "Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        echo json_encode($response);
        exit();
    }

    // Déplacer le fichier vers le dossier de destination
    $targetDir = "../../uploads/"; // Dossier de destination
    $targetFile = $targetDir . basename($image["name"]);
    if (!move_uploaded_file($image["tmp_name"], $targetFile)) {
        $response["success"] = false;
        $response["message"] = "Erreur lors de l'upload du fichier.";
        echo json_encode($response);
        exit();
    }
} else {
    $response["success"] = false;
    $response["message"] = "Aucune image n'a été téléchargée.";
    echo json_encode($response);
    exit();
}

// Obtenir les données du formulaire
$category = isset($_POST['category']) ? htmlspecialchars(trim($_POST['category'])) : '';
$cardName = isset($_POST['cardName']) ? htmlspecialchars(trim($_POST['cardName'])) : '';
$description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : '';
$link = isset($_POST['link']) ? trim($_POST['link']) : '';

// Valider les données du formulaire
if (empty($category) || empty($cardName) || empty($description) || empty($link)) {
    $response["success"] = false;
    $response["message"] = "Tous les champs sont requis.";
    echo json_encode($response);
    exit();
}

// Valider le lien Discord
$discordLinkPattern = "/^https:\/\/discord\.(gg|com\/invite)\/[a-zA-Z0-9]+$/";
if (!preg_match($discordLinkPattern, $link)) {
    $response["success"] = false;
    $response["message"] = "Lien invalide. Seuls les liens de serveur Discord sont acceptés.";
    echo json_encode($response);
    exit();
}

try {
    // Préparer la requête SQL
    $sql = "INSERT INTO cartes (category, cardName, description, link, image) VALUES (:category, :cardName, :description, :link, :image)";
    $stmt = $conn->prepare($sql);

    // Lier les paramètres
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':cardName', $cardName);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':link', $link);
    $stmt->bindParam(':image', $targetFile); // Enregistrer le chemin de l'image dans la base de données

    // Exécuter la requête
    if ($stmt->execute()) {
        $response["success"] = true;
        $response["message"] = "Nouvel enregistrement créé avec succès";
    } else {
        throw new Exception("Erreur lors de l'exécution de la requête : " . implode(" - ", $stmt->errorInfo()));
    }
} catch (Exception $e) {
    $response["success"] = false;
    $response["message"] = $e->getMessage();
}

echo json_encode($response);
$conn = null; // Fermer la connexion à la base de données
?>
