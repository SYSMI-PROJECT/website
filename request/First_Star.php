<?php
session_start();
include 'DB.php';

// Récupérez l'ID de l'utilisateur depuis la session
$id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

if ($id !== null) {
    // Vérifiez si l'utilisateur n'a pas encore obtenu d'étoile
    $checkSql = "SELECT etoile FROM utilisateur WHERE id = :id";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(":id", $id);

    try {
        $checkStmt->execute();
        $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // Vérifiez si la colonne 'etoile' est inférieure à un certain seuil (par exemple, 1)
        if ($row['etoile'] < 1) {
            // Incrémentez la valeur de la colonne 'etoile' dans la base de données
            $updateSql = "UPDATE utilisateur SET etoile = COALESCE(etoile, 0) + 1 WHERE id = :id";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(":id", $id);

            try {
                $updateStmt->execute();
                
                // Redirigez l'utilisateur vers la page souhaitée après l'échange
                header("Location: ../index.php");
                exit();
            } catch (PDOException $e) {
                // Gérez les erreurs de la base de données si nécessaire
                echo "Erreur : " . $e->getMessage();
            }
        } else {
            header("Location: ../Error/First_Star.php");
            exit();
        }
    } catch (PDOException $e) {
        // Gérez les erreurs de la base de données si nécessaire
        echo "Erreur : " . $e->getMessage();
    }
} else {
    // Gérez le cas où user_id n'est pas défini dans la session
    echo "Erreur : Identifiant d'utilisateur non défini.";
}
?>
