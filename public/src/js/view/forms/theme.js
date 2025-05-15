document.addEventListener("DOMContentLoaded", function () {
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme) {
        setTheme(savedTheme);
        // Coche le bouton correspondant
        document.querySelectorAll('.theme-item input').forEach(input => {
            if (input.value === savedTheme) {
                input.checked = true;
            }
        });
    }
});

// Écouteurs d'événements sur les inputs radio
document.querySelectorAll('.theme-item input').forEach(input => {
    input.addEventListener("change", function () {
        setTheme(this.value);
    });
});

// Fonction pour changer le thème
function setTheme(theme) {
    document.body.style.background = getThemeColor(theme);
    localStorage.setItem("theme", theme);

    // Mettre en surbrillance le thème sélectionné
    document.querySelectorAll('.theme-item').forEach(item => {
        item.classList.remove("selected");
        if (item.querySelector("input").value === theme) {
            item.classList.add("selected");
        }
    });
}

// Obtenir la couleur de fond en fonction du thème
function getThemeColor(theme) {
    switch (theme) {
        case "blanc": return "#ffffff";
        case "noir": return "#222";
        case "bleu": return "#007bff";
        case "rouge": return "#dc3545";
        case "vert": return "#28a745";
        default: return "#ffffff";
    }
}