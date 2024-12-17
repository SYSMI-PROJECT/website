<?php
session_start();
include 'request/DB.php';
include 'request/UserInfo.php';
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil TikTok</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .profile-header {
            background-color: #fff;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .profile-header img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            border: 2px solid #ddd;
        }
        .profile-header h1 {
            margin: 10px 0 5px;
            font-size: 24px;
        }
        .profile-header p {
            color: #888;
            margin: 5px 0;
        }
        .profile-stats {
            display: flex;
            justify-content: center;
            margin: 15px 0;
        }
        .profile-stats div {
            margin: 0 15px;
            text-align: center;
        }
        .profile-stats div h2 {
            margin: 0;
            font-size: 18px;
        }
        .profile-stats div p {
            margin: 0;
            color: #888;
            font-size: 14px;
        }
        .follow-button {
            background-color: #fe2c55;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 20px;
            cursor: pointer;
        }
        .profile-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }
        .video-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin: 10px;
            overflow: hidden;
            width: 200px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .video-card img {
            width: 100%;
            height: auto;
        }
        .video-card p {
            padding: 10px;
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="profile-header">
        <?php if (!empty($image_content)): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($image_content); ?>" alt="Profile Picture" class="profile-picture">
        <?php endif; ?>
        <h1>zac_Xgamer</h1>
        <p>@zac_Xgamer</p>
        <div class="profile-stats">
            <div>
                <h2>123</h2>
                <p>Abonnements</p>
            </div>
            <div>
                <h2>4567</h2>
                <p>Abonnés</p>
            </div>
            <div>
                <h2>89k</h2>
                <p>Likes</p>
            </div>
        </div>
        <button class="follow-button">Suivre</button>
        <button class="follow-button">écrire</button>
    </div>
    <div class="profile-content">
        <div class="video-card">
            <img src="https://th.bing.com/th/id/OIP.jllbWulLNMbGDMHVg1niAQHaNK?rs=1&pid=ImgDetMain" alt="Vidéo 1">
            <p>Description de la vidéo 1</p>
        </div>
        <div class="video-card">
            <img src="https://th.bing.com/th/id/OIP.jllbWulLNMbGDMHVg1niAQHaNK?rs=1&pid=ImgDetMain" alt="Vidéo 2">
            <p>Description de la vidéo 2</p>
        </div>
        <div class="video-card">
            <img src="https://th.bing.com/th/id/OIP.jllbWulLNMbGDMHVg1niAQHaNK?rs=1&pid=ImgDetMain" alt="Vidéo 3">
            <p>Description de la vidéo 3</p>
        </div>
        <!-- Ajoutez autant de cartes vidéo que nécessaire -->
    </div>
</body>
</html>
