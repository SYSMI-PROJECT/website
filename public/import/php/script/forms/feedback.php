<?php
session_start();

// Vérification si l'utilisateur est authentifié
if (!isset($_SESSION['user_id'])) {
    die("Accès refusé : utilisateur non authentifié.");
}

include __DIR__ . '/../../../../../requiments/database.php';

try {
    // Vérifier si $conn est bien initialisé
    if (!isset($conn)) {
        die("Erreur : Connexion à la base de données non établie.");
    }

    // Paramétrage des erreurs PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération sécurisée de l'ID utilisateur depuis la session
    $user_id = $_SESSION['user_id'];

    // Nettoyage et validation des entrées
    $feedback = trim($_POST['feedback'] ?? '');
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;

    // Validation des champs
    if (empty($feedback) || $rating < 1 || $rating > 5) {
        die("Veuillez remplir tous les champs correctement.");
    }

    // Préparation de la requête SQL pour insérer le feedback
    $query = "INSERT INTO feedback (user_id, feedback, rating) VALUES (:user_id, :feedback, :rating)";
    $stmt = $conn->prepare($query);

    // Liaison des paramètres avec les valeurs correspondantes
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':feedback', $feedback, PDO::PARAM_STR);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);

    // Exécution de la requête
    if ($stmt->execute()) {
        header("Location: /public/import/Error/thx-feedback.php");
        exit;
    } else {
        echo "Erreur lors de l'envoi de votre avis. Veuillez réessayer plus tard.";
    }
} catch (PDOException $e) {
    // En cas d'erreur avec la base de données, on affiche le message d'erreur
    die("Erreur : " . $e->getMessage());
}
?>
