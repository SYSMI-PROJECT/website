<?php
header('Content-Type: application/json');

require_once('../../../requiments/database.php');
$response = array();

if (!$conn) {
    $response["success"] = false;
    $response["message"] = "La connexion a échoué : " . $conn->errorInfo()[2];
    echo json_encode($response);
    exit();
}

try {
    $sql = "SELECT category, cardName, description, link, image FROM cartes";
    $stmt = $conn->prepare($sql);

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
$conn = null;
?>
