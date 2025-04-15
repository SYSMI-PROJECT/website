<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../../../../../../requiments/database.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_POST['user_id'];
    $displayInfo = $_POST['display_info'];

    // Vérifier que l'ID est valide et que la valeur display_info est correcte
    if (is_numeric($userID) && in_array($displayInfo, [0, 1])) {
        try {
            // Préparer la requête pour mettre à jour display_info dans la base de données
            $sql = "UPDATE utilisateur SET display_info = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);

            // Exécuter la mise à jour
            if ($stmt->execute([$displayInfo, $userID])) {
                // Rediriger vers la page de profil après la mise à jour
                header("Location: /public/import/php/account.php?id=" . $userID);
                exit;
            } else {
                echo "Erreur lors de la mise à jour des informations.";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Données invalides.";
    }
} else {
    echo "Aucune donnée reçue.";
}
?>
