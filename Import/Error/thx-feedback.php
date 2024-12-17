<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../icons/Logo.png" type="image/png">
    <title>Merci pour votre avis</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }
        .success-container {
            text-align: center;
            padding: 40px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(15px);
            max-width: 500px;
        }
        .success-container h1 {
            font-size: 46px;
            margin-bottom: 15px;
        }
        .success-container p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .emoji {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .stars {
            font-size: 24px;
            color: #ffd700;
            margin-bottom: 30px;
        }
        .redirect-button {
            background-color: #ff9800;
            color: #fff;
            padding: 14px 30px;
            border: none;
            border-radius: 25px;
            font-size: 20px;
            cursor: pointer;
            transition: transform 0.2s ease, background-color 0.3s;
        }
        .redirect-button:hover {
            background-color: #e68900;
            transform: scale(1.05);
        }
    </style>
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
        <button class="redirect-button" onclick="window.location.href='../../index.php'">Continuer</button>
    </div>
</body>
</html>
