<?php
include '../../request/DB.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loggedInUserID = $_SESSION['user_id'];
    $requesterID = $_POST['requester_id'];

    $sql = "UPDATE relation SET statut = 1 WHERE demandeur = ? AND receveur = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$requesterID, $loggedInUserID])) {
        header('Location: ../../PAGES/Divers/UserList.php');
    } else {
        echo "Erreur lors de l'acceptation de la demande d'ami.";
    }
}
?>