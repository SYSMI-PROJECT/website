<?php
session_start();
header('Content-Type: application/json');

include __DIR__ . '/../../../../../requiments/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}
$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['publication_id'], $data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$publicationId = $data['publication_id'];
$action = $data['action'];

try {
    if ($action === 'like') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE publication_id = ? AND user_id = ?");
        $stmt->execute([$publicationId, $user_id]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $conn->prepare("INSERT INTO likes (publication_id, user_id) VALUES (?, ?)");
            $stmt->execute([$publicationId, $user_id]);
        }
    } elseif ($action === 'unlike') {
        $stmt = $conn->prepare("DELETE FROM likes WHERE publication_id = ? AND user_id = ?");
        $stmt->execute([$publicationId, $user_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Action invalide']);
        exit;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE publication_id = ?");
    $stmt->execute([$publicationId]);
    $likesCount = $stmt->fetchColumn();

    echo json_encode(['success' => true, 'likes' => $likesCount]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}
?>
