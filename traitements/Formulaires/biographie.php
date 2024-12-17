<?php
session_start();
require '../../request/DB.php'; // Inclure le fichier de connexion à la base de données

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Redirigez vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: login.php");
    exit();
}

// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Vérifiez si le champ de biographie est défini et non vide
    if (isset($_POST["bio"]) && !empty($_POST["bio"])) {
        
        // Récupérez la biographie depuis le formulaire
        $bio = $_POST["bio"]; // biographie de l'utilisateur
        $user_id = $_SESSION['user_id']; // identifiant de l'utilisateur connecté

        try {
            // Préparez la requête SQL pour mettre à jour la biographie
            $stmt = $conn->prepare("UPDATE utilisateur SET bio = :bio WHERE id = :user_id");

            // Liez les paramètres
            $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            // Exécutez la requête
            $stmt->execute();

            // Redirigez vers la page profile.php ou une autre page
            header("Location: ../../PAGES/user/profile.php?id=" . $user_id);
            exit(); // Assurez-vous de terminer le script après la redirection
        } catch(PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Le champ de biographie ne doit pas être vide.";
    }
} else {
    echo "Le formulaire n'a pas été soumis.";
}
?>
