<?php
// Vérifier si une session est déjà active
if (session_status() == PHP_SESSION_NONE) {
    // Démarrer la session
    session_start();
}

// Inclure le fichier de connexion à la base de données
include __DIR__ . '/../../requiments/database.php';

// Initialiser le compteur à zéro
$nbDemandes = 0;

try {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        // Si l'utilisateur n'est pas connecté, simplement retourner 0
        return 0;
    }
    $loggedInUserID = $_SESSION['user_id'];

    // Requête SQL pour compter les demandes d'amis en attente
    $sql = "SELECT COUNT(*) AS nb_demandes
            FROM relation r
            INNER JOIN utilisateur u ON r.demandeur = u.id
            WHERE r.receveur = ? AND r.statut = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$loggedInUserID]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nombre de demandes d'amis en attente
    $nbDemandes = $result['nb_demandes'];

} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
    // En cas d'erreur, retourner 0 ou gérer l'erreur selon vos besoins
    return 0;
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
    // En cas d'erreur, retourner 0 ou gérer l'erreur selon vos besoins
    return 0;
}

// Retourner le nombre de demandes
return $nbDemandes;
?>
