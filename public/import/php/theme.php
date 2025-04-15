<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../../../requiments/database.php';

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer la valeur du thème sélectionné
    $theme = $_POST['theme'];

    // Assurez-vous que la valeur est soit 'blanc', soit 'noir'
    if ($theme === 'blanc' || $theme === 'noir') {
        // Vérifier si l'utilisateur est connecté et récupérer son ID depuis la session
        if (isset($_SESSION['user_id'])) {
            $utilisateur_id = $_SESSION['user_id']; // ID utilisateur provenant de la session

            // Préparer la requête avec PDO
            $sql = "UPDATE utilisateur SET theme = :theme WHERE id = :id";

            // Préparer l'exécution de la requête
            $stmt = $conn->prepare($sql);

            // Lier les paramètres
            $stmt->bindParam(':theme', $theme, PDO::PARAM_STR);
            $stmt->bindParam(':id', $utilisateur_id, PDO::PARAM_INT);

            // Exécuter la requête
            if ($stmt->execute()) {
                header("Location: /index.php");
            } else {
                echo "Erreur lors de la mise à jour du thème.";
            }
        } else {
            echo "Utilisateur non connecté.";
        }
    } else {
        echo "Thème invalide.";
    }
}
?>
