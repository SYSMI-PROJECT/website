<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../icons/Logo.png" type="image/png">
    <title>Page Indisponible</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: linear-gradient(135deg, #ff0000 0%, #1500c3 100%);
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
            box-shadow: 0 0 10px rgb(2 0 87);
        }
        h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .back-button {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #219a52;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Page Indisponible</h1>
        <p>Nous travaillons dur pour vous offrir une expérience exceptionnelle. Revenez bientôt !</p>
        <button class="back-button" onclick="window.history.back()">Retourner à la page précédente</button>
    </div>
</body>
</html>

