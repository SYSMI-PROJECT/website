<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../icons/Logo.png" type="image/png">
    <link rel="stylesheet" href="/public/import/css/Error/thx_feedback.css">
    <title>Merci pour votre avis</title>
</head>
<body>
    <div class="success-container">
        <div class="emoji">😊</div>
        <h1>Merci pour votre avis !</h1>
        <p>Votre avis est précieux et nous aide à rendre La SYSMI PROJECT encore meilleure.</p>
        <!-- Section des étoiles -->
        <div class="stars">
            <!-- Remplacez le PHP par les étoiles dynamiques selon la note -->
            <?php 
            $rating = 5; // Exemple de note, remplacez par la valeur réelle
            echo str_repeat("⭐", $rating); 
            ?>
        </div>
        <button class="redirect-button" onclick="window.location.href='/index.php'">Continuer</button>
    </div>
</body>
</html>
