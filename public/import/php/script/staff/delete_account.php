<?php
require_once('../../../../../requiments/database.php');

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id_utilisateur = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM utilisateur WHERE id = :id");

        $stmt->bindParam(':id', $id_utilisateur);

        $stmt->execute();

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "ID de l'utilisateur non spécifié.";
}
?>
