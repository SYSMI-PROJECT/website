<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include __DIR__ . '/../../../../../requiments/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = htmlspecialchars($_POST["prenom"]);
    $nom = htmlspecialchars($_POST["nom"]);
    $email = htmlspecialchars($_POST["email"]);
    $motDePasse = htmlspecialchars($_POST["password"]);
    $codeVIP = htmlspecialchars($_POST["code_vip"]);

    if (empty($prenom)) {
        header("Location: error_prenom.php");
        exit();
    }
    if (empty($nom)) {
        header("Location: error_nom.php");
        exit();
    }
    if (empty($codeVIP)) {
        header("Location: error_code_vip.php");
        exit();
    }

    $existingUserSql = "SELECT id, prenom, nom, VIP, secret_code FROM utilisateur WHERE email = :email LIMIT 1";
    $existingUserStmt = $conn->prepare($existingUserSql);
    $existingUserStmt->bindParam(":email", $email);
    $existingUserStmt->execute();
    $existingUser = $existingUserStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        if ($existingUser['prenom'] != $prenom) {
            header("Location: ../Error/Error_VIP.php");
            exit();
        }
        if ($existingUser['nom'] != $nom) {
            header("Location: ../Error/Error_VIP.php");
            exit();
        }

        if ($existingUser['secret_code'] == $codeVIP) {
            if ($existingUser['VIP'] != 1) {
                $updateSqlVIP = "UPDATE utilisateur SET VIP = 1 WHERE id = :id";
                $updateSqlEtoile = "UPDATE utilisateur SET etoile = etoile + 500 WHERE id = :id";

                $updateStmtVIP = $conn->prepare($updateSqlVIP);
                $updateStmtEtoile = $conn->prepare($updateSqlEtoile);

                $updateStmtVIP->bindParam(":id", $existingUser['id']);
                $updateStmtEtoile->bindParam(":id", $existingUser['id']);

                if ($updateStmtVIP->execute() && $updateStmtEtoile->execute()) {
                    $_SESSION['VIP'] = true;
                    $_SESSION['prenom'] = $prenom;
                    $_SESSION['nom'] = $nom;
                    $_SESSION['email'] = $email;

                    header("Location: /index.php");
                    exit();
                } else {
                    header("Location: error_db_update.php");
                    exit();
                }
            } else {
                header("Location: error_already_vip.php");
                exit();
            }
        } else {
            header("Location: ../Error/Error_VIP.php");
            exit();
        }
    } else {
        header("Location: error_user_not_found.php");
        exit();
    }
}
?>
