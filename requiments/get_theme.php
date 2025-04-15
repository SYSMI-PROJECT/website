<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'database.php';
$defaultTheme = 'light';

if (isset($_SESSION['user_id'])) {
    $utilisateur_id = $_SESSION['user_id'];

    try {
        $sql = "SELECT theme FROM utilisateur WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $utilisateur_id, PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && isset($result['theme'])) {
            $theme = $result['theme'];
        } else {
            $theme = $defaultTheme;
        }
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération du thème : " . $e->getMessage();
        exit;
    }
} else {
    $theme = $defaultTheme;
}
if ($theme === 'noir') {
    $cssFile = '/public/import/css/pages/index.css';
} elseif ($theme === 'blanc') {
    $cssFile = '/public/import/css/Theme/white.css';
} else {
    $cssFile = '/public/import/css/Theme/white.css';
}
?>
