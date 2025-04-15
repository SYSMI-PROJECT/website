<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/import/css/navbar.css?v=0.1">
    <title>Avis des Utilisateurs</title>
    <style>

        .container {
            background: rgba(0, 0, 0, 0.85);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #ffd700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
            font-size: 2.5em;
        }

        #reviews {
            margin-bottom: 30px;
        }

        .review {
            padding: 20px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s ease;
        }

        .review:hover {
            transform: scale(1.03);
        }

        .review h2 {
            margin: 0 0 10px;
            color: #00d9ff;
            font-size: 1.5em;
        }

        .review p {
            margin: 0;
            color: #f0f0f0;
            line-height: 1.5;
            font-size: 1.1em;
        }

        .review small {
            display: block;
            margin-top: 10px;
            color: #a0a0a0;
            font-size: 0.9em;
        }

        form {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }

        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        input, textarea {
            background: rgba(255, 255, 255, 0.8);
            color: #333;
        }

        input::placeholder, textarea::placeholder {
            color: #777;
        }

        button {
            background: #00b53a;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background: #007b25;
            transform: scale(1.05);
        }

        .no-reviews {
            text-align: center;
            font-size: 1.2em;
            color: #ddd;
            margin-top: 20px;
        }

        .testimonials {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .testimonial {
            background-color: #FFF;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 30px;
            width: 720px;
            position: relative;
            text-align: left;
            transition: transform 0.3s, box-shadow 0.3s;
            }

        .testimonial:hover {
            transform: translateY(-10px);
            box-shadow: 0 5px 12px rgb(16 7 0);
        }

        .testimonial p {
            color: black;
        }

        .testimonial .author {
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }

        .testimonial-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        p.rating {
            font-size: 1.2em;
            color: #ff9900;
        }

        .feedback-profile {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            background-color: #f0f0f0;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            margin-right: 10px;
            object-fit: cover;
        }

        #testimonials-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

    </style>
</head>
<body>

<div class="navbar">
        <div class="navbar-logo">
            <a href="/index.php" target="_self">
                <img src="/public/img/icon/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
            </a>
        </div>
    </div>

    <div class="container">
        <h1>Avis utilisateurs</h1>
        <div id="testimonials-container">
        <?php include __DIR__ . '/../../../public/import/php/feedback.php' ?>
        </div>    
</body>
</html>
