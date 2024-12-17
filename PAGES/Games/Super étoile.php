<?php
session_start();
include '../../request/DB.php';

// Récupérer l'ID de l'utilisateur depuis la session
$id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

// Durée de validité de l'obtention de l'étoile en secondes (ici 10 secondes)
$etoileValidityDuration = 10; // 10 secondes

if ($id !== null) {
    // Vérifier si une étoile a déjà été obtenue dans cette session
    if (isset($_SESSION["etoile_obtenue"]) && $_SESSION["etoile_obtenue"] === true) {
        // Vérifier si la durée de validité est dépassée
        if (time() - $_SESSION["etoile_obtenue_time"] <= $etoileValidityDuration) {
            // Afficher un message d'erreur si l'utilisateur a déjà obtenu une étoile
            echo "<!DOCTYPE html>
            <html lang='fr'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Erreur</title>
                <style>
                    body { 
                        font-family: 'Arial', sans-serif; 
                        display: flex; 
                        justify-content: center; 
                        align-items: center; 
                        height: 100vh; 
                        margin: 0; 
                        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
                    }
                    .container {
                        background-color: #fff; 
                        border-radius: 15px; 
                        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); 
                        padding: 30px; 
                        text-align: center;
                        max-width: 400px;
                        width: 100%;
                        animation: fadeIn 1s ease-in-out;
                    }
                    .container img {
                        max-width: 100px;
                        margin: 0 auto 20px;
                        display: block;
                        transition: transform 0.3s;
                    }
                    .container img:hover {
                        transform: scale(1.1);
                    }
                    .title {
                        font-size: 2em;
                        color: #333;
                        margin-bottom: 20px;
                        font-weight: bold;
                    }
                    .message {
                        font-size: 1.2em;
                        color: #555;
                        margin-bottom: 20px;
                    }
                    .back-button {
                        font-size: 1em;
                        color: #fff;
                        background-color: #f00;
                        border: none;
                        padding: 10px 20px;
                        cursor: pointer;
                        border-radius: 5px;
                        text-decoration: none;
                        display: inline-block;
                    }
                    .back-button:hover {
                        background-color: #555;
                    }
                    @keyframes fadeIn {
                        from {
                            opacity: 0;
                            transform: translateY(-10px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='title'>Erreur</div>
                    <div class='message'>
                        Vous avez déjà obtenu une étoile. Vous ne pouvez pas en obtenir une nouvelle pour l'instant.
                    </div>
                    <button class='back-button' onclick='history.back()'>Retour en arrière</button>
                </div>
            </body>
            </html>";
            exit();
        } else {
            // Si la validité est dépassée, permettre une nouvelle étoile et réinitialiser la session
            unset($_SESSION["etoile_obtenue"]);
            unset($_SESSION["etoile_obtenue_time"]);
        }
    } 

    try {
        // Vérifier la connexion à la base de données
        if (!$conn) {
            throw new Exception("Erreur de connexion à la base de données.");
        }

        // Incrémenter la valeur de la colonne 'etoile' dans la base de données
        $updateSql = "UPDATE utilisateur SET XP = COALESCE(XP, 0) + 10 WHERE id = :id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindParam(":id", $id, PDO::PARAM_INT);

        $updateStmt->execute();

        // Marquer que l'utilisateur a obtenu une étoile dans cette session et enregistrer le temps
        $_SESSION["etoile_obtenue"] = true;
        $_SESSION["etoile_obtenue_time"] = time();

        // Afficher un message de succès avec une image et rediriger après 5 secondes
        echo "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Félicitations</title>
            <style>
                body { 
                    font-family: 'Arial', sans-serif; 
                    display: flex; 
                    justify-content: center; 
                    align-items: center; 
                    height: 100vh; 
                    margin: 0; 
                    background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
                }
                .container {
                    background-color: #fff; 
                    border-radius: 15px; 
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); 
                    padding: 30px; 
                    text-align: center;
                    max-width: 400px;
                    width: 100%;
                    animation: fadeIn 1s ease-in-out;
                }
                .image-container {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 20px;
                }
                .image-container span {
                    font-size: 1.5em;
                    margin-right: 10px;
                    color: black;
                    font-weight: 600;
                }
                .image-container img {
                    max-width: 100px;
                    transition: transform 0.3s;
                }
                .image-container img:hover {
                    transform: scale(1.1);
                }
                .title {
                    font-size: 2em;
                    color: #333;
                    margin-bottom: 20px;
                    font-weight: bold;
                }
                .message {
                    font-size: 1.2em;
                    color: #555;
                    margin-bottom: 20px;
                }
                .countdown {
                    font-size: 1.2em;
                    color: #777;
                    margin-top: 10px;
                }
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            </style>
            <script>
                var seconds = 5;
                function countdown() {
                    var countdownElement = document.getElementById('countdown');
                    seconds--;
                    countdownElement.innerHTML = 'Vous serez redirigé dans ' + seconds + ' secondes...';
                    if (seconds <= 0) {
                        window.location.href = '" . htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'default_page.php') . "';
                    } else {
                        setTimeout(countdown, 1000);
                    }
                }
                setTimeout(countdown, 1000);
            </script>
        </head>
        <body>
            <div class='container'>
                <div class='title'>Félicitations !</div>
                <div class='message'>
                    Vous avez gagné.
                </div>
                <div class='image-container'>
                    <span>x10</span>
                    <img src='https://production-gameflipusercontent.fingershock.com/us-east-1:283d9bf8-ddfd-4a29-82be-3e03e84897dc/932f60c4-6f5b-433c-b5ca-9e1979faf095/041aedbd-70d5-4996-98f4-1e3ba9d4175a' alt='Félicitations'>
                </div>
                <div class='countdown' id='countdown'>Vous serez redirigé dans 5 secondes...</div>
            </div>
        </body>
        </html>";
        exit();
    } catch (PDOException $e) {
        // Gérer les erreurs de la base de données si nécessaire
        echo "Erreur de base de données : " . $e->getMessage();
    } catch (Exception $e) {
        // Gérer les autres erreurs
        echo "Erreur : " . $e->getMessage();
    }
} else {
    // Gérer le cas où user_id n'est pas défini dans la session
    header("Location: ../../Error/Not_Accounte_exist.php");
}
?>
