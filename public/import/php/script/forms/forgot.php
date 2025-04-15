<?php
session_start();
include __DIR__ . '/../../../../../requiments/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST["password"]) && !empty($_POST["password"]) 
        && isset($_POST["confirm_password"]) && !empty($_POST["confirm_password"])
        && isset($_POST["email"]) && !empty($_POST["email"])) {
        
        $password = $_POST["password"]; 
        $confirm_password = $_POST["confirm_password"]; 
        $email = $_POST["email"]; 

        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt = $conn->prepare("UPDATE utilisateur SET password = :password WHERE email = :email");

                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':email', $email);

                $stmt->execute();

                header("Location: ../../formulaires/login.html");
                exit();
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
