<?php
include __DIR__ . '/../../../requiments/database.php';
session_start();

// Vérification que l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Si l'utilisateur n'est pas connecté, on arrête le script
    die("Vous devez être connecté pour accéder à cette page.");
}

// Récupérer les paramètres de l'utilisateur dans la base de données
try {
    $stmt = $conn->prepare("SELECT * FROM user_settings WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

// Fermeture de la connexion
$conn = null;
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/import/css/navbar.css">
    <link rel="icon" href="/Import/icons/Logo.png" type="image/png">
    <link rel="stylesheet" href="/public/import/css/settings/securite.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Gestion du Compte</title>
</head>
<body>

    <div class="container">
        <div class="header">
            <a href="javascript:history.back()" class="back-arrow"><i class="fas fa-chevron-left"></i></a>
            <h2>Gestion du Compte</h2>
        </div>

        <form action="/traitements/Formulaires/compte.php" method="POST" id="accountForm">
            <!-- Section pour la confidentialité du compte -->
            <div class="form-section">
                <label>Confidentialité du compte :</label>
                <div class="settings-group">
                    <div>
                        <input type="radio" name="visibility" value="public" id="public" 
                        <?php echo (isset($user_settings['visibility']) && $user_settings['visibility'] == 'public') ? 'checked' : ''; ?>>
                        <label for="public">Public</label>
                    </div>
                    <div>
                        <input type="radio" name="visibility" value="private" id="private" 
                        <?php echo (isset($user_settings['visibility']) && $user_settings['visibility'] == 'private') ? 'checked' : ''; ?>>
                        <label for="private">Privé</label>
                    </div>
                </div>
            </div>

            <!-- Section pour gérer les publications -->
            <div class="form-section">
                <label>Qui peut voir vos publications ?</label>
                <div class="settings-group">
                    <div>
                        <input type="checkbox" name="visibility_posts[]" value="followers" id="followers" 
                        <?php echo (isset($user_settings['visibility_posts']) && in_array('followers', explode(',', $user_settings['visibility_posts']))) ? 'checked' : ''; ?>>
                        <label for="followers">Abonnés uniquement</label>
                    </div>
                    <div>
                        <input type="checkbox" name="visibility_posts[]" value="friends" id="friends" 
                        <?php echo (isset($user_settings['visibility_posts']) && in_array('friends', explode(',', $user_settings['visibility_posts']))) ? 'checked' : ''; ?>>
                        <label for="friends">Amis uniquement</label>
                    </div>
                    <div>
                        <input type="checkbox" name="visibility_posts[]" value="everyone" id="everyone" 
                        <?php echo (isset($user_settings['visibility_posts']) && in_array('everyone', explode(',', $user_settings['visibility_posts']))) ? 'checked' : ''; ?>>
                        <label for="everyone">Tout le monde</label>
                    </div>
                </div>
            </div>

            <!-- Section pour les notifications -->
            <div class="form-section">
                <label>Notifications :</label>
                <div class="settings-group">
                    <div>
                        <input type="checkbox" name="notifications" value="enabled" id="notifications_enabled" 
                        <?php echo (isset($user_settings['notifications']) && $user_settings['notifications'] == 'enabled') ? 'checked' : ''; ?>>
                        <label for="notifications_enabled">Activer les notifications</label>
                    </div>
                    <div>
                        <input type="checkbox" name="notifications" value="disabled" id="notifications_disabled" 
                        <?php echo (isset($user_settings['notifications']) && $user_settings['notifications'] == 'disabled') ? 'checked' : ''; ?>>
                        <label for="notifications_disabled">Désactiver les notifications</label>
                    </div>
                </div>
            </div>

            <!-- Section pour les autres paramètres -->
            <div class="form-section">
                <label>Autres paramètres :</label>
                <div class="settings-group">
                    <div>
                        <input type="checkbox" name="two_factor_auth" value="enabled" id="two_factor_enabled" 
                        <?php echo (isset($user_settings['two_factor_auth']) && $user_settings['two_factor_auth'] == 'enabled') ? 'checked' : ''; ?>>
                        <label for="two_factor_enabled">Activer l'authentification à deux facteurs</label>
                    </div>
                    <div>
                        <input type="checkbox" name="dark_mode" value="enabled" id="dark_mode_enabled" 
                        <?php echo (isset($user_settings['dark_mode']) && $user_settings['dark_mode'] == 'enabled') ? 'checked' : ''; ?>>
                        <label for="dark_mode_enabled">Mode sombre</label>
                    </div>
                </div>
            </div>

            <button type="submit">Enregistrer les paramètres</button>
        </form>
    </div>

</body>
</html>
