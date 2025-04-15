<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'database.php';
include 'profile.php';

// Récupérer l'ID de l'utilisateur depuis la session
$id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

// Vérifier si les informations de l'utilisateur sont déjà en session
if (isset($_SESSION['etoile'], $_SESSION['role'], $_SESSION['statut'], $_SESSION['prenom'], $_SESSION['nom'], $_SESSION['consol'], $_SESSION['email'], $_SESSION['secret_code'], $_SESSION['XP'])) {
    // Récupérer les informations depuis la session
    $etoile = $_SESSION['etoile'];
    $role = $_SESSION['role']; // Utilisation de 'role' au lieu de 'VIP'
    $statut = $_SESSION['statut']; // Ajouter la variable statut
    $prenom = $_SESSION['prenom'];
    $nom = $_SESSION['nom'];
    $email = $_SESSION['email'];
    $secretCode = $_SESSION['secret_code'];
    $consol = $_SESSION['consol'];
    $XP = $_SESSION['XP']; // Utilisation de 'XP' au lieu de 'xp'
    $theme = $_SESSION['theme'];
} elseif ($id !== null) {
    // Si les informations de l'utilisateur ne sont pas en session, les récupérer depuis la base de données
    $sql = "SELECT etoile, role, statut, prenom, nom, theme, email, consol, secret_code, xp FROM utilisateur WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id);

    try {
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Assigner les valeurs récupérées aux variables correspondantes
        $etoile = isset($row['etoile']) ? $row['etoile'] : null;
        $role = isset($row['role']) ? $row['role'] : null; // Remplacer 'VIP' par 'role'
        $statut = isset($row['statut']) ? $row['statut'] : 'actif'; // Récupérer le statut (actif ou banni)
        $prenom = isset($row['prenom']) ? $row['prenom'] : null;
        $nom = isset($row['nom']) ? $row['nom'] : null;
        $email = isset($row['email']) ? $row['email'] : null;
        $secretCode = isset($row['secret_code']) ? $row['secret_code'] : null;
        $consol = isset($row['consol']) ? $row['consol'] : null;
        $XP = isset($row['xp']) ? $row['xp'] : null;
        $theme = isset($row['theme']) ? $row['theme'] : 'blanc';

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
        $_SESSION['role'] = $role; // Stocker 'role' dans la session
        $_SESSION['statut'] = $statut; // Stocker 'statut' dans la session
        $_SESSION['prenom'] = $prenom;
        $_SESSION['nom'] = $nom;
        $_SESSION['email'] = $email;
        $_SESSION['secret_code'] = $secretCode;
        $_SESSION['XP'] = $XP; // Stocker 'XP' dans la session
        $_SESSION['theme'] = $theme;
    } catch (PDOException $e) {
        // Gérer les erreurs de la base de données si nécessaire
        echo "Erreur : " . $e->getMessage();
    }
} else {
    // Si user_id n'est pas défini dans la session
    $etoile = null;
    $role = null; // Initialiser role à null si l'utilisateur n'est pas en session
    $statut = null; // Initialiser statut à null si l'utilisateur n'est pas en session
    $prenom = null;
    $nom = null;
    $email = null;
    $secretCode = null;
    $consol = null;
    $XP = null; // Initialiser XP à null si l'utilisateur n'est pas en session
    $theme = null;
}

// Vérifier si l'utilisateur est banni et l'empêcher d'accéder à certaines pages
if ($statut === 'banni') {
    header("Location: ../../Import/Error/Account_banned.php");
    exit; // Arrêter l'exécution du script si l'utilisateur est banni
}
?>
