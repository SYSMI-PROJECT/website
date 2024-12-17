<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'DB.php';

$nbDemandes = 0;

try {
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    $loggedInUserID = $_SESSION['user_id'];
    $sql = "SELECT COUNT(*) AS nb_demandes
            FROM relation r
            INNER JOIN utilisateur u ON r.demandeur = u.id
            WHERE r.receveur = ? AND r.statut = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$loggedInUserID]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $nbDemandes = $result['nb_demandes'];

} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
    return 0;
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
    return 0;
}

return $nbDemandes;
?>
