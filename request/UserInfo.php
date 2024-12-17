<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'DB.php';
include 'profile.php';

// Récupérer l'ID de l'utilisateur depuis la session
$id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

// Vérifier si les informations de l'utilisateur sont déjà en session
if (isset($_SESSION['etoile'], $_SESSION['VIP'], $_SESSION['prenom'], $_SESSION['nom'], $_SESSION['consol'], $_SESSION['email'], $_SESSION['secret_code'], $_SESSION['XP'])) {
    // Récupérer les informations depuis la session
    $etoile = $_SESSION['etoile'];
    $VIP = $_SESSION['VIP'];
    $prenom = $_SESSION['prenom'];
    $nom = $_SESSION['nom'];
    $email = $_SESSION['email'];
    $secretCode = $_SESSION['secret_code'];
    $consol = $_SESSION['consol'];
    $XP = $_SESSION['XP']; // Utilisation de 'XP' au lieu de 'xp'
} elseif ($id !== null) {
    // Si les informations de l'utilisateur ne sont pas en session, les récupérer depuis la base de données
    $sql = "SELECT etoile, VIP, prenom, nom, email, consol, secret_code, xp FROM utilisateur WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id);

    try {
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Assigner les valeurs récupérées aux variables correspondantes
        $etoile = isset($row['etoile']) ? $row['etoile'] : null;
        $VIP = isset($row['VIP']) ? $row['VIP'] : null;
        $prenom = isset($row['prenom']) ? $row['prenom'] : null;
        $nom = isset($row['nom']) ? $row['nom'] : null;
        $email = isset($row['email']) ? $row['email'] : null;
        $secretCode = isset($row['secret_code']) ? $row['secret_code'] : null;
        $consol = isset($row['consol']) ? $row['consol'] : null;
        $XP = isset($row['xp']) ? $row['xp'] : null;

        // Si le code secret n'est pas défini, générer et enregistrer un nouveau
        if (!$secretCode) {
            $secretCode = uniqid();

            $updateSql = "UPDATE utilisateur SET secret_code = :secretCode WHERE id = :id";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(":secretCode", $secretCode);
            $updateStmt->bindParam(":id", $id);
            $updateStmt->execute();
        }

        // Stocker les informations de l'utilisateur en session pour une utilisation future
        $_SESSION['etoile'] = $etoile;
        $_SESSION['VIP'] = $VIP;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['nom'] = $nom;
        $_SESSION['email'] = $email;
        $_SESSION['secret_code'] = $secretCode;
        $_SESSION['XP'] = $XP; // Stocker 'XP' dans la session
    } catch (PDOException $e) {
        // Gérer les erreurs de la base de données si nécessaire
        echo "Erreur : " . $e->getMessage();
    }
} else {
    // Si user_id n'est pas défini dans la session
    $etoile = null;
    $VIP = null;
    $prenom = null;
    $nom = null;
    $email = null;
    $secretCode = null;
    $consol = null;
    $XP = null; // Initialiser XP à null si l'utilisateur n'est pas en session
}
?>
