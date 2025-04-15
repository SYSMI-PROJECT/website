<?php
session_start();
include __DIR__ . '/../../../../../../requiments/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST["bio"]) && !empty($_POST["bio"])) {
        
        $bio = $_POST["bio"]; 
        $user_id = $_SESSION['user_id'];

        try {
            $stmt = $conn->prepare("UPDATE utilisateur SET bio = :bio WHERE id = :user_id");

            $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            $stmt->execute();

            header("Location: /public/import/php/account.php?id=" . $user_id);
            exit();
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
