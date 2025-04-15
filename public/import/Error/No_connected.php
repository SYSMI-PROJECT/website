<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../icons/Logo.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/import/css/Error/No_connected.css">
    <title>Utilisateur Non Connecté</title>
    <style>

    </style>
</head>
<body>
    <div class="error-container">
        <!-- Icône Font Awesome pour l'erreur -->
        <i class="fas fa-exclamation-circle"></i>
        <h1>Erreur : Utilisateur Non Connecté</h1>
        <p>Veuillez vous connecter pour accéder à cette page.</p>
        <div class="button-container">
            <button class="login-button" onclick="history.back()">Retourner</button>
            <button class="home-button" onclick="window.location.href='../../formulaires/login.html'">Connexion</button>
        </div>
    </div>
</body>
</html>
