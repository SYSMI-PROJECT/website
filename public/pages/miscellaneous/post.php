<?php
include __DIR__ . '/../../../requiments/post.php'; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SYSMI PROJECT</title>
  <link rel="icon" type="image/png" href="/Import/icons/Logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="manifest" href="/Import/assets/manifest.json">
  <link rel="stylesheet" href="<?= htmlspecialchars($cssFile ?? '/public/import/css/pages/index.css') ?>">
  <link rel="stylesheet" href="/public/import/css/pages/post.css">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
</head>
<body>
  <nav>
    <div class="logo">
      <img src="/public/img/icon/Logo.png" alt="Logo de Mon Réseau" class="logo-img">
      <span class="logo-text">the SYSMI</span>
    </div>
    
    <!-- Bouton pour afficher les publications -->
    <a href="#" class="menu-item" id="home-link" onclick="refreshPosts();">
      <i id="home-icon" class="fas fa-home"></i> 
      <span>VOS POST</span>
    </a>
    
    <!-- Liens pour le contenu dynamique -->
    <a href="/PAGES/user/MessageList.php" class="menu-item" id="messages-link">
      <i class="fas fa-envelope"></i>
      <span>NOTIFIER</span>
      <?php if (isset($nbMessagesNonLus) && $nbMessagesNonLus > 0): ?>
        <div class="badge"><?= $nbMessagesNonLus ?></div>
      <?php endif; ?>
    </a>
    <a href="/PAGES/user/test.php" class="menu-item" id="publier-link">
      <i class="fas fa-plus-circle"></i>
      <span>PUBLIER</span>
    </a>
    <a href="/index.php" class="menu-item" id="compte-link">
      <i class="fas fa-user"></i>
      <span>COMPTE</span>
    </a>
  </nav>
	
	<div id="contenu"></div>
  
  <!-- Conteneur principal avec les publications -->
  <div class="scroll-container">
    <main class="main">
      <?php if (empty($publications)): ?>
        <div id="posts-container" class="publication-container" style="margin: 19rem auto; background-color: aliceblue;">
          <?php 
            if ($filterUserId && $filterUserId != $user_id) {
              echo "<p style='background-color: aliceblue; color: black; font-weight: 600; text-align: center; padding: 20px;'>
                      <span style='display: inline-block; width: 60px; height: 60px; background-color: #000; color: white; 
                           border-radius: 50%; display: flex; align-items: center; justify-content: center; 
                           margin: 0 auto 10px; font-size: 2rem;'>
                        <i class='fas fa-lock'></i>
                      </span>
                      Ce compte est privé.
                    </p>";
            } else {
              echo "<div style='text-align: center; padding: 15px; border-radius: 10px;'>
                      <h2 style='color: #ff6600;'>📢 Deviens la star du moment ! 🎥</h2>
                      <p style='font-size: 16px; color: #001eff; font-weight: 600;'>Ajoute ta vidéo et fais vibrer la communauté !</p>
                      <button onclick='uploadMedia()' style='background: #ff6600; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>
                        🚀 Partage ta vidéo maintenant !
                      </button>
                    </div>";
            }
          ?>
        </div>
      <?php else: ?>
        <div id="posts-container" class="publication-container">
          <?php foreach ($publications as $publication): 
            // Récupération du nombre de commentaires pour la publication
            $stmt = $conn->prepare("SELECT COUNT(*) FROM comments WHERE publication_id = :publication_id");
            $stmt->execute(['publication_id' => $publication['id']]);
            $commentCount = $stmt->fetchColumn();
            // Vérification si la publication a été réalisée via webcam
            $viaWebcam = (!empty($publication['via_webcam']) && $publication['via_webcam'] == 1);
          ?>
            <div class="publication-wrapper">
              <article class="publication" id="publication-<?= $publication['id'] ?>">
                <?php if (!empty($publication['media_path'])): ?>
                  <div class="media">
                    <div class="media-wrapper" id="media-wrapper-<?= $publication['id'] ?>">
                      <?php
                        $mediaPath = htmlspecialchars($publication['media_path']);
                        $extension = strtolower(pathinfo($mediaPath, PATHINFO_EXTENSION));
                        // Pour les images
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])):
                      ?>
                        <img src="<?= $mediaPath ?>" alt="Image" loading="lazy" <?= $viaWebcam ? 'style="object-fit: cover;"' : '' ?>>
                        <div class="image-overlay">
                          <div class="follow-container">
                            <h3><?= htmlspecialchars($publication['auteur']) ?></h3>
                            <a href="#" class="follow-btn" onclick="toggleFollow(<?= $publication['user_id'] ?>, event)">Suivre</a>
                          </div>
                          <div class="desc">
                            <p class="contenu truncated">
                              <?= nl2br(convertHashtagsToLinks($publication['contenu'])) ?>
                              <?php if (!empty($publication['hashtags'])): ?>
                                <span class="hashtags-links">
                                  <?= nl2br(convertHashtagsToLinks($publication['hashtags'])) ?>
                                </span>
                              <?php endif; ?>
                            </p>
                            <button type="button" class="show-more">Afficher plus</button>
                          </div>
                          <small class="timestamp">Publié le : <?= date('d-m-Y H:i', strtotime($publication['date_creation'])) ?></small>
                        </div>
                      <?php 
                        // Pour les vidéos
                        elseif (in_array($extension, ['mp4', 'webm', 'ogg'])): 
                      ?>
                        <div class="video-container" onclick="togglePlayPause(<?= $publication['id'] ?>)">
                          <video id="video-<?= $publication['id'] ?>" class="media" preload="metadata" playsinline loop muted <?= $viaWebcam ? 'style="object-fit: cover;"' : '' ?>>
                            <source src="<?= $mediaPath ?>" type="video/<?= $extension ?>">
                            Votre navigateur ne supporte pas cette vidéo.
                          </video>
                          <button type="button" class="play-pause-button" id="play-pause-<?= $publication['id'] ?>"
                                  onclick="togglePlayPause(<?= $publication['id'] ?>); event.stopPropagation();">
                            <i class="fas fa-play"></i>
                          </button>
                          <div class="video-overlay" onclick="event.stopPropagation();">
                            <div class="follow-container" onclick="event.stopPropagation();">
                              <h3><?= htmlspecialchars($publication['auteur']) ?></h3>
                              <a href="#" class="follow-btn" onclick="toggleFollow(<?= $publication['user_id'] ?>, event)">Suivre</a>
                            </div>
                            <div class="desc" onclick="event.stopPropagation();">
                              <p class="contenu truncated">
                                <?= nl2br(convertHashtagsToLinks($publication['contenu'])) ?>
                                <?php if (!empty($publication['hashtags'])): ?>
                                  <span class="hashtags-links">
                                    <?= nl2br(convertHashtagsToLinks($publication['hashtags'])) ?>
                                  </span>
                                <?php endif; ?>
                              </p>
                              <button type="button" class="show-more">Afficher plus</button>
                            </div>
                            <small class="timestamp">Publié le : <?= date('d-m-Y H:i', strtotime($publication['date_creation'])) ?></small>
                          </div>
                          <div class="progress-container" data-publication-id="<?= $publication['id'] ?>">
                            <div class="buffered-progress" id="buffered-<?= $publication['id'] ?>"></div>
                            <div class="progress" id="progress-<?= $publication['id'] ?>"></div>
                            <div class="progress-handle" id="handle-<?= $publication['id'] ?>"></div>
                            <div class="progress-tooltip" id="tooltip-<?= $publication['id'] ?>">00:00 / 00:00</div>
                          </div>
                        </div>
                      <?php 
                        // Pour les fichiers audio
                        elseif (in_array($extension, ['mp3', 'wav', 'ogg'])): 
                      ?>
                        <audio controls>
                          <source src="<?= $mediaPath ?>" type="audio/<?= $extension ?>">
                          Votre navigateur ne supporte pas cet audio.
                        </audio>
                      <?php else: ?>
                        <p>Type de média non pris en charge.</p>
                      <?php endif; ?>
    
                      <div class="media-controls">
                        <button type="button" class="menu-button">
                          <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu">
                          <button type="button" class="fullscreen-button" onclick="toggleFullscreen(<?= $publication['id'] ?>, event)">
                            <i class="fas fa-expand"></i>
                          </button>
                          <button type="button" class="download-button" onclick="downloadMedia('<?= $mediaPath ?>', event)">
                            <i class="fas fa-download"></i>
                          </button>
                          <button type="button" class="share-media-button" onclick="shareMedia('<?= $mediaPath ?>', event)">
                            <i class="fas fa-share-alt"></i>
                          </button>
                          <button type="button" class="rewind-button" onclick="rewindVideo(<?= $publication['id'] ?>, event)">
                            <i class="fas fa-backward"></i>
                          </button>
                          <button type="button" class="forward-button" onclick="forwardVideo(<?= $publication['id'] ?>, event)">
                            <i class="fas fa-forward"></i>
                          </button>
                          <button type="button" class="pip-button" onclick="togglePictureInPicture(<?= $publication['id'] ?>, event)">
                            <i class="fas fa-tv"></i>
                          </button>
                          <button type="button" class="bookmark-button" onclick="toggleBookmark(<?= $publication['id'] ?>, event)">
                            <i class="fa-regular fa-bookmark"></i>
                          </button>
                          <button type="button" class="report-button" onclick="reportPost(<?= $publication['id'] ?>, event)">
                            <i class="fas fa-flag"></i>
                          </button>
                          <button type="button" class="copy-link-button" onclick="copyPostLink(<?= $publication['id'] ?>, event)">
                            <i class="fas fa-link"></i>
                          </button>
                          <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $publication['user_id']): ?>
                            <button type="button" class="edit-post-button" onclick="editPost(<?= $publication['id'] ?>, event)">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="delete-post-button" onclick="deletePost(<?= $publication['id'] ?>, event)">
                              <i class="fas fa-trash"></i>
                            </button>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
              </article>
              <div class="interactions" data-publication-id="<?= $publication['id'] ?>">
                <div class="avatar-container">
                  <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/PAGES/user/home.php?id=<?= htmlspecialchars($publication['user_id']) ?>">
                      <img src="<?= !empty($publication['avatar']) ? htmlspecialchars($publication['avatar']) : 'https://via.placeholder.com/40' ?>" alt="Avatar" loading="lazy">
                    </a>
                  <?php else: ?>
                    <a href="/public/forms/login.html" style="display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; background: #ff0000; border-radius: 50%;">
                      <i class="fas fa-sign-in" style="font-size: 20px; color: white;"></i>
                    </a>
                  <?php endif; ?>
                </div>
                <button type="button" class="mute-button" data-publication-id="<?= $publication['id'] ?>">
                  <i class="fas fa-volume-low"></i>
                </button>
                <?php 
                  $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE publication_id = ? AND user_id = ?");
                  $stmt->execute([$publication['id'], $user_id]);
                  $userLiked = $stmt->fetchColumn() > 0;
                  $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE publication_id = ?");
                  $stmt->execute([$publication['id']]);
                  $likesCount = $stmt->fetchColumn() ?: 0;
                ?>
                <div class="like-container">
                  <button type="button" class="like-button <?= $userLiked ? 'liked' : '' ?>"
                          data-publication-id="<?= $publication['id'] ?>"
                          data-action="<?= $userLiked ? 'unlike' : 'like' ?>">
                    <i class="fas fa-heart"></i>
                  </button>
                  <div class="like-count"><?= $likesCount ?></div>
                </div>
                <div class="speed-container">
                  <button type="button" class="speed-button" onclick="cyclePlaybackSpeed(<?= $publication['id'] ?>, event)">
                    <i class="fas fa-tachometer-alt"></i>
                  </button>
                  <div class="comment-count" id="speed-display-<?= $publication['id'] ?>">1x</div>
                </div>
                <div class="comment-container">
                  <button type="button" class="comment-button" data-publication-id="<?= $publication['id'] ?>">
                    <i class="fas fa-comment"></i>
                  </button>
                  <div class="comment-count"><?= $commentCount ?></div>
                </div>
              </div>
            </div>
            <div class="comment-modal" id="comment-modal-<?= $publication['id'] ?>">
              <header>
                <h3><span id="comment-count-<?= $publication['id'] ?>"><?= $commentCount ?></span> Commentaires</h3>
                <button type="button" class="close-modal" data-publication-id="<?= $publication['id'] ?>">&times;</button>
              </header>
              <div class="comments-body" id="comments-body-<?= $publication['id'] ?>">
                <div id="comment-list-<?= $publication['id'] ?>">
                  <?php
                    $stmt = $conn->prepare("SELECT * FROM comments WHERE publication_id = :publication_id ORDER BY id DESC");
                    $stmt->execute(['publication_id' => $publication['id']]);
                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (count($comments) > 0):
                      foreach ($comments as $comment):
                  ?>
                    <div class="comment">
                      <div class="comment-avatar">
                        <img style="width: 40px; height: 40px;" src="<?= htmlspecialchars($commentAvatar ?? 'https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg') ?>" alt="Avatar du commentateur">
                      </div>
                      <div class="comment-text">
                        <div><strong><?= htmlspecialchars($comment['username']) ?></strong> :</div>
                        <div><?= nl2br(htmlspecialchars($comment['contenu'])) ?></div>
                      </div>
                      <div class="comment-options">
                        <button class="menu-button"><i class="fas fa-ellipsis-v"></i></button>
                        <div class="dropdown-menu">
                          <button onclick="replyComment(<?= $comment['id'] ?>, event)"><i class="fas fa-reply"></i> Répondre</button>
                          <button onclick="editComment(<?= $comment['id'] ?>, event)"><i class="fas fa-edit"></i> Éditer</button>
                          <button onclick="copyComment(<?= $comment['id'] ?>, event)"><i class="fas fa-copy"></i> Copier</button>
                          <button onclick="reportComment(<?= $comment['id'] ?>, event)"><i class="fas fa-exclamation-circle"></i> Signaler</button>
                          <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                            <button onclick="deleteComment(<?= $comment['id'] ?>, event)"><i class="fas fa-trash"></i> Supprimer</button>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php
                      endforeach;
                    else:
                      echo "<p style='color: black; text-align: center;' id='no-comment-message-{$publication['id']}'>Aucun commentaire pour l'instant.</p>";
                    endif;
                  ?>
                </div>
              </div>
              <div class="comment-input-container">
                <form method="POST" class="ajax-comment-form" style="width:100%; display: flex; gap: 5px; padding: 10px;">
                  <input type="text" name="comment" placeholder="Ajouter un commentaire" required>
                  <button type="submit" name="add_comment">Envoyer</button>
                  <input type="hidden" name="publication_id" value="<?= $publication['id'] ?>">
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>
  
  <div class="scroll-buttons-container">
    <button id="scroll-up" class="scroll-button"><i class="fas fa-arrow-up"></i></button>
    <button id="scroll-down" class="scroll-button"><i class="fas fa-arrow-down"></i></button>
  </div>
  
    <script src="/public/import/js/post.js"></script>
  </body>
</html>
