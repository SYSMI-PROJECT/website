<?php
include 'database.php';

// Si l'utilisateur est déjà connecté, ne rien faire
if (isset($_SESSION["user_id"])) {
    return;
}

// Vérification automatique du jeton de connexion
if (isset($_COOKIE["stay_connected"])) {
    $token = $_COOKIE["stay_connected"];

    // Vérifier si le jeton est valide
    $sql_check_token = "SELECT user_id FROM user_tokens WHERE token = :token AND expiry_date > NOW()";
    $stmt_check_token = $conn->prepare($sql_check_token);
    $stmt_check_token->bindParam(":token", $token);
    $stmt_check_token->execute();

    if ($stmt_check_token->rowCount() > 0) {
        $user_id = $stmt_check_token->fetch()["user_id"];

        // Récupérer les informations de l'utilisateur
        $sql_user = "SELECT id, nom, VIP FROM utilisateur WHERE id = :id";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bindParam(":id", $user_id);
        $stmt_user->execute();
        $user = $stmt_user->fetch();

        if ($user) {
            // Rétablir la session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["nom"];
            $_SESSION["VIP"] = $user["VIP"];

            // Rafraîchir le token (optionnel)
            $newExpiryDate = date("Y-m-d H:i:s", strtotime("+30 days"));
            $sql_update_token = "UPDATE user_tokens SET expiry_date = :expiry_date WHERE token = :token";
            $stmt_update_token = $conn->prepare($sql_update_token);
            $stmt_update_token->bindParam(":expiry_date", $newExpiryDate);
            $stmt_update_token->bindParam(":token", $token);
            $stmt_update_token->execute();
        }
    } else {
        // Supprimer le cookie si le jeton est invalide
        setcookie("stay_connected", "", time() - 3600, "/", "", false, true);
    }
}
