<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Carte</title>
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(-45deg, #1d003d, #211b1b, #00072b);
            background-attachment: fixed;
        }

        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #d7a900;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .message-container {
            display: none;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
        }

        .message-container.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message-container.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        select, input, textarea {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            transition: border-color 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        select:hover, input:hover, textarea:hover {
            border-color: #aaa;
        }

        .input-group {
            position: relative;
        }

        .error-message {
            color: red;
            font-size: 14px;
            display: none;
            position: absolute;
            bottom: 0px;
            left: 0;
        }

        button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            background-color: #FF3400;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #e62e00;
        }

        .invalid {
            animation: shake 0.3s;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }

        .fa {
            margin-right: 10px;
        }

        .image-preview-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .image-preview {
            max-width: 100px;
            height: auto;
            display: none;
            border: solid 3px #ed8100;
            border-radius: 5px;
            margin: auto;
            margin-top: 50px;
        }
    </style>
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

<script>
    function saveCard() {
        var category = document.getElementById("category").value;
        var cardName = document.getElementById("cardName").value;
        var description = document.getElementById("description").value;
        var link = document.getElementById("link").value;

        var isValid = true;

        if (!category) {
            document.getElementById("categoryError").style.display = "block";
            isValid = false;
        } else {
            document.getElementById("categoryError").style.display = "none";
        }

        if (!cardName.trim()) {
            document.getElementById("nameError").style.display = "block";
            document.getElementById("cardName").classList.add('invalid');
            isValid = false;
        } else {
            document.getElementById("nameError").style.display = "none";
            document.getElementById("cardName").classList.remove('invalid');
        }

        if (!description.trim()) {
            document.getElementById("descriptionError").style.display = "block";
            document.getElementById("description").classList.add('invalid');
            isValid = false;
        } else {
            document.getElementById("descriptionError").style.display = "none";
            document.getElementById("description").classList.remove('invalid');
        }

        if (!link.trim() || !isValidUrl(link)) {
            document.getElementById("linkError").style.display = "block";
            document.getElementById("link").classList.add('invalid');
            isValid = false;
        } else {
            document.getElementById("linkError").style.display = "none";
            document.getElementById("link").classList.remove('invalid');
        }

        if (isValid) {
            var formData = new FormData();
            formData.append('category', category);
            formData.append('cardName', cardName);
            formData.append('description', description);
            formData.append('link', link);

            var imageInput = document.getElementById('image');
            if (imageInput.files && imageInput.files[0]) {
                formData.append('image', imageInput.files[0]);
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_card.php', true);
            xhr.onload = function () {
                var messageContainer = document.getElementById("messageContainer");
                messageContainer.style.display = "block";

                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            messageContainer.className = "message-container success";
                            messageContainer.textContent = "Insertion réussie : " + response.message;
                            setTimeout(function() {
                                window.location.href = "SYSMI_ZONE.php";
                            }, 2000);
                        } else {
                            messageContainer.className = "message-container error";
                            messageContainer.textContent = "Erreur d'insertion : " + response.message;
                        }
                    } catch (e) {
                        messageContainer.className = "message-container error";
                        messageContainer.textContent = "Erreur lors de l'analyse de la réponse JSON.";
                    }
                } else {
                    messageContainer.className = "message-container error";
                    messageContainer.textContent = "Erreur de serveur : " + xhr.statusText;
                }
            };
            xhr.send(formData);
        }
    }

    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch (_) {
            return false;
        }
    }

    function previewImage(event) {
        var input = event.target;
        var imagePreview = document.getElementById('imagePreview');
        var imageError = document.getElementById('imageError');

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                imageError.style.display = 'none';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            imagePreview.src = '#';
            imagePreview.style.display = 'none';
            imageError.style.display = 'block';
        }
    }

    function togglePreview() {
        var imagePreview = document.getElementById('imagePreview');
        var button = document.querySelector('button[type="button"]');
        if (imagePreview.style.display === 'none') {
            imagePreview.style.display = 'block';
            button.innerHTML = '<i class="fas fa-eye-slash"></i> Masquer l\'aperçu de l\'image';
        } else {
            imagePreview.style.display = 'none';
            button.innerHTML = '<i class="fas fa-eye"></i> Afficher l\'aperçu de l\'image';
        }
    }
</script>
</body>
</html>
