<?php
session_start();

// Stocker les informations spécifiques à l'utilisateur VIP dans des variables temporaires
$VIP = isset($_SESSION['VIP']) ? $_SESSION['VIP'] : false;
$prenom = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : null;
$nom = isset($_SESSION['nom']) ? $_SESSION['nom'] : null;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Nouvelle ligne

// Stocker la valeur de $VIP dans un cookie
setcookie("VIP", $VIP, time() + 3600, "/");

// Détruire la session
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();

// Redémarrer la session
session_start();

// Restaurer les informations spécifiques à l'utilisateur VIP dans la nouvelle session
$_SESSION['VIP'] = $VIP;
$_SESSION['prenom'] = $prenom;
$_SESSION['nom'] = $nom;
$_SESSION['email'] = $email;
$_SESSION['user_id'] = $user_id; // Nouvelle ligne

// Redirection vers l'index principal
header("Location: ../../menu.php");
exit();
?>
