document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const cartesUtilisateurs = document.querySelectorAll(".user-card");

    searchInput.addEventListener("input", function() {
        const searchTerm = searchInput.value.trim().toLowerCase();

        cartesUtilisateurs.forEach(function(carte) {
            const prenomElement = carte.querySelector(".text h2");
            const nomElement = carte.querySelector(".text p");
            const reseauLinks = carte.querySelectorAll(".reseau-link img");

            const prenom = prenomElement ? prenomElement.textContent.trim().toLowerCase() : "";
            const nom = nomElement ? nomElement.textContent.trim().toLowerCase() : "";

            let found = prenom.includes(searchTerm) || nom.includes(searchTerm);

            if (!found) {
                found = Array.from(reseauLinks).some(img => img.alt.trim().toLowerCase().includes(searchTerm));
            }

            carte.classList.toggle("hidden", !found);
        });
    });
});
