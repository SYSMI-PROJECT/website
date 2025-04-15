<?php 
include __DIR__ . '/../../../../../requiments/database.php';
session_start();

// Vérification des sessions longues
ini_set('session.gc_maxlifetime', 2592000); // 30 jours
ini_set('session.cookie_lifetime', 2592000);

// Fonction pour générer un token sécurisé
function generateToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $stayConnected = isset($_POST["stayConnected"]); // Option "Rester connecté"

    // Vérifier si l'utilisateur existe
    $sql_check_user = "SELECT id, nom, password, VIP, statut, verified FROM utilisateur WHERE email = :email";
    $stmt_check_user = $conn->prepare($sql_check_user);
    $stmt_check_user->bindParam(":email", $email);
    $stmt_check_user->execute();

    if ($stmt_check_user->rowCount() > 0) {
        $row_user = $stmt_check_user->fetch();

        // Vérifier le mot de passe
        if (!password_verify($password, $row_user["password"])) {
            header("Location: /public/import/Error/incorrect_password.php");
            exit();
        }

        // Vérifier si le compte est banni ou non vérifié
        if ($row_user['statut'] != 'actif') {
            header("Location: /public/import/Error/Account_banned.php");
            exit();
        }
        if ($row_user['verified'] != 1) {
            header("Location: /public/import/Error/Account_not_verified.php");
            exit();
        }

        // Connexion réussie, création de la session
        $_SESSION["user_id"] = $row_user["id"];
        $_SESSION["username"] = $row_user["nom"];
        $_SESSION["VIP"] = isset($row_user['VIP']) ? $row_user['VIP'] : false;

        // Gestion du "Rester connecté"
        if ($stayConnected) {
            $token = generateToken();
            $expiryDate = date("Y-m-d H:i:s", strtotime("+30 days"));

            // Stocker le token en base
            $sql_token = "INSERT INTO user_tokens (user_id, token, expiry_date) VALUES (:user_id, :token, :expiry_date) 
                          ON DUPLICATE KEY UPDATE token = :token, expiry_date = :expiry_date";
            $stmt_token = $conn->prepare($sql_token);
            $stmt_token->bindParam(":user_id", $row_user["id"]);
            $stmt_token->bindParam(":token", $token);
            $stmt_token->bindParam(":expiry_date", $expiryDate);
            $stmt_token->execute();

            // Stocker le token dans un cookie sécurisé
            setcookie("stay_connected", $token, time() + (30 * 24 * 60 * 60), "/", "", false, true);
        }

        header("Location: /index.php");
        exit();
    } else {
        header("Location: /public/import/Error/Unknown_Account.php");
        exit();
    }
}
?>
