<?php
require_once('../../../../../requiments/database.php');

if (isset($_POST['id_utilisateur'])) {
    $id_utilisateur = $_POST['id_utilisateur'];

    try {
        $stmt = $conn->prepare("UPDATE utilisateur SET statut = 'banni' WHERE id = :id_utilisateur");
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();

        header("Location: /public/pages/dashboard/staff/user_dashboard.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
