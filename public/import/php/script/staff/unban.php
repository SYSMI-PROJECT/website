<?php
require_once('../../../../../requiments/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_utilisateur = $_POST['id_utilisateur'];

    try {
        $stmt = $conn->prepare("UPDATE utilisateur SET statut = 'actif' WHERE id = :id_utilisateur");
        $stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: /public/pages/dashboard/staff/user_dashboard.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
