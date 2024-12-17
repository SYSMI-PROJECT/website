<?php
// Inclure votre fichier de connexion à la base de données
include '../../request/DB.php';

// Démarrer la session
session_start();

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'ID de l'utilisateur connecté
    $loggedInUserID = $_SESSION['user_id'];

    // Récupérer l'ID du destinataire depuis les données POST
    $receiverID = $_POST['receiver_id'];

    // Vérifier si une demande existe déjà entre ces deux utilisateurs
    $sql_check = "SELECT * FROM relation WHERE (demandeur = ? AND receveur = ?) OR (demandeur = ? AND receveur = ?)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$loggedInUserID, $receiverID, $receiverID, $loggedInUserID]);

    // Vérifier s'il existe déjà une demande
    if ($stmt_check->rowCount() > 0) {
        echo "Une demande d'ami est déjà en cours pour cet utilisateur.";
        exit(); // Arrêter l'exécution du script si une demande existe déjà
    }

    // Insérer une nouvelle demande d'ami dans la base de données
    $sql_insert = "INSERT INTO relation (demandeur, receveur, statut) VALUES (?, ?, 0)";
    $stmt_insert = $conn->prepare($sql_insert);

    // Exécuter la requête d'insertion
    if ($stmt_insert->execute([$loggedInUserID, $receiverID])) {
        // Rediriger vers le profil de l'utilisateur destinataire après l'insertion réussie
        header('Location: ../../PAGES/Divers/UserList.php');
        exit(); // Arrêter l'exécution du script après la redirection
    } else {
        echo "Erreur lors de l'envoi de la demande d'ami.";
    }
}
?>