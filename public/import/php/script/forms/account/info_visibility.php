<?php
include __DIR__ . '/../../../../../../requiments/database.php';
session_start();

$loggedInUserID = $_SESSION['user_id'] ?? null;

if (!$loggedInUserID) {
    echo "Utilisateur non connecté.";
    exit;
}

if (isset($_GET['id'])) {
    $userID = $_GET['id'];

    if ($userID != $loggedInUserID) {
        echo "Vous n'avez pas l'autorisation d'accéder à cette page.";
        exit;
    }

    try {
        $sql = "SELECT display_info FROM utilisateur WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userID]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentDisplayInfo = $userData['display_info'] ?? 1;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        exit;
    }
} else {
    echo "ID utilisateur manquant.";
    exit;
}

include __DIR__ . '/../../../../../../public/forms/account/info_visibility.html';
?>
