document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les cartes depuis le serveur
    fetchCards();
});

function fetchCards() {
    fetch('fetch_cards.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                generateCardNumbers(data.cards); // Générer les numéros de carte
                displayCards(data.cards); // Afficher les cartes
            } else {
                console.error("Erreur de récupération des cartes : " + data.message);
            }
        })
        .catch(error => {
            console.error("Erreur de requête : ", error);
        });
}

function generateCardNumbers(data) {
    var categories = {};
    data.forEach(function(card) {
        if (!categories[card.category]) {
            categories[card.category] = 1;
        } else {
            categories[card.category]++;
        }
        card.number = ("000" + categories[card.category]).slice(-3);
    });
}

function displayCards(cards) {
var cardContainer = document.getElementById("cardContainer");
cardContainer.innerHTML = "";
cards.forEach(function(card) {
    var cardElement = document.createElement("div");
    cardElement.classList.add("card");
    cardElement.setAttribute("data-category", card.category);
    cardElement.setAttribute("data-number", card.number);

    var cardInnerHTML = `
<h3>${card.cardName}</h3>
<div class="card-details">
    <div class="server-tag" style="background-image: url('${card.image}')"></div>
    <div class="card-details-text">
        <p>Numéro: ${card.number}</p>
        <p>Catégorie: ${card.category.charAt(0).toUpperCase() + card.category.slice(1)}</p>
    </div>
</div>
<div class="description">
    <h1>${card.description}</h1>
</div>
<a href="${card.link}" target="_blank" class="join-button">Rejoindre</a>
`;

    cardElement.innerHTML = cardInnerHTML.trim().split('\n').map(line => line.trim()).join('\n');
    cardContainer.appendChild(cardElement);
});
}


function filterCards() {
    var filterValue = document.getElementById("filter").value;
    var cards = document.getElementsByClassName("card");

    for (var i = 0; i < cards.length; i++) {
        if (filterValue === "all") {
            cards[i].style.display = "block";
        } else if (cards[i].getAttribute("data-category") === filterValue) {
            cards[i].style.display = "block";
        } else {
            cards[i].style.display = "none";
        }
    }
}

function searchCards() {
    var searchValue = document.getElementById("search").value;
    var cards = document.getElementsByClassName("card");

    for (var i = 0; i < cards.length; i++) {
        if (cards[i].getAttribute("data-number").includes(searchValue)) {
            cards[i].style.display = "block";
        } else {
            cards[i].style.display = "none";
        }
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const menuToggle = document.querySelector(".menu-toggle");
    const menu = document.querySelector(".menu");

    menuToggle.addEventListener("click", function() {
        menu.classList.toggle("active");
    });
});