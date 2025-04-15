<?php
// script.php

if (isset($bloc)) {
    if ($bloc == 'etoile') {
        // Bloc pour la colonne "étoile"
        if ($etoile >= 1000000) {
            $formattedEtoile = round($etoile / 1000000, 1) . 'M';
        } elseif ($etoile >= 1000) {
            $formattedEtoile = floor($etoile / 1000) . 'k';
        } else {
            $formattedEtoile = $etoile;
        }

        // Ajuster pour n'afficher qu'un seul 0 si $etoile est 0
        if ($etoile == 0) {
            echo '0';
        } else {
            echo $formattedEtoile . '';
        }
    }

    // ------------------------------------------- VIP COLONE ------------------------------------------- //

    if ($bloc == 'vip') {
        echo $VIP . '&nbsp';
    }

    // ------------------------------------------- XP UTILISATEUR ------------------------------------------- //

    if ($bloc == 'level') {
        if ($XP <= 99) {
            // Vérifiez si $XP est NULL et affichez '0' si c'est le cas
            echo is_null($XP) ? '0&nbsp;' : $XP . '&nbsp;';
        } else {
            $etoile = $etoile + 1;
    
            $sql = "UPDATE utilisateur SET etoile = COALESCE(etoile, 0) + 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
    
            if ($stmt === false) {
                echo "Erreur lors de la préparation de la requête : " . $conn->errorInfo();
            } else {
                $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    // Vérifiez si $XP ou $etoile sont NULL
                    echo '0';
                } else {
                    echo "Erreur lors de l'exécution de la requête : " . $stmt->errorInfo();
                }
            }
            $stmt->closeCursor();
        }
    }      
}
?>
