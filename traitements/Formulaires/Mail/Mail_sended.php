<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de votre adresse e-mail</title>
    <style>
        body {
            background-image: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            background-size: cover;
            background-position: center;
            font-family: 'Roboto', sans-serif;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }

        .card {
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 5px;
            text-align: left;
        }

        h1 {
            color: #333333;
            margin-bottom: 20px;
        }

        p {
            color: #666666;
            margin-bottom: 10px;
            text-align: center;
        }

        .footer {
            color: #666666;
            font-size: 14px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="../../../Logo.png" alt="Logo" class="logo">
        <h1>Votre compte est désactivé !</h1>
        <div class="card">
            <p>Merci de vous être inscrit ! 
                <br><br>
                Un e-mail de vérification a été envoyé à votre adresse e-mail. Veuillez vérifier votre boîte de réception et cliquer sur le lien de vérification pour activer votre compte.</p>
        </div>
        <p class="footer">© 2024 Votre Société. Tous droits réservés.</p>
    </div>
</body>
</html>
