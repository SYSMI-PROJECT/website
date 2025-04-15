<?php
session_start();
include __DIR__ . '/../../../../../../requiments/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $types_autorises = ['image/jpeg', 'image/png', 'image/gif'];
    $taille_max = 5 * 1024 * 1024; // 5 Mo

    if (
        isset($_FILES['photo']['error']) &&
        $_FILES['photo']['error'] === UPLOAD_ERR_OK &&
        in_array($_FILES['photo']['type'], $types_autorises) &&
        $_FILES['photo']['size'] <= $taille_max
    ) {
        try {
            $conn->beginTransaction();

            $users_id = $_SESSION['user_id'];
            $horodatage = time();
            $nom_original = basename($_FILES['photo']['name']);
            $nom_fichier_unique = $horodatage . '_' . $nom_original;

            $type_image = $_FILES['photo']['type'];
            $image_content = file_get_contents($_FILES['photo']['tmp_name']);

            if ($type_image === 'image/gif') {
                $compressed_image_content = $image_content;
            } else {
                if (!function_exists('imagecreatefromstring')) {
                    throw new Exception("gd_extension");
                }

                $image = imagecreatefromstring($image_content);
                if (!$image) {
                    throw new Exception("image_traitement");
                }

                ob_start();
                imagejpeg($image, null, 75); // qualité 75
                $compressed_image_content = ob_get_contents();
                ob_end_clean();
                imagedestroy($image);
            }

            $stmt = $conn->prepare("INSERT INTO photos_de_profil (user_id, nom_fichier, image_content) VALUES (?, ?, ?)");
            $stmt->bindParam(1, $users_id);
            $stmt->bindParam(2, $nom_fichier_unique);
            $stmt->bindParam(3, $compressed_image_content, PDO::PARAM_LOB);

            $stmt->execute();
            $conn->commit();

            header("Location: /public/import/php/account.php?id=" . $users_id);
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            // Redirection avec type d'erreur en cas d'exception
            $typeErreur = $e->getMessage();
            header("Location: /public/import/error/invald_format.php?type=" . urlencode($typeErreur));
            exit;
        }
    } else {
        // Redirections selon les cas
        if (!isset($_FILES['photo']['error']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            header('Location: /public/import/error/invald_format.php?type=upload_error');
            exit;
        } elseif (!in_array($_FILES['photo']['type'], $types_autorises)) {
            header('Location: /public/import/error/invald_format.php?type=type');
            exit;
        } elseif ($_FILES['photo']['size'] > $taille_max) {
            header('Location: /public/import/error/invald_format.php?type=taille');
            exit;
        } else {
            header('Location: /public/import/error/invald_format.php?type=inconnue');
            exit;
        }
    }
}

unset($conn);
?>
