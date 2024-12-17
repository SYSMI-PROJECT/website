<?php
include '../../request/DB.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loggedInUserID = $_SESSION['user_id'];
    $receiverID = $_POST['receiver_id'];

    $sql = "DELETE FROM relation WHERE demandeur = ? AND receveur = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$loggedInUserID, $receiverID])) {
        header('Location: ../../PAGES/Divers/UserList.php');
    } else {
        echo "Erreur lors de l'annulation de la demande d'ami.";
    }
}
?>
