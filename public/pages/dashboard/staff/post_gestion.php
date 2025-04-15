<?php
// post_gestion.php

// Connexion à la base de données
include '../../../../requiments/database.php';

/**
 * Récupère les médias en fonction des filtres et de la pagination.
 *
 * @param PDO $pdo
 * @param int $limit
 * @param int $offset
 * @param string|null $mediaType
 * @param string|null $author
 * @return array
 */
function getMediaByFilters($pdo, $limit = 20, $offset = 0, $mediaType = null, $author = null) {
    $query = "SELECT id, media_path, auteur, date_creation FROM publications WHERE media_path IS NOT NULL";

    if ($mediaType) {
        if ($mediaType == 'image') {
            $query .= " AND (media_path LIKE '%.jpg' OR media_path LIKE '%.png' OR media_path LIKE '%.gif')";
        } elseif ($mediaType == 'video') {
            $query .= " AND (media_path LIKE '%.mp4' OR media_path LIKE '%.avi')";
        } elseif ($mediaType == 'audio') {
            $query .= " AND media_path LIKE '%.mp3'";
        }
    }

    if ($author) {
        $query .= " AND auteur LIKE :author";
    }

    $query .= " ORDER BY date_creation DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($query);
    if ($author) {
        // Correction : s'assurer que $author est une chaîne (évite de passer null)
        $stmt->bindValue(':author', $author, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère le nombre total de médias pour la pagination.
 *
 * @param PDO $pdo
 * @param string|null $mediaType
 * @param string|null $author
 * @return int
 */
function getTotalMediaCount($pdo, $mediaType = null, $author = null) {
    $queryCount = "SELECT COUNT(*) FROM publications WHERE media_path IS NOT NULL";
    if ($mediaType) {
        if ($mediaType == 'image') {
            $queryCount .= " AND (media_path LIKE '%.jpg' OR media_path LIKE '%.png' OR media_path LIKE '%.gif')";
        } elseif ($mediaType == 'video') {
            $queryCount .= " AND (media_path LIKE '%.mp4' OR media_path LIKE '%.avi')";
        } elseif ($mediaType == 'audio') {
            $queryCount .= " AND media_path LIKE '%.mp3'";
        }
    }
    if ($author) {
        $queryCount .= " AND auteur LIKE :author";
    }

    $stmt = $pdo->prepare($queryCount);
    if ($author) {
        $stmt->bindValue(':author', $author, PDO::PARAM_STR);
    }
    $stmt->execute();
    return (int) $stmt->fetchColumn();
}

// Traitement des filtres en s'assurant que les variables soient des chaînes (pour éviter qu'htmlspecialchars reçoive null)
$mediaType = isset($_GET['media_type']) ? $_GET['media_type'] : '';
$author = isset($_GET['author']) ? $_GET['author'] : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$mediaPaths = getMediaByFilters($conn, $limit, $offset, $mediaType, $author);
$totalMedia = getTotalMediaCount($conn, $mediaType, $author);
$totalPages = ceil($totalMedia / $limit);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie des Médias</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/public/import/css/dashboard/post_gestion.css">
</head>
<body>
    <!-- Header hero avec bouton Retour -->
    <header class="hero">
        <h1>Galerie des Médias</h1>
    </header>
    
    <div class="wrapper">
        <div class="container-custom">
        <a href="/index.php" class="btn-home">Accueil</a>
            <!-- Formulaire des filtres -->
            <form id="filterForm" action="" method="GET" class="row g-3 filter-form">
                <div class="col-md-4">
                    <select name="media_type" class="form-select" aria-label="Filtrer par type">
                        <option value="">Filtrer par type</option>
                        <option value="image" <?php echo ($mediaType === 'image') ? 'selected' : ''; ?>>Image</option>
                        <option value="video" <?php echo ($mediaType === 'video') ? 'selected' : ''; ?>>Vidéo</option>
                        <option value="audio" <?php echo ($mediaType === 'audio') ? 'selected' : ''; ?>>Audio</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <!-- Correction via l'opérateur null coalescent afin d'éviter htmlspecialchars(null) -->
                    <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($author ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Recherche par auteur">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
            
            <!-- Liste des médias -->
            <div id="mediaList">
                <?php if (empty($mediaPaths)): ?>
                    <div class="alert alert-warning">Aucun média disponible.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Auteur</th>
                                    <th>Média</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mediaPaths as $media): ?>
                                    <tr id="media-<?php echo $media['id']; ?>">
                                        <td><?php echo $media['id']; ?></td>
                                        <td><?php echo htmlspecialchars($media['auteur'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <div class="media-preview" data-bs-toggle="modal" data-bs-target="#mediaModal" data-media-path="<?php echo $media['media_path']; ?>">
                                                <?php if (strpos($media['media_path'], '.jpg') !== false || strpos($media['media_path'], '.png') !== false || strpos($media['media_path'], '.gif') !== false): ?>
                                                    <img src="<?php echo $media['media_path']; ?>" alt="Image">
                                                <?php elseif (strpos($media['media_path'], '.mp4') !== false || strpos($media['media_path'], '.avi') !== false): ?>
                                                    <video controls>
                                                        <source src="<?php echo $media['media_path']; ?>" type="video/mp4">
                                                    </video>
                                                <?php elseif (strpos($media['media_path'], '.mp3') !== false): ?>
                                                    <audio controls>
                                                        <source src="<?php echo $media['media_path']; ?>" type="audio/mp3">
                                                    </audio>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?php echo date('d-m-Y H:i', strtotime($media['date_creation'])); ?></td>
                                        <td class="action-buttons">
                                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $media['id']; ?>">Supprimer</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination pagination-sm">
                            <li class="page-item <?php echo ($page == 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=1&media_type=<?php echo urlencode($mediaType); ?>&author=<?php echo urlencode($author); ?>"><<</a>
                            </li>
                            <li class="page-item <?php echo ($page == 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?>&media_type=<?php echo urlencode($mediaType); ?>&author=<?php echo urlencode($author); ?>"><</a>
                            </li>
                            <li class="page-item <?php echo ($page == $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo min($totalPages, $page + 1); ?>&media_type=<?php echo urlencode($mediaType); ?>&author=<?php echo urlencode($author); ?>">></a>
                            </li>
                            <li class="page-item <?php echo ($page == $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $totalPages; ?>&media_type=<?php echo urlencode($mediaType); ?>&author=<?php echo urlencode($author); ?>">>></a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modale pour l'affichage en taille réelle du média -->
    <div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aperçu du Média</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div id="mediaPreviewContainer">
                        <!-- Le média sera chargé ici via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Gestion de l'affichage du média dans la modale
        $('#mediaModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var mediaPath = button.data('media-path');
            var mediaPreviewContainer = $('#mediaPreviewContainer');
            mediaPreviewContainer.empty();
            if (mediaPath.endsWith('.jpg') || mediaPath.endsWith('.png') || mediaPath.endsWith('.gif')) {
                mediaPreviewContainer.append('<img src="' + mediaPath + '" class="img-fluid" alt="Aperçu de l\'image">');
            } else if (mediaPath.endsWith('.mp4') || mediaPath.endsWith('.avi')) {
                mediaPreviewContainer.append('<video controls class="img-fluid"><source src="' + mediaPath + '" type="video/mp4">Votre navigateur ne supporte pas ce format.</video>');
            } else if (mediaPath.endsWith('.mp3')) {
                mediaPreviewContainer.append('<audio controls class="w-100"><source src="' + mediaPath + '" type="audio/mp3">Votre navigateur ne supporte pas ce format.</audio>');
            }
        });
    
        // Suppression d'un média via AJAX
        $(document).on('click', '.delete-btn', function() {
            var mediaId = $(this).data('id');
            if (confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) {
                $.ajax({
                    url: 'delete-media.php',
                    type: 'POST',
                    data: { id: mediaId },
                    success: function(response) {
                        $('#media-' + mediaId).fadeOut();
                    }
                });
            }
        });
    </script>
</body>
</html>
