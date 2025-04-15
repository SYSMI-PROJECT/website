<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur : Compte Existante</title>
    <link rel="stylesheet" href="/public/import/css/mail/mail_exist.css">
</head>
<body>
    <div class="error-container">
        <h1>Erreur : Compte Existant</h1>
        <p>Un compte avec cette adresse e-mail existe déjà. Si vous avez oublié votre mot de passe, vous pouvez le réinitialiser en cliquant sur le lien de réinitialisation.</p>
        <button class="back-button" onclick="window.history.back()">Retourner</button>
        <button class="login-button" onclick="window.location.href='../../formulaires/login.php'">Se Connecter</button>
    </div>
</body>
</html>
