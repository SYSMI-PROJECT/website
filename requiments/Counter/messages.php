<?php
// Vérifier si une session est active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier de connexion à la base de données
include __DIR__ . '/../../requiments/database.php';

// Initialiser le compteur
$nbMessagesNonLus = 0;

try {
    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['user_id'])) {
        $loggedInUserID = $_SESSION['user_id'];

        // Requête SQL pour compter les messages non lus
        $sql = "SELECT COUNT(*) AS nb_non_lus
                FROM messages_prives
                WHERE destinataire_id = ? AND lu = 0";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$loggedInUserID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Nombre de messages non lus
        $nbMessagesNonLus = $result['nb_non_lus'];
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
