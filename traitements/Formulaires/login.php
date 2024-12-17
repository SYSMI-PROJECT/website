<?php 
include '../../request/DB.php';

// Fonction pour vérifier si l'utilisateur est bloqué après plusieurs tentatives infructueuses
function checkBruteForce($email) {
    global $conn;
    $sql = "SELECT count(*) AS attempts FROM login_attempts WHERE email = :email AND timestamp > (NOW() - INTERVAL 1 HOUR)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['attempts'];
}

// Vérifier si l'utilisateur est bloqué après plusieurs tentatives infructueuses
function isUserBlocked($email) {
    $attempts = checkBruteForce($email);
    return $attempts >= 3; // Bloquer l'utilisateur après 3 tentatives infructueuses
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (isUserBlocked($email)) {
        header("Location: ../../Import/Error/Acconte_blocked.php");
        exit();
    }
    
    // Vérifier si l'utilisateur est banni
    $sql_banned = "SELECT banni FROM utilisateur WHERE email = :email";
    $stmt_banned = $conn->prepare($sql_banned);
    $stmt_banned->bindParam(":email", $email);
    $stmt_banned->execute();
    $row_banned = $stmt_banned->fetch(PDO::FETCH_ASSOC);

    if ($row_banned['banni'] == 1) {
        header("Location: ../../Import/Error/Account_banned.php");
        exit();
    }
    
    // Vérifier si l'utilisateur existe
    $sql = "SELECT id, nom, password, VIP, verified FROM utilisateur WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();

        if (password_verify($password, $row["password"])) {
            // Vérifier si le compte de l'utilisateur est vérifié
            if ($row['verified'] == 1) {
                session_start();
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["username"] = $row["nom"];

                // Récupérer les informations spécifiques à l'utilisateur VIP depuis la session
                $_SESSION["VIP"] = isset($_SESSION['VIP']) ? $_SESSION['VIP'] : false;
                $_SESSION["prenom"] = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : null;
                $_SESSION["nom"] = isset($_SESSION['nom']) ? $_SESSION['nom'] : null;
                $_SESSION["email"] = isset($_SESSION['email']) ? $_SESSION['email'] : null;
                $_SESSION["user_id"] = $row["id"]; // Nouvelle ligne

                // Redirection vers l'index principal
                header("Location: ../../menu.php");
                exit();
            } else {
                // Redirection vers une page d'erreur si le compte n'est pas vérifié
                header("Location: ../../Import/Error/not-verify.php");
                exit();
            }
        } else {
            // Enregistrer la tentative de connexion infructueuse dans la base de données
            $sql = "INSERT INTO login_attempts (email, timestamp) VALUES (:email, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            header("Location: ../../Import/Error/Password_incorrect.php");
            exit();
        }
    } else {
        header("Location: ../../Import/Error/Not_Accounte_exist.php");
        exit();
    }
}
?>
