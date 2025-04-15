<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Carte</title>
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="/public/import/css/discord/add_server.css">
</head>
<body>

<div class="form-container">
    <h2><i class="fas fa-plus"></i> AJOUTER UN SERVEUR</h2>
    <form id="cardForm">
        

        <div class="image-preview-container">
            <label for="imagePreview"><i class="fas fa-eye"></i> Aperçu de l'image:</label>
            <img id="imagePreview" class="image-preview" src="#" alt="Aperçu de l'image">
        </div>

        <div class="input-group">
            <label for="image"><i class="fas fa-image"></i> Image:</label>
            <input type="file" id="image" accept="image/*" onchange="previewImage(event)">
            <span class="error-message" id="imageError"><i class="fas fa-exclamation-circle"></i> Veuillez sélectionner une image.</span>
        </div>

        <button type="button" onclick="togglePreview()" style="margin-bottom: 20px;"><i class="fas fa-eye"></i> Afficher l'aperçu de l'image</button>

        <div class="input-group">
            <label for="category"><i class="fas fa-folder"></i> Catégorie:</label>
            <select id="category" required>
                <option value="rencontre">Rencontre</option>
                <option value="gaming">Gaming</option>
                <option value="anime">Animé</option>
                <option value="nsfw">NSFW</option>
                <option value="development">Développement</option>
            </select>
            <span class="error-message" id="categoryError"><i class="fas fa-exclamation-circle"></i> Veuillez sélectionner une catégorie.</span>
        </div>

        <div class="input-group">
            <label for="cardName"><i class="fas fa-file-alt"></i> Nom du serveur:</label>
            <input type="text" id="cardName" required>
            <span class="error-message" id="nameError"><i class="fas fa-exclamation-circle"></i> Veuillez entrer un nom pour la carte.</span>
        </div>

        <div class="input-group">
            <label for="description"><i class="fas fa-align-left"></i> Description:</label>
            <textarea id="description" rows="4" required></textarea>
            <span class="error-message" id="descriptionError"><i class="fas fa-exclamation-circle"></i> Veuillez entrer une description pour la carte.</span>
        </div>

        <div class="input-group">
            <label for="link"><i class="fas fa-link"></i> Lien:</label>
            <input type="url" id="link" required>
            <span class="error-message" id="linkError"><i class="fas fa-exclamation-circle"></i> Veuillez entrer un lien valide.</span>
        </div>

        <div id="messageContainer" class="message-container"></div>

        <button type="button" onclick="saveCard()"><i class="fas fa-plus"></i> Ajouter ma Carte</button>
    </form>
</div>

<script src="/public/import/js/discord/add_server.js"></script>
</body>
</html>
