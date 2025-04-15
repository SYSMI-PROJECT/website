<?php
session_start();
include '../../request/DB.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Utilisateur non connecté.']);
    exit();
}

// Vérifier que les données sont présentes
if (!isset($_POST['service_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes.']);
    exit();
}

$serviceId = (int)$_POST['service_id'];
$userId = $_SESSION['user_id'];

// Vérifier si l'utilisateur est déjà inscrit au service
$checkQuery = "SELECT * FROM service_utilisateur WHERE user_id = ? AND service_id = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->execute([$userId, $serviceId]);

if ($checkStmt->rowCount() > 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Vous êtes déjà inscrit à ce service.']);
    exit();
}

// Ajouter l'utilisateur au service
$insertQuery = "INSERT INTO service_utilisateur (user_id, service_id) VALUES (?, ?)";
$insertStmt = $conn->prepare($insertQuery);
$result = $insertStmt->execute([$userId, $serviceId]);

if ($result) {
    echo json_encode(['success' => 'Vous avez été inscrit au service avec succès.']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Une erreur est survenue lors de l\'inscription.']);
}
?>
