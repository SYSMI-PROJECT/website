<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur : Aucun Compte Trouvé</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }
        .error-container {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 20px;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .back-button, .signup-button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-right: 10px;
        }
        .back-button:hover, .signup-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Erreur : Aucun Compte Trouvé</h1>
        <p>Aucun compte associé à cette adresse e-mail n'a été trouvé. Veuillez vous inscrire pour créer un nouveau compte.</p>
        <button class="back-button" onclick="window.history.back()">Retourner</button>
        <button class="signup-button" onclick="window.location.href='../../formulaires/Signup.html'">S'inscrire</button>
    </div>
</body>
</html>
