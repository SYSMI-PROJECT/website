<?php
// Inclusion des fichiers nécessaires
include __DIR__ . '/../../../requiments/UsersData.php';

// Démarrage de la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Vérification de la connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: /Import/Error/No_connected.php");
    exit();
}

// Récupération des informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$query = "
    SELECT utilisateur.prenom, photos_de_profil.image_content
    FROM utilisateur
    LEFT JOIN photos_de_profil ON utilisateur.id = photos_de_profil.user_id
    WHERE utilisateur.id = :user_id
";
$stmt = $conn->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur non trouvé.");
}

$prenom = $user['prenom'];
$userAvatar = !empty($user['image_content'])
    ? 'data:image/png;base64,' . base64_encode($user['image_content'])
    : 'https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg';

// Génération d'un token CSRF pour sécuriser le formulaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Traitement du formulaire de publication
if (
    isset($_POST['submit_publication']) &&
    isset($_POST['csrf_token']) &&
    hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $contenu  = $_POST['contenu'];
    $hashtags = isset($_POST['hashtags']) ? $_POST['hashtags'] : '';
    $mediaPath = null;

    // Déterminer le mode de média (record pour webcam ou upload pour fichier)
    $media_mode = $_POST['media_mode'] ?? 'record';
    $via_webcam = ($media_mode === 'record') ? 1 : 0;

    // Enregistrement via webcam (photo ou vidéo)
    if ($media_mode === 'record' && !empty($_POST['recorded_media'])) {
        $data = $_POST['recorded_media'];
        if (strpos($data, 'base64,') !== false) {
            list($prefix, $data) = explode('base64,', $data);
        } else {
            $prefix = '';
        }
        $decodedData = base64_decode($data);
        preg_match('/data:(.*?);/', $prefix, $matches);
        $mime = $matches[1] ?? 'video/webm';
        // Choix de l'extension selon le type mime (vidéo ou image)
        $ext = ($mime === 'video/webm' || $mime === 'video/mp4') ? 'webm' : 'png';

        $mediaFolder = '../../Import/uploads/media/';
        if (strpos($mime, 'image') !== false || $ext === 'png') {
            $mediaFolder .= 'images/';
        } elseif (strpos($mime, 'video') !== false) {
            $mediaFolder .= 'videos/';
        } else {
            $mediaFolder .= 'autres/';
        }
        if (!file_exists($mediaFolder)) { mkdir($mediaFolder, 0777, true); }
        $filename = 'recorded_' . time() . '_' . $user_id . '.' . $ext;
        $mediaPath = $mediaFolder . $filename;
        file_put_contents($mediaPath, $decodedData);
    // Upload classique
    } elseif (
        $media_mode === 'upload' &&
        isset($_FILES['media']) &&
        $_FILES['media']['error'] == 0 &&
        !empty($_FILES['media']['name'])
    ) {
        $mediaFile = $_FILES['media'];
        $mediaType = mime_content_type($mediaFile['tmp_name']);

        if (strpos($mediaType, 'image') !== false) {
            $mediaFolder = '../../Import/uploads/media/images/';
        } elseif (strpos($mediaType, 'video') !== false) {
            $mediaFolder = '../../Import/uploads/media/videos/';
        } elseif (strpos($mediaType, 'gif') !== false) {
            $mediaFolder = '../../Import/uploads/media/gifs/';
        } else {
            $mediaFolder = '../../Import/uploads/media/autres/';
        }
        if (!file_exists($mediaFolder)) { mkdir($mediaFolder, 0777, true); }
        // Nettoyage du nom de fichier pour plus de sécurité
        $cleanFilename = preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($mediaFile['name']));
        $filename = time() . '_' . $cleanFilename;
        $mediaPath = $mediaFolder . $filename;
        move_uploaded_file($mediaFile['tmp_name'], $mediaPath);
    }

    // Conversion du chemin pour la base de données
    if ($mediaPath) {
        $mediaPath = str_replace('../../Import', '/Import', $mediaPath);
    }

    // Insertion dans la base de données incluant le champ via_webcam
    $query = "INSERT INTO publications (auteur, contenu, avatar, media_path, user_id, hashtags, via_webcam)
              VALUES (:auteur, :contenu, :avatar, :media_path, :user_id, :hashtags, :via_webcam)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'auteur'     => $prenom,
        'contenu'    => htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
        'avatar'     => $userAvatar,
        'media_path' => $mediaPath,
        'user_id'    => $user_id,
        'hashtags'   => htmlspecialchars($hashtags, ENT_QUOTES, 'UTF-8'),
        'via_webcam' => $via_webcam
    ]);
    header("Location: /publication.php");
    exit();
}

// Récupération des publications (pour d'éventuelles utilisations ultérieures)
function getPublications($conn) {
    $query = "SELECT * FROM publications ORDER BY id DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$publications = getPublications($conn);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Création de Contenu - Enregistrement avec Effets</title>
  <!-- Import Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="/public/import/css/pages/new_post.css">
</head>
<body>
  <div class="container">
    <!-- Flux de la caméra -->
    <video id="video" autoplay playsinline aria-label="Flux vidéo de la caméra"></video>

    <!-- Barre du Haut -->
    <div class="top-bar">
      <div>
        <button id="mirrorBtn" title="Effet miroir On/Off" aria-label="Effet miroir">
          <i class="fas fa-arrows-alt-h"></i>
        </button>
        <button id="flipBtn" title="Changer de caméra" aria-label="Changer de caméra">
          <i class="fas fa-sync-alt"></i>
        </button>
      </div>
      <button id="backBtn" title="Quitter" aria-label="Quitter">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Barre d'options -->
    <div class="option-bar">
      <button id="uploadOption">Téléverser</button>
    </div>

    <!-- Menu des modes -->
    <div class="mode-menu">
      <button class="mode-btn active" data-mode="video" title="Vidéo" aria-label="Mode Vidéo">
        <i class="fas fa-video"></i>
      </button>
      <button class="mode-btn" data-mode="photo" title="Photo" aria-label="Mode Photo">
        <i class="fas fa-camera"></i>
      </button>
      <button class="mode-btn" data-mode="slowmo" title="Slow Motion" aria-label="Mode Slow Motion">
        <i class="fas fa-hourglass-half"></i>
      </button>
      <button class="mode-btn" data-mode="timelapse" title="Timelapse" aria-label="Mode Timelapse">
        <i class="fas fa-stopwatch"></i>
      </button>
      <button class="mode-btn filter-toggle" data-mode="filters" title="Filtres" aria-label="Ouvrir les filtres">
        <i class="fas fa-sliders-h"></i>
      </button>
      <button class="mode-btn" data-mode="effects" title="Effets" aria-label="Ouvrir les effets">
        <i class="fas fa-magic"></i>
      </button>
    </div>

    <!-- Panneau des Filtres -->
    <div class="filter-panel" id="filterPanel" aria-label="Panneau des filtres">
      <header>
        <h2>Filtres</h2>
        <button id="closeFilterPanel" aria-label="Fermer les filtres">&times;</button>
      </header>
      <div class="filter-buttons">
        <button class="filter-btn active" data-filter="none" aria-label="Filtre None">None</button>
        <button class="filter-btn" data-filter="grayscale(100%)" aria-label="Filtre Grayscale">Grayscale</button>
        <button class="filter-btn" data-filter="sepia(100%)" aria-label="Filtre Sepia">Sepia</button>
        <button class="filter-btn" data-filter="invert(100%)" aria-label="Filtre Invert">Invert</button>
        <button class="filter-btn" data-filter="contrast(150%)" aria-label="Filtre Contrast">Contrast</button>
        <button class="filter-btn" data-filter="custom" aria-label="Filtre Custom">Custom</button>
      </div>
      <div class="custom-filter" id="customFilter">
        <label for="brightnessRange">Brightness</label>
        <input type="range" id="brightnessRange" min="50" max="150" value="100">
      </div>
    </div>

    <!-- Panneau des Effets -->
    <div class="effects-panel" id="effectsPanel" aria-label="Panneau des effets">
      <header>
        <h2>Effets</h2>
        <button id="closeEffectsPanel" aria-label="Fermer les effets">&times;</button>
      </header>
      <div class="effect-controls">
        <div class="effect-control">
          <label for="effBrightness">Brightness</label>
          <input type="range" id="effBrightness" min="50" max="150" value="100">
        </div>
        <div class="effect-control">
          <label for="effContrast">Contrast</label>
          <input type="range" id="effContrast" min="50" max="150" value="100">
        </div>
        <div class="effect-control">
          <label for="effSaturation">Saturation</label>
          <input type="range" id="effSaturation" min="50" max="150" value="100">
        </div>
      </div>
    </div>

    <!-- Indicateurs d'enregistrement -->
    <div class="recording-indicator" id="recordingIndicator">Enregistrement...</div>
    <div class="recording-timer" id="recordingTimer">00:00</div>

    <!-- Bouton d'enregistrement -->
    <div class="record-btn" id="recordBtn">
      <div class="record">
        <div class="record-inner"><i class="fas fa-circle"></i></div>
      </div>
    </div>

    <!-- Bouton Pause/Reprise -->
    <button id="pauseBtn" class="pause-btn" aria-label="Pause/Reprise">
      <i class="fas fa-pause"></i>
    </button>
  </div>

  <!-- Modal de prévisualisation type iPhone avec formulaire -->
  <div id="iphonePreviewContainer" class="iphone-preview-container" aria-label="Prévisualisation iPhone">
    <button class="close-iphone" id="closeIphonePreview">&times;</button>
    <div class="iphone-frame">
      <div class="header">Prévisualisation</div>
      <div class="content" id="iphoneContent">
        <!-- La prévisualisation et le formulaire seront injectés ici -->
        <div id="iphonePreviewMedia"></div>
        <!-- Formulaire de publication -->
        <form id="publicationForm" method="post" enctype="multipart/form-data">
          <textarea name="contenu" id="contenu" placeholder="Ajouter une description..." required></textarea>
          <input type="text" name="hashtags" id="hashtags" placeholder="Hashtags (optionnel)">
          <input type="hidden" name="media_mode" id="media_mode" value="">
          <input type="hidden" name="recorded_media" id="recorded_media" value="">
          <input type="file" name="media" id="media" accept="image/*,video/*">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
          <button type="submit" name="submit_publication">Publier</button>
        </form>
      </div>
    </div>
  </div>

  <script src="/public/import/js/new_post.js"></script>

</body>
</html>