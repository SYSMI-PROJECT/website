document.addEventListener('DOMContentLoaded', function () {
    // Récupérer les cartes depuis le serveur Express
    fetchCards();

    // Toggle menu
    const menuToggle = document.querySelector(".menu-toggle");
    const menu = document.querySelector(".menu");
    if (menuToggle && menu) {
        menuToggle.addEventListener("click", function () {
            menu.classList.toggle("active");
        });
    }
});

function fetchCards() {
    fetch('/discord/SYSMI-ZONE/data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                generateCardNumbers(data.cards);
                displayCards(data.cards);
            } else {
                console.error("Erreur de récupération des cartes : " + data.message);
            }
        })
        .catch(error => {
            console.error("Erreur de requête : ", error);
        });
}

function generateCardNumbers(data) {
    const categories = {};
    data.forEach(card => {
        if (!categories[card.category]) {
            categories[card.category] = 1;
        } else {
            categories[card.category]++;
        }
        card.number = ("000" + categories[card.category]).slice(-3);
    });
}

function displayCards(cards) {
    const cardContainer = document.getElementById("cardContainer");
    cardContainer.innerHTML = "";

    cards.forEach(card => {
        const cardElement = document.createElement("div");
        cardElement.classList.add("card");
        cardElement.setAttribute("data-category", card.category);
        cardElement.setAttribute("data-number", card.number);

        cardElement.innerHTML = `
            <h3>${card.cardName}</h3>
            <div class="card-details">
                <div class="server-tag" style="background-image: url('${card.image}')"></div>
                <div class="card-details-text">
                    <p>Numéro: ${card.number}</p>
                    <p>Catégorie: ${capitalize(card.category)}</p>
                </div>
            </div>
            <div class="description">
                <h1>${card.description}</h1>
            </div>
            <a href="${card.link}" target="_blank" class="join-button">Rejoindre</a>
        `.trim();

        cardContainer.appendChild(cardElement);
    });
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function filterCards() {
    const filterValue = document.getElementById("filter").value;
    const cards = document.getElementsByClassName("card");

    for (let i = 0; i < cards.length; i++) {
        const match = filterValue === "all" || cards[i].getAttribute("data-category") === filterValue;
        cards[i].style.display = match ? "block" : "none";
    }
}

function searchCards() {
    const searchValue = document.getElementById("search").value;
    const cards = document.getElementsByClassName("card");

    for (let i = 0; i < cards.length; i++) {
        const match = cards[i].getAttribute("data-number").includes(searchValue);
        cards[i].style.display = match ? "block" : "none";
    }
}
