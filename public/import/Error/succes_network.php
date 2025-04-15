<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../icons/Logo.png" type="image/png">
    <link rel="stylesheet" href="/public/import/css/Error/succes_network.css">
    <title>Insertion Réussie</title>
</head>
<body>
    <div class="success-container">
        <h1>Insertion Réussie</h1>
        <p>Le lien a été inséré avec succès dans la base de données.</p>
        <button class="redirect-button" onclick="window.location.href='/public/import/php/account.php?id=<?php echo htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8'); ?>'">Continuer</button>
    </div>
</body>
</html>
