document.getElementById("loginForm").addEventListener("submit", function(event) {
    let valid = true;
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");

    // Réinitialiser les messages d'erreur
    emailError.style.display = "none";
    passwordError.style.display = "none";

    // Validation de l'email
    if (!emailInput.value.includes("@") || !emailInput.value.includes(".")) {
        emailError.style.display = "block";
        valid = false;
    }

    // Validation du mot de passe
    if (passwordInput.value.trim() === "") {
        passwordError.style.display = "block";
        valid = false;
    }

    // Empêche la soumission si les champs ne sont pas valides
    if (!valid) {
        event.preventDefault();
    }
});