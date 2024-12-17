<?php
// Inclure la connexion à la base de données
include '../../request/DB.php';

// Vérifier si l'utilisateur est bien connecté et a accès à cette page
session_start();
$loggedInUserID = $_SESSION['user_id'];

// Vérifier si l'ID de l'utilisateur est passé dans l'URL
if (isset($_GET['id'])) {
    $userID = $_GET['id'];

    // Vérifier si l'ID de l'utilisateur correspond à l'utilisateur connecté
    if ($userID != $loggedInUserID) {
        echo "Vous n'avez pas l'autorisation d'accéder à cette page.";
        exit;
    }

    // Récupérer les données de l'utilisateur pour afficher l'état actuel de display_info
    try {
        $sql = "SELECT display_info FROM utilisateur WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userID]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        $currentDisplayInfo = $userData['display_info'] ?? 1; // 1 si visible, 0 si caché
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        exit;
    }
} else {
    echo "ID utilisateur manquant.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la visibilité des informations</title>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers votre fichier CSS pour le style -->
</head>
<body>
    <div class="container">
        <h2>Modifier la visibilité de vos informations</h2>
        <form action="../../traitements/Formulaires/informations.php" method="POST" class="form">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userID); ?>">

            <div class="form-group">
                <label for="display_info">Afficher les informations</label>
                <select name="display_info" id="display_info" required>
                    <option value="1" <?php echo $currentDisplayInfo == 1 ? 'selected' : ''; ?>>Oui</option>
                    <option value="0" <?php echo $currentDisplayInfo == 0 ? 'selected' : ''; ?>>Non</option>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-submit">Mettre à jour</button>
            </div>
        </form>
    </div>

    <style>
        /* Style CSS pour rendre le formulaire plus moderne */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        .form-group select:focus {
            outline: none;
            border-color: #007bff;
        }

        .btn-submit {
            background-color: #007bff;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }
    </style>
</body>
</html>
