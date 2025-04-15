<?php
include __DIR__ . '/../../../requiments/database.php';
session_start();

if (!isset($_GET['tag'])) {
    die("Aucun hashtag spécifié.");
}

$tag = htmlspecialchars($_GET['tag']);

// Fonction pour transformer les hashtags en liens
function convertHashtagsToLinks($text) {
    return preg_replace('/#(\w+)/', '<a href="/PAGES/hashtags.php?tag=$1" class="hashtag-link">#$1</a>', $text);
}

// Récupérer les publications contenant ce hashtag
$sql = "SELECT * FROM publications WHERE hashtags LIKE :tag ORDER BY date_creation DESC";
$stmt = $conn->prepare($sql);
$stmt->execute(['tag' => "%#$tag%"]);
$publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tag : #<?= htmlspecialchars($tag) ?></title>
    <link rel="stylesheet" href="/public/import/css/pages/index.css">
    <link rel="stylesheet" href="/public/import/css/pages/hashtag.css">
    <link rel="icon" href="/public/import/img/icon/Logo.png" type="image/png">
</head>
<body>
<div class="container">
	<div class="tag-container">
			<h1><?= htmlspecialchars($tag) ?></h1>
		<div class="tag">
    		#
		</div>
	</div>

    <div class="publication-container">
        <?php if (empty($publications)): ?>
            <p>Aucune publication trouvée pour ce hashtag.</p>
        <?php else: ?>
            <?php foreach ($publications as $publication): ?>
                <div class="publication">
                    <!-- En ajoutant l'ancre "#publication-[id]" on redirige vers l'élément correspondant dans la page publication -->
                    <a href="/publication.php#publication-<?= $publication['id'] ?>">
                        <?php
                        $mediaPath = '';
                        $extension = '';

                        if (strpos($publication['media_path'], 'images/') !== false) {
                            $mediaPath = '/Import/uploads/media/images/' . basename($publication['media_path']);
                            $extension = pathinfo($mediaPath, PATHINFO_EXTENSION);
                        } elseif (strpos($publication['media_path'], 'videos/') !== false) {
                            $mediaPath = '/Import/uploads/media/videos/' . basename($publication['media_path']);
                            $extension = pathinfo($mediaPath, PATHINFO_EXTENSION);
                        }

                        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<img src='{$mediaPath}' alt='Image' loading='lazy'>";
                        } elseif (in_array(strtolower($extension), ['mp4', 'webm', 'ogg'])) {
                            echo "<video autoplay loop muted playsinline><source src='{$mediaPath}' type='video/{$extension}'></video>";
                        }
                        ?>
                    </a>

                    <!-- Infos superposées -->
                    <div class="author-info">
                        <img src="<?= htmlspecialchars($publication['avatar']) ?: '/path/to/default-avatar.png' ?>" alt="Avatar">
                        <span class="pseudo"><?= htmlspecialchars($publication['auteur']) ?: 'Anonyme' ?></span>
                    </div>

                    <div class="hashtags"><?= convertHashtagsToLinks($publication['hashtags']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
