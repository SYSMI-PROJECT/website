<?php
include __DIR__ . '/../../../../../requiments/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loggedInUserID = $_SESSION['user_id'];

    $receiverID = $_POST['receiver_id'];

    $sql_check = "SELECT * FROM relation WHERE (demandeur = ? AND receveur = ?) OR (demandeur = ? AND receveur = ?)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$loggedInUserID, $receiverID, $receiverID, $loggedInUserID]);

    if ($stmt_check->rowCount() > 0) {
        echo "Une demande d'ami est déjà en cours pour cet utilisateur.";
        exit();
    }

    $sql_insert = "INSERT INTO relation (demandeur, receveur, statut) VALUES (?, ?, 0)";
    $stmt_insert = $conn->prepare($sql_insert);

    if ($stmt_insert->execute([$loggedInUserID, $receiverID])) {
        header('Location: /public/pages/miscellaneous/UserList.php');
    } else {
        echo "Erreur lors de l'envoi de la demande d'ami.";
    }
}
?>