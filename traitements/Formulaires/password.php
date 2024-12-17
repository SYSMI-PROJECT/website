<?php
session_start();
require '../../request/DB.php'; // Incluez le fichier de connexion à la base de données

// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Vérifiez si les champs de mot de passe, de confirmation et d'email sont définis et non vides
    if (isset($_POST["password"]) && !empty($_POST["password"]) 
        && isset($_POST["confirm_password"]) && !empty($_POST["confirm_password"])
        && isset($_POST["email"]) && !empty($_POST["email"])) {
        
        // Récupérez les mots de passe et l'email depuis le formulaire
        $password = $_POST["password"]; // mot de passe en clair
        $confirm_password = $_POST["confirm_password"]; // confirmation du mot de passe
        $email = $_POST["email"]; // adresse e-mail de l'utilisateur

        // Vérifiez si les mots de passe correspondent
        if ($password === $confirm_password) {
            // Hachez le mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                // Préparez la requête SQL pour mettre à jour le mot de passe
                $stmt = $conn->prepare("UPDATE utilisateur SET password = :password WHERE email = :email");

                // Liez les paramètres
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':email', $email);

                // Exécutez la requête
                $stmt->execute();

                // Redirigez vers la page index.php ou une autre page
                header("Location: ../../formulaires/login.html");
                exit(); // Assurez-vous de terminer le script après la redirection
            } catch(PDOException $e) {
                echo "Erreur : " . $e->getMessage();
            }
        } else {
            echo "Les mots de passe ne correspondent pas.";
        }
    } else {
        echo "Les champs de mot de passe, de confirmation et d'email ne doivent pas être vides.";
    }
} else {
    echo "Le formulaire n'a pas été soumis.";
}
?>
