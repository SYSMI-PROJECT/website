<?php
// Connexion à la base de données
include __DIR__ . '/../../../requiments/database.php';
try {
    // Charger tous les témoignages sans pagination
    $stmt = $conn->prepare("
        SELECT u.prenom, u.nom, p.image_content, f.feedback, f.rating
        FROM feedback f
        JOIN utilisateur u ON f.user_id = u.id
        LEFT JOIN photos_de_profil p ON u.id = p.user_id
        ORDER BY f.id DESC
    ");
    $stmt->execute();
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Vérifier s'il y a des témoignages à afficher
    if ($feedbacks) {
        foreach ($feedbacks as $feedback) {
            $rating = str_repeat("⭐", $feedback['rating']); // Affichage des étoiles en fonction de la note
            $profilePicture = $feedback['image_content']; // Récupérer le contenu de l'image
            $firstName = htmlspecialchars($feedback['prenom']); // Sécuriser le prénom
            $lastName = htmlspecialchars($feedback['nom']); // Sécuriser le nom

            // Affichage du témoignage
            echo "<div class='testimonial'>";

            // Afficher le prénom, le nom et la photo de profil côte à côte
            echo "<div class='testimonial-header'>";
            // Afficher la photo de profil
            if ($profilePicture) {
                echo "<img src='data:image/jpeg;base64," . base64_encode($profilePicture) . "' alt='Photo de $firstName $lastName' class='feedback-profile'>";
            } else {
                echo "<img src='https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg' alt='Photo par défaut de $firstName $lastName' class='feedback-profile'>";
            }
            // Afficher le prénom et le nom
            echo "<p><strong>" . $firstName . " " . $lastName . " :</strong></p>";
            echo "</div>";

            // Affichage du témoignage et de la note
            echo "<p>" . htmlspecialchars($feedback['feedback']) . "</p>"; // Afficher le témoignage
            echo "<p class='rating'>" . $rating . "</p>"; // Afficher les étoiles de la note

            echo "</div>";
        }
    } else {
        echo "<p>Aucun témoignage trouvé.</p>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>