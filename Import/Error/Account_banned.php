<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../icons/Logo.png" type="image/png">
    <title>Compte Banni</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
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
            font-size: 36px;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .back-button, .contact-button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-right: 10px;
        }
        .back-button:hover, .contact-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Compte Banni</h1>
        <p>Votre compte a été banni. Si vous pensez qu'il s'agit d'une erreur, veuillez contacter le support.</p>
        <button class="back-button" onclick="window.history.back()">Retourner en arrière</button>
        <button class="contact-button" onclick="window.location.href='mailto:support@votresite.com'">Contacter le support</button>
    </div>
</body>
</html>
