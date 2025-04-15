<?php
include __DIR__ . '/../../../../../requiments/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo "Utilisateur non connecté.";
        exit();
    }
    if (!isset($_POST['friend_id']) || !isset($_POST['action'])) {
        echo "Données POST manquantes.";
        exit();
    }

    $loggedInUserID = intval($_SESSION['user_id']);
    $friendID = intval($_POST['friend_id']);
    $action = $_POST['action'];

    if ($action === 'block') {
        $sql = "UPDATE relation 
                SET statut = 2, bloqueur = ? 
                WHERE (demandeur = ? AND receveur = ?) OR (demandeur = ? AND receveur = ?)";
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([$loggedInUserID, $loggedInUserID, $friendID, $friendID, $loggedInUserID]);
    } elseif ($action === 'unblock') {
        $sql = "UPDATE relation 
                SET statut = 1, bloqueur = NULL 
                WHERE (demandeur = ? AND receveur = ?) OR (demandeur = ? AND receveur = ?)";
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([$loggedInUserID, $friendID, $friendID, $loggedInUserID]);
    } else {
        echo "Action non valide.";
        exit();
    }

    if ($success) {
        header('Location: /public/pages/miscellaneous/UserList.php?success=relation_updated');
        exit();
    } else {
        echo "Erreur lors de la mise à jour de la relation.";
        exit();
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo "Méthode non autorisée.";
    exit();
}
?>
