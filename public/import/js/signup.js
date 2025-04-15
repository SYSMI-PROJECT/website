function togglePassword() {
    var passwordInput = document.getElementById("motDePasse");
    var eyeIcon = document.getElementById("eyeIcon");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
}

document.getElementById("motDePasse").addEventListener('input', function() {
    var strengthBar = document.getElementById('strengthIndicator');
    var password = this.value;
    var strength = 0;

    if (password.length >= 10) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    switch (strength) {
        case 1:
            strengthBar.style.width = '25%';
            strengthBar.style.background = 'red';
            break;
        case 2:
            strengthBar.style.width = '50%';
            strengthBar.style.background = 'orange';
            break;
        case 3:
            strengthBar.style.width = '75%';
            strengthBar.style.background = 'yellow';
            break;
        case 4:
            strengthBar.style.width = '100%';
            strengthBar.style.background = 'green';
            break;
        default:
            strengthBar.style.width = '0';
    }
});

document.getElementById("inscriptionForm").addEventListener("submit", function(event) {
    var errorMessageContainer = document.getElementById("errorMessage");
    var password = document.getElementById("motDePasse").value;
    var confirmPassword = document.getElementById("confirmMotDePasse").value;
    var nom = document.getElementById("nom").value;
    var prenom = document.getElementById("prenom").value;

    // Liste de mots interdits
    var motsInterdits = ["bite", "pénis", "sucemoi", "cul", "chatte", "seins", "boobs", "pussy", "chibre", "fils", "fille", "con", "connard", "pute", "putain", "salope", "nique", "niquer", "t'es"];
    
    // Vérification des mots interdits
    var estInterdit = motsInterdits.some(mot => nom.toLowerCase().includes(mot) || prenom.toLowerCase().includes(mot));
    if (estInterdit) {
        event.preventDefault();
        errorMessageContainer.innerHTML = '<div class="error-message">Votre nom ou prénom contient des mots inappropriés.</div>';
        return;
    }

    if (password !== confirmPassword) {
        event.preventDefault();
        errorMessageContainer.innerHTML = '<div class="error-message">Les mots de passe ne correspondent pas.</div>';
        return;
    }

    if (password.length < 10) {
        event.preventDefault();
        errorMessageContainer.innerHTML = '<div class="error-message">Le mot de passe doit contenir au moins 10 caractères.</div>';
        return;
    }

    errorMessageContainer.innerHTML = ''; // Réinitialiser les messages d'erreur
});

document.getElementById('date_naissance').addEventListener('input', function (e) {
let value = e.target.value.replace(/\D/g, ''); // Supprime tout ce qui n'est pas un chiffre
if (value.length > 8) {
    value = value.slice(0, 8); // Limite à 8 chiffres
}

let formattedValue = "";
if (value.length > 4) {
    formattedValue = value.slice(0, 2) + '/' + value.slice(2, 4) + '/' + value.slice(4);
} else if (value.length > 2) {
    formattedValue = value.slice(0, 2) + '/' + value.slice(2);
} else {
    formattedValue = value;
}

e.target.value = formattedValue;
});