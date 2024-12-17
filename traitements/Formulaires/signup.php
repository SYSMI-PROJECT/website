<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../Import/vendor/autoload.php';
require '../../request/DB.php'; // Assurez-vous que le fichier DB.php est inclus et qu'il établit une connexion à la base de données

// Fonction pour vérifier si une chaîne contient un "@" (email-like)
function containsEmail($str) {
    return strpos($str, '@') !== false;
}

// Fonction pour vérifier le domaine de l'e-mail
function isValidEmailDomain($email, $allowedDomains) {
    $emailDomain = substr(strrchr($email, "@"), 1);
    return in_array($emailDomain, $allowedDomains);
}

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et validation des données du formulaire
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $motDePasse = $_POST['motDePasse'];
    $pays = trim($_POST['pays']);

    // Validation des données
    if (empty($nom) || empty($prenom) || empty($email) || empty($motDePasse) || empty($pays)) {
        echo "Veuillez remplir tous les champs du formulaire.";
        exit;
    }

    // Validation de l'adresse email
    if (!$email) {
        echo "Veuillez fournir une adresse email valide.";
        exit;
    }

    // Vérification si le prénom contient un "@" (élément d'adresse email)
    if (containsEmail($prenom)) {
        echo "Le prénom ne peut pas contenir d'éléments d'adresse email.";
        exit;
    }

    // Vérification si le nom contient un "@" (élément d'adresse email)
    if (containsEmail($nom)) {
        echo "Le nom ne peut pas contenir d'éléments d'adresse email.";
        exit;
    }

    // Vérification de la force du mot de passe
    if (strlen($motDePasse) < 8) {
        echo "Le mot de passe doit contenir au moins 8 caractères.";
        exit;
    }

    // Vérification du domaine de l'e-mail
    $allowedDomains = [
        'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com', 'aol.com', 
        'mail.com', 'zoho.com', 'yandex.com', 'protonmail.com', 'gmx.com', 'lycos.com', 
        'fastmail.com', 'hushmail.com', 'live.com', 'msn.com', 'me.com', 'inbox.com',
        'apple.com', 'att.net', 'bellsouth.net', 'btinternet.com', 'charter.net', 
        'comcast.net', 'cox.net', 'earthlink.net', 'email.com', 'gmx.net', 'hotmail.co.uk', 
        'mac.com', 'mail.ru', 'naver.com', 'orange.fr', 'qq.com', 'rocketmail.com', 
        'rogers.com', 'shaw.ca', 'sky.com', 'sympatico.ca', 'telus.net', 'verizon.net', 
        'web.de', 'windstream.net', 'zoominternet.net',
        'yahoo.co.uk', 'yahoo.fr', 'yahoo.de', 'yahoo.it', 'yahoo.es', 'yahoo.ca',
        'hotmail.fr', 'hotmail.it', 'hotmail.de', 'hotmail.es', 'hotmail.co.jp', 'hotmail.com.mx',
        'live.fr', 'live.co.uk', 'live.de', 'live.it', 'live.ca', 'live.com.mx',
        'outlook.fr', 'outlook.de', 'outlook.es', 'outlook.it', 'outlook.co.uk',
        'gmx.de', 'gmx.at', 'gmx.ch', 'gmx.net', 'gmx.us',
        'mail.ru', 'bk.ru', 'list.ru', 'inbox.ru'
    ];
    if (!isValidEmailDomain($email, $allowedDomains)) {
        echo "Veuillez utiliser une adresse e-mail se terminant par " . implode(', ', $allowedDomains) . ".";
        exit;
    }

    // Vérification de l'unicité de l'email en utilisant une requête préparée
    $stmt = $conn->prepare("SELECT COUNT(*) AS num_rows FROM utilisateur WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['num_rows'] > 0) {
        header("Location: ../../Import/Error/Mail/Mail_exist.php");
        exit;
    }

    // Hachage sécurisé du mot de passe
    $motDePasseHache = password_hash($motDePasse, PASSWORD_DEFAULT);

    // Génération d'un jeton unique
    $verificationToken = bin2hex(random_bytes(16)); // Génère un jeton de 32 caractères hexadécimaux

    // Récupération de la date et heure actuelle
    $dateInscription = date('Y-m-d H:i:s');

    // Requête SQL préparée pour insérer les données dans la base de données
    $sql = "INSERT INTO utilisateur (nom, prenom, email, password, pays, verification_token, date_inscription) 
            VALUES (:nom, :prenom, :email, :password, :pays, :token, :dateInscription)";
    
    // Préparation de la requête
    $stmt = $conn->prepare($sql);
    
    // Liaison des paramètres avec les valeurs
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $motDePasseHache);
    $stmt->bindParam(':pays', $pays);
    $stmt->bindParam(':token', $verificationToken);
    $stmt->bindParam(':dateInscription', $dateInscription);

    // Exécution de la requête
    try {
        $stmt->execute();

        // Création de l'e-mail de vérification via PHPMailer
        $mail = new PHPMailer(true);

        // Paramètres SMTP Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'zacrazali38@gmail.com'; // Votre adresse e-mail Gmail
        $mail->Password = 'vaxo atje vbjg kbpz'; // Votre mot de passe Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('zacrazali38@gmail.com', 'zac_Xgamer');
        $mail->addAddress($email, $prenom);
        $mail->isHTML(true);
        $mail->Subject = 'Veuillez activer votre compte';

        // Ajout du style CSS inline dans le corps de l'e-mail
        $mail->Body = "<div style='background-color: #f9f9f9; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>" .
                     "<p style='font-size: 18px; color: #333;'>Bonjour $prenom,</p>" .
                     "<p style='font-size: 16px; color: #555;'>Nous vous remercions de vous être inscrit sur notre site. Votre compte n'a pas encore été activé. Veuillez cliquer sur le lien ci-dessous pour activer votre compte :</p>" .
                     "<p style='text-align: center;'><a href='http://sysmiproject.000.pe/traitements/Formulaires/token.php?token=$verificationToken' style='background-color: #007bff; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Activer mon compte</a></p>" .
                     "<p style='font-size: 14px; color: #777; text-align: center;'>Cordialement,<br>Votre équipe (SYSMI Project)</p>" .
                     "</div>";

        // Envoi de l'e-mail
        $mail->send();

        echo "Un e-mail de vérification a été envoyé à votre adresse e-mail. Veuillez vérifier votre boîte de réception.";

        // Stockage des données de l'utilisateur dans la session
        $_SESSION['nom'] = $nom;
        $_SESSION['email'] = $email;
        $_SESSION['verificationToken'] = $verificationToken;

        // Redirection vers forms.php
        header("Location: ../../Import/Error/Mail/Mail_sended.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de l'insertion des données.";
        // Journalisation de l'erreur sans révéler de détails sensibles
        error_log("Erreur lors de l'insertion des données : " . $e->getMessage());
    }
}
?>
