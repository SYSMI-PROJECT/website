<?php
session_start();
include __DIR__ . '/../../../../../../requiments/database.php';

// ✅ Si la requête est trop lourde (ex: post_max_size dépassé), $_FILES est vide
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_FILES)) {
    header('Location: /public/import/error/invald_format.php?type=taille');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $types_autorises = ['image/jpeg', 'image/png', 'image/gif'];
    $taille_min = 5 * 1024; // 5 Ko
    $taille_max = 5 * 1024 * 1024; // 5 Mo

    if (
        isset($_FILES['photo']) &&
        isset($_FILES['photo']['error']) &&
        $_FILES['photo']['error'] === UPLOAD_ERR_OK &&
        in_array($_FILES['photo']['type'], $types_autorises) &&
        $_FILES['photo']['size'] >= $taille_min &&
        $_FILES['photo']['size'] <= $taille_max
    ) {
        try {
            $conn->beginTransaction();
            $image_content = file_get_contents($_FILES['photo']['tmp_name']);
            $user_id = $_SESSION['user_id'];
            $horodatage = time();
            $nom_original = basename($_FILES['photo']['name']);
            $nom_fichier_unique = $horodatage . '_' . $nom_original;

            $stmt = $conn->prepare("
                UPDATE photos_de_profil
                SET nom_fichier = ?, image_content = ?
                WHERE user_id = ?
            ");
            $stmt->bindParam(1, $nom_fichier_unique);
            $stmt->bindParam(2, $image_content, PDO::PARAM_LOB);
            $stmt->bindParam(3, $user_id);
            $stmt->execute();

            $conn->commit();

            header("Location: /public/import/php/account.php?id=" . $user_id);
            exit;
        } catch (PDOException $e) {
            $conn->rollBack();
            echo 'Erreur lors de la mise à jour des données dans la base de données : ' . $e->getMessage();
        }
    } else {
        // Redirections vers la page d'erreur selon le type de problème
        if (isset($_FILES['photo']['error']) && $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            header('Location: /erreur_upload.php?type=erreur');
            exit;
        } elseif (isset($_FILES['photo']['type']) && !in_array($_FILES['photo']['type'], $types_autorises)) {
            header('Location: /erreur_upload.php?type=type');
            exit;
        } elseif (isset($_FILES['photo']['size']) && ($_FILES['photo']['size'] < $taille_min || $_FILES['photo']['size'] > $taille_max)) {
            header('Location: /public/import/error/test.php?type=taille');
            exit;
        } else {
            header('Location: /erreur_upload.php?type=inconnue');
            exit;
        }
    }
}

unset($conn);
?>
