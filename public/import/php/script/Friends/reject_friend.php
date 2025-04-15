<?php
include __DIR__ . '/../../../../../requiments/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loggedInUserID = $_SESSION['user_id'];
    $requesterID = $_POST['requester_id'];

    $sql = "DELETE FROM relation WHERE demandeur = ? AND receveur = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$requesterID, $loggedInUserID])) {
        header('Location: /public/pages/miscellaneous/UserList.php');
    } else {
        echo "Erreur lors du rejet de la demande d'ami.";
    }
}
?>
