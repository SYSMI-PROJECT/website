<?php
session_start();
include __DIR__ . '/../../../requiments/UsersData.php';

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
    <link rel="icon" href="/public/img/icon/Logo.png" type="image/png">
    <title>Aide et Services</title>
    <link rel="stylesheet" href="/public/import/css/pages/service.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header>
        <h1>Aide et Services</h1>
    </header>

    <div class="container">
        <a href="/index.php" class="back-to-home" aria-label="Retour à l'accueil">
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
