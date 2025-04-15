<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../../../../public/import/vendor/autoload.php';
require __DIR__ . '/../../../../../requiments/database.php';

// Charger les variables d'environnement manuellement
$dotenvPath = __DIR__ . '/../../../../../.env';
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        putenv($line);
    }
}

// Fonctions utiles
function containsEmail($str) {
    return strpos($str, '@') !== false;
}

function isValidEmailDomain($email, $allowedDomains) {
    $emailDomain = explode('@', $email)[1] ?? '';
    return in_array($emailDomain, $allowedDomains);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validation de base
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $motDePasse = $_POST['motDePasse'];
    $pays = trim($_POST['pays']);
    $dateNaissance = $_POST['date_naissance'];

    if (!$nom || !$prenom || !$email || !$motDePasse || !$pays || !$dateNaissance) {
        echo "Veuillez remplir tous les champs du formulaire.";
        exit;
    }

    if (containsEmail($prenom) || containsEmail($nom)) {
        echo "Le prénom et le nom ne peuvent pas contenir d'adresse email.";
        exit;
    }

    if (strlen($motDePasse) < 8) {
        echo "Le mot de passe doit contenir au moins 8 caractères.";
        exit;
    }

    $allowedDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com', 'protonmail.com'];
    if (!isValidEmailDomain($email, $allowedDomains)) {
        echo "Veuillez utiliser une adresse email valide.";
        exit;
    }

    // 🔧 Correction ici : séparer création de l'objet pour éviter l'erreur
    $dateNaissanceStr = $dateNaissance;
    $dateNaissanceObj = DateTime::createFromFormat('d/m/Y', $dateNaissanceStr);
    if (!$dateNaissanceObj) {
        echo "Date de naissance invalide (format attendu : JJ/MM/AAAA).";
        exit;
    }

    $age = (new DateTime())->diff($dateNaissanceObj)->y;
    if ($age < 13) {
        echo "Vous devez avoir au moins 13 ans pour vous inscrire.";
        exit;
    }

    // Vérification si email déjà utilisé
    $stmt = $conn->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        header("Location: ../../Import/Error/Mail/Mail_exist.php");
        exit;
    }

    // Enregistrement en base de données
    $motDePasseHache = password_hash($motDePasse, PASSWORD_DEFAULT);
    $verificationToken = bin2hex(random_bytes(16));
    $dateInscription = date('Y-m-d H:i:s');

    $sql = "INSERT INTO utilisateur (nom, prenom, email, password, pays, date_naissance, verification_token, date_inscription) 
            VALUES (:nom, :prenom, :email, :password, :pays, :dateNaissance, :token, :dateInscription)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':prenom', $prenom);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':password', $motDePasseHache);
    $stmt->bindValue(':pays', $pays);
    $stmt->bindValue(':dateNaissance', $dateNaissanceObj->format('Y-m-d'));
    $stmt->bindValue(':token', $verificationToken);
    $stmt->bindValue(':dateInscription', $dateInscription);

    try {
        $stmt->execute();

        // 📧 Envoi de l'email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USERNAME');
        $mail->Password   = getenv('SMTP_PASSWORD');
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom(getenv('SMTP_USERNAME'), 'SYSMI Project');
        $mail->addAddress($email, $prenom);
        $mail->addReplyTo(getenv('REPLY_TO'), 'Support SYSMI');

        $mail->isHTML(true);
        $mail->Subject = 'Veuillez activer votre compte';

        $lien = "https://sysmiproject.mercurehosting.com/traitements/Formulaires/token.php?token=$verificationToken";
        $mail->Body = "
            <div style='background-color: #f9f9f9; padding: 20px; border-radius: 10px; font-family: Arial;'>
                <p style='font-size: 18px;'>Bonjour $prenom,</p>
                <p>Merci de vous être inscrit. Cliquez sur le bouton ci-dessous pour activer votre compte :</p>
                <div style='text-align: center; margin: 20px 0;'>
                    <a href='$lien' style='background: #007bff; padding: 10px 20px; color: #fff; text-decoration: none; border-radius: 5px;'>Activer mon compte</a>
                </div>
                <p>Si vous n’avez pas demandé cette inscription, vous pouvez ignorer ce message.</p>
                <p style='font-size: 14px; color: #666;'>– L'équipe SYSMI Project</p>
            </div>";
        $mail->AltBody = "Bonjour $prenom,\n\nCliquez ici pour activer votre compte : $lien";

        $mail->send();

        $_SESSION['nom'] = $nom;
        $_SESSION['email'] = $email;
        $_SESSION['verificationToken'] = $verificationToken;

        header("Location: /public/import/Error/Mail/Mail_sended.php");
        exit;

    } catch (Exception $e) {
        echo "Le message n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}";
    }
}
?>
