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

try {
    // Préparer la requête de sélection
    $sql = "SELECT category, cardName, description, link, image FROM cartes";
    $stmt = $conn->prepare($sql);

    // Exécuter la requête
    if ($stmt->execute()) {
        $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response["success"] = true;
        $response["cards"] = $cards;
    } else {
        throw new Exception("Erreur : " . implode(" - ", $stmt->errorInfo()));
    }
} catch (Exception $e) {
    $response["success"] = false;
    $response["message"] = $e->getMessage();
}

echo json_encode($response);
$conn = null; // Fermer la connexion à la base de données
?>
