<?php 
    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['user_id'])) {
        // Utilisateur connecté
        // Vérifier si l'utilisateur a déjà obtenu une étoile
        $checkSql = "SELECT etoile FROM utilisateur WHERE id = :id";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(":id", $_SESSION['user_id']);

        try {
            $checkStmt->execute();
            $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($row && $row['etoile'] >= 1) {
                // L'utilisateur a déjà obtenu une étoile
                echo '<p style="color: red; font-size: 20px;">étoile déjà obtenue</p>';
            } else {
                // L'utilisateur n'a pas encore obtenu d'étoile, afficher le lien
                echo '<a href="request/First_Star.php" style="background-color: #004beb;">Étoile de bienvenue</a>';
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        // Utilisateur non connecté
        echo '<p style="color: red; font-size: 20px;">Compte nécessaire</p>';
    }
    ?>