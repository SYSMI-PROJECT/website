<?php
include __DIR__ . '/../../../../../requiments/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loggedInUserID = $_SESSION['user_id'];
    $friendID = $_POST['friend_id'];

    $sql = "DELETE FROM relation WHERE (demandeur = ? AND receveur = ?) OR (demandeur = ? AND receveur = ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$loggedInUserID, $friendID, $friendID, $loggedInUserID])) {
        header('Location: /public/pages/miscellaneous/UserList.php');
    } else {
        echo "Erreur lors de la suppression de l'ami.";
    }
}
?>
