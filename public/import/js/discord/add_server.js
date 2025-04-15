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