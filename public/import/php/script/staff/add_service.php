<?php
session_start();
include __DIR__ . '/../../../../../requiments/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $serviceName = trim($_POST['service_name']);

    if (!empty($serviceName)) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_service'])) {
    $serviceId = (int) $_POST['service_id'];

    if ($serviceId > 0) {
        $deleteQuery = "DELETE FROM services WHERE id = :id";
        $deleteStmt = $conn->prepare($deleteQuery);
        
        try {
            $deleteStmt->execute([':id' => $serviceId]);
            $successMessage = "Service supprimé avec succès!";
        } catch (PDOException $e) {
            $errorMessage = "Erreur lors de la suppression du service : " . $e->getMessage();
        }
    }
}

$query = "SELECT id, nom_service FROM services ORDER BY nom_service ASC";
$stmt = $conn->query($query);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/import/css/navbar.css?v=0.1">
    <link rel="icon" href="/Import/icons/Logo.png" type="image/png">
    <title>Gestion des Services</title>
    <style>
        h2 {
            text-align: center;
            color: #333;
        }

        .form-container {
            margin-bottom: 2rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        button {
            padding: 0.75rem 1.25rem;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 1rem;
            font-weight: bold;
            text-align: center;
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #dc3545;
        }

        .service-list {
            margin-top: 2rem;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .service-list h3 {
            margin-bottom: 1rem;
            color: #333;
        }

        .service-list ul {
            list-style-type: none;
            padding: 0;
        }

        .service-list li {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .service-list li:last-child {
            border-bottom: none;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .delete-btn:active {
            background-color: #9f2d2d;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">
            <a href="/public/pages/miscellaneous/dashboard.php" target="_self">
                <img src="/public/img/icon/Logo.png" alt="Logo La SYSMI PROJECT" class="logo">
            </a>
        </div>
    </div>

    <div class="container">
        <h2>Gestion des Services</h2>
        <div class="form-container">
            <form action="add_service.php" method="POST">
                <label for="service_name">Nom du Service</label>
                <input type="text" id="service_name" name="service_name" required>
                <button type="submit" name="add_service">Ajouter le Service</button>
            </form>
        </div>

        <?php if (isset($successMessage)): ?>
            <div class="message success"><?= htmlspecialchars($successMessage) ?></div>
        <?php elseif (isset($errorMessage)): ?>
            <div class="message error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>
    </div>

        <div class="container" style="margin: -70px auto;">
        <div class="service-list">
            <h3>Services ajoutés</h3>
            <?php if (count($services) > 0): ?>
                <ul>
                    <?php foreach ($services as $service): ?>
                        <li>
                            <?= htmlspecialchars($service['nom_service']) ?>
                            <form action="add_service.php" method="POST" style="display: inline;">
                                <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                <button type="submit" name="delete_service" class="delete-btn">Supprimer</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucun service ajouté pour le moment.</p>
            <?php endif; ?>
        </div>
    
</body>
</html>
