<?php
session_start();
include '../../request/DB.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userID = $_SESSION['user_id'];

$servicesQuery = "SELECT id, nom_service FROM services";
$servicesStmt = $conn->prepare($servicesQuery);
$servicesStmt->execute();
$services = $servicesStmt->fetchAll(PDO::FETCH_ASSOC);

$selectedServiceId = isset($_GET['service']) ? $_GET['service'] : null;
$usersData = [];

if ($selectedServiceId) {
    $usersQuery = "SELECT u.id, u.prenom, u.nom, u.bio, p.image_content
                   FROM utilisateur u
                   LEFT JOIN photos_de_profil p ON u.id = p.user_id
                   JOIN service_utilisateur su ON u.id = su.user_id
                   WHERE su.service_id = ?";
    $usersStmt = $conn->prepare($usersQuery);
    $usersStmt->execute([$selectedServiceId]);
    $usersData = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <title>Aide et Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #0056b3;
            --success-color: #00bf05;
            --error-color: #ff0000;
            --text-color: #333;
            --background-color: #f4f4f9;
            --border-color: #ddd;
            --box-shadow: rgba(0, 0, 0, 0.1);
            --hover-shadow: rgba(0, 0, 0, 0.2);
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        header {
            background-color: var(--primary-color);
            color: #fff;
            text-align: center;
            padding: 1rem;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .container {
            max-width: 1200px;
            margin: 6rem auto 2rem; /* Ajout d'une marge supérieure pour le header fixe */
            padding: 0 1.5rem;
        }

        .back-to-home {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            color: #fff;
            background-color: var(--success-color);
            cursor: pointer;
            text-align: center;
            margin-bottom: 2rem;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-to-home:hover {
            background-color: var(--secondary-color);
            transform: scale(1.05);
        }

        .back-to-home i {
            margin-right: 0.5rem;
        }

        label {
            font-weight: 600;
            color: var(--text-color);
            display: block;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }

        select {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            font-size: 1rem;
            background-color: #fff;
            box-shadow: 0 4px 8px var(--box-shadow);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        .participation-button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            color: #fff;
            background-color: var(--error-color);
            cursor: pointer;
            text-align: center;
            display: block;
            margin-bottom: 2rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .participation-button:hover {
            background-color: var(--secondary-color);
            transform: scale(1.05);
        }

        .user-list {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: center;
        }

        .user-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px var(--box-shadow);
            padding: 1.5rem;
            margin: 1rem 0;
            width: calc(25% - 1.5rem);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--border-color);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .user-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px var(--hover-shadow);
        }

        .user-card .profile-pic {
            margin-bottom: 1rem;
        }

        .user-card .profile-pic img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
            padding: 2px;
            transition: border-color 0.3s ease;
        }

        .user-card .profile-pic img:hover {
            border-color: var(--secondary-color);
        }

        .user-card h3 {
            margin: 0.5rem 0;
            color: var(--text-color);
            font-size: 1.25rem;
            font-weight: 600;
        }

        .user-card p {
            margin: 0.5rem 0;
            color: #555;
            font-size: 1rem;
        }

        .user-card .user-id {
            font-size: 0.9rem;
            color: #888;
        }

        .user-card .user-bio {
            font-size: 0.9rem;
            color: #666;
            margin: 1rem 0;
            padding: 0 1rem;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .user-card .view-profile {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            margin-top: 0.5rem;
            transition: color 0.3s ease;
        }

        .user-card .view-profile:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        footer {
            text-align: center;
            padding: 1rem;
            background-color: var(--primary-color);
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
            font-size: 0.9rem;
        }

        @media (max-width: 1024px) {
            .user-card {
                width: calc(33.333% - 1.5rem);
            }
        }

        @media (max-width: 768px) {
            .user-card {
                width: calc(50% - 1.5rem);
            }
        }

        @media (max-width: 480px) {
            .user-card {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <header>
        <h1>Aide et Services</h1>
    </header>

    <div class="container">
        <a href="../../index.php" class="back-to-home" aria-label="Retour à l'accueil">
            <i class="fas fa-home"></i> Retour à l'accueil
        </a>

        <label for="service-select">Sélectionnez un service :</label>
        <select id="service-select" name="service" aria-label="Sélectionnez un service">
            <option value="" disabled selected>Choisissez un service</option>
            <?php foreach ($services as $service): ?>
                <option value="<?= htmlspecialchars($service['id']) ?>" <?= ($selectedServiceId == $service['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($service['nom_service']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php if ($selectedServiceId): ?>
            <button class="participation-button" id="participate-btn">Je suis intéressé</button>
        <?php endif; ?>

        <div class="user-list" id="user-list">
            <?php if ($selectedServiceId && !empty($usersData)): ?>
                <?php foreach ($usersData as $userData): ?>
                    <div class="user-card">
                        <div class="profile-pic">
                            <a href="profile.php?id=<?= htmlspecialchars($userData['id']) ?>" aria-label="Voir le profil de <?= htmlspecialchars($userData['prenom']) . ' ' . htmlspecialchars($userData['nom']) ?>">
                                <?php if ($userData['image_content']): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($userData['image_content']) ?>" alt="Photo de profil de <?= htmlspecialchars($userData['prenom']) ?>">
                                <?php else: ?>
                                    <img src="https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg" alt="Avatar">
                                <?php endif; ?>
                            </a>
                        </div>
                        <h3><?= htmlspecialchars($userData['prenom']) . ' ' . htmlspecialchars($userData['nom']) ?></h3>
                        <p class="user-id">ID: <?= htmlspecialchars($userData['id']) ?></p>
                        <p class="user-bio"><?= htmlspecialchars($userData['bio']) ?></p>
                        <a href="../../Gestionnaire/profile.php?id=<?= htmlspecialchars($userData['id']) ?>" class="view-profile" aria-label="Voir le profil complet de <?= htmlspecialchars($userData['prenom']) ?>">Voir le profil</a>
                    </div>
                <?php endforeach; ?>
            <?php elseif ($selectedServiceId): ?>
                <p>Aucun utilisateur trouvé pour ce service.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('service-select').addEventListener('change', function () {
            const serviceId = this.value;
            if (serviceId) {
                window.location.href = `?service=${serviceId}`;
            }
        });

        document.getElementById('participate-btn')?.addEventListener('click', function () {
            const serviceId = document.getElementById('service-select').value;

            if (!serviceId) {
                alert("Veuillez sélectionner un service.");
                return;
            }

            fetch('add_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'service_id': serviceId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.success);
                    window.location.href = `?service=${serviceId}`;
                } else {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue.');
            });
        });
    </script>

</body>
</html>
