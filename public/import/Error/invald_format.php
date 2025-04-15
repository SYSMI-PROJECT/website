<?php
$type = $_GET['type'] ?? 'inconnue';

$messages = [
    'taille' => 'La taille du fichier est trop grande ou trop petite. Elle doit être comprise entre 5 Ko et 5 Mo.',
    'type' => 'Type de fichier non autorisé. Seuls les formats JPEG, PNG et GIF sont acceptés.',
    'erreur' => 'Une erreur s’est produite pendant le téléchargement.',
    'inconnue' => 'Une erreur inconnue est survenue.',
];

$message = $messages[$type] ?? $messages['inconnue'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/import/css/Error/invalid_format.css">
    <title>Erreur de Téléchargement</title>
</head>
<body>
    <div class="error-container">
        <div class="big-x">❌</div>
        <h1>Erreur lors du téléchargement</h1>
        <p><?= htmlspecialchars($message) ?></p>
        <a href="/public/import/php/account.php">
            <button class="back-button">⬅️ Retour au profil</button>
        </a>
        <a href="javascript:history.back();">
            <button class="retry-button">🔄 Réessayer</button>
        </a>
    </div>
</body>
</html>
