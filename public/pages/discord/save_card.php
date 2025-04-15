<?php
header('Content-Type: application/json');

require_once('../../request/DB.php');

$response = array();

if (!$conn) {
    $response["success"] = false;
    $response["message"] = "La connexion a échoué : " . $conn->errorInfo()[2];
    echo json_encode($response);
    exit();
}

define('MAX_FILE_SIZE', 2 * 1024 * 1024);

if (isset($_FILES['image'])) {
    $image = $_FILES['image'];

    if ($image['size'] > MAX_FILE_SIZE) {
        $response["success"] = false;
        $response["message"] = "La taille de l'image ne doit pas dépasser 2 Mo.";
        echo json_encode($response);
        exit();
    }

    $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $response["success"] = false;
        $response["message"] = "Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés. ";
        echo json_encode($response);
        exit();
    }

    $targetDir = "../../Import/uploads/discord/";
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

$category = isset($_POST['category']) ? htmlspecialchars(trim($_POST['category'])) : '';
$cardName = isset($_POST['cardName']) ? htmlspecialchars(trim($_POST['cardName'])) : '';
$description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : '';
$link = isset($_POST['link']) ? trim($_POST['link']) : '';

if (empty($category) || empty($cardName) || empty($description) || empty($link)) {
    $response["success"] = false;
    $response["message"] = "Tous les champs sont requis.";
    echo json_encode($response);
    exit();
}

$discordLinkPattern = "/^https:\/\/discord\.(gg|com\/invite)\/[a-zA-Z0-9]+$/";
if (!preg_match($discordLinkPattern, $link)) {
    $response["success"] = false;
    $response["message"] = "Lien invalide. Seuls les liens de serveur Discord sont acceptés.";
    echo json_encode($response);
    exit();
}

try {
    $sql = "INSERT INTO cartes (category, cardName, description, link, image) VALUES (:category, :cardName, :description, :link, :image)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':cardName', $cardName);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':link', $link);
    $stmt->bindParam(':image', $targetFile);

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
$conn = null;
?>
