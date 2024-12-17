<?php
session_start();
include '../../request/DB.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Traitement du formulaire d'ajout de service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $serviceName = trim($_POST['service_name']);

    if (!empty($serviceName)) {
        // Préparer la requête d'insertion
        $insertQuery = "INSERT INTO services (nom_service) VALUES (:nom_service)";
        $insertStmt = $conn->prepare($insertQuery);
        
        try {
            $insertStmt->execute([':nom_service' => $serviceName]);
            $successMessage = "Service ajouté avec succès!";
        } catch (PDOException $e) {
            $errorMessage = "Erreur lors de l'ajout du service : " . $e->getMessage();
        }
    } else {
        $errorMessage = "Le nom du service ne peut pas être vide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1.5rem;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        form {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            font-size: 1rem;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 1rem;
            font-weight: bold;
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <header>
        <h1>Ajouter un Service</h1>
    </header>

    <div class="container">
        <form action="add_service.php" method="POST">
            <label for="service_name">Nom du Service</label>
            <input type="text" id="service_name" name="service_name" required>
            <button type="submit" name="add_service">Ajouter le Service</button>
        </form>

        <?php if (isset($successMessage)): ?>
            <div class="message success"><?= htmlspecialchars($successMessage) ?></div>
        <?php elseif (isset($errorMessage)): ?>
            <div class="message error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
