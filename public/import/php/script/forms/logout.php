<?php
include __DIR__ . '/../../requiments/database.php'; // Connexion à la base de données
session_start();

// Supprimer le jeton de connexion (si "Rester connecté" activé)
if (isset($_COOKIE["stay_connected"])) {
    $token = $_COOKIE["stay_connected"];

    // Supprimer le token de la base de données
    $sql_delete_token = "DELETE FROM user_tokens WHERE token = :token";
    $stmt_delete_token = $conn->prepare($sql_delete_token);
    $stmt_delete_token->bindParam(":token", $token);
    $stmt_delete_token->execute();

    // Supprimer le cookie du navigateur
    setcookie("stay_connected", "", time() - 3600, "/", "", false, true);
}

// Stocker temporairement les informations VIP et utilisateur (si besoin après la déconnexion)
$VIP = isset($_SESSION['VIP']) ? $_SESSION['VIP'] : false;
$prenom = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : null;
$nom = isset($_SESSION['nom']) ? $_SESSION['nom'] : null;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Stocker temporairement VIP dans un cookie pour 1h si nécessaire
setcookie("VIP", $VIP, time() + 3600, "/");

// Détruire la session et supprimer le cookie de session
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();

// Redémarrer une session propre si besoin de garder VIP
session_start();
$_SESSION['VIP'] = $VIP;
$_SESSION['prenom'] = $prenom;
$_SESSION['nom'] = $nom;
$_SESSION['email'] = $email;
$_SESSION['user_id'] = $user_id;

// Redirection vers la page d'accueil
header("Location: /publication.php");
exit();
?>
