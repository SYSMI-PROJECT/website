<?php
session_start();
include '../../request/DB.php'; // Inclure le fichier de connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Types de fichiers autorisés et taille limite
    $types_autorises = ['image/jpeg', 'image/png', 'image/gif'];
    $taille_min = 5 * 1024; // 5 Ko en octets
    $taille_max = 5 * 1024 * 1024; // 5 Mo en octets

    // Vérifiez si le fichier a été correctement téléchargé
    if (
        isset($_FILES['photo']['error']) &&
        $_FILES['photo']['error'] === UPLOAD_ERR_OK &&
        in_array($_FILES['photo']['type'], $types_autorises) &&
        $_FILES['photo']['size'] >= $taille_min &&
        $_FILES['photo']['size'] <= $taille_max
    ) {
        try {
            // Commencez une transaction
            $conn->beginTransaction();

            // Récupérez le contenu de l'image
            $image_content = file_get_contents($_FILES['photo']['tmp_name']);
            $user_id = $_SESSION['user_id'];
            $horodatage = time();
            $nom_original = basename($_FILES['photo']['name']);
            $nom_fichier_unique = $horodatage . '_' . $nom_original;

            // Préparez et exécutez la requête de mise à jour
            $stmt = $conn->prepare("
                UPDATE photos_de_profil
                SET nom_fichier = ?, image_content = ?
                WHERE user_id = ?
            ");
            $stmt->bindParam(1, $nom_fichier_unique);
            $stmt->bindParam(2, $image_content, PDO::PARAM_LOB);
            $stmt->bindParam(3, $user_id);

            $stmt->execute();

            // Validez et confirmez la transaction
            $conn->commit();

            // Redirigez vers la page de profil après succès
            header("Location: ../../PAGES/user/profile.php?id=" . $user_id);
            exit;
        } catch (PDOException $e) {
            // En cas d'erreur, annulez la transaction
            $conn->rollBack();
            echo 'Erreur lors de la mise à jour des données dans la base de données : ' . $e->getMessage();
        }
    } else {
        // Gérer les erreurs de téléchargement de fichier
        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            echo 'Erreur lors du téléchargement du fichier. Code d\'erreur : ' . $_FILES['photo']['error'];
        } elseif (!in_array($_FILES['photo']['type'], $types_autorises)) {
            echo 'Type de fichier non autorisé. Seules les images JPEG, PNG et GIF sont autorisées.';
        } elseif ($_FILES['photo']['size'] < $taille_min || $_FILES['photo']['size'] > $taille_max) {
            echo 'La taille du fichier doit être comprise entre 5 Ko et 5 Mo.';
        } else {
            echo 'Erreur inconnue lors du téléchargement du fichier.';
        }
    }
}

// Fermez la connexion à la base de données
unset($conn);
?>
