<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../Import/icons/Logo.png" type="image/png">
    <title>Personnalisation de la grande maison</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #35363d;
        }

        .house {
            width: 2000px;
            height: 1300px;
            background-color: black;
            background-size: cover;
            position: relative;
            margin-left: 110px;
            margin-top: 20px;
        }

        .item {
            position: absolute;
            cursor: pointer;
        }

        #controlSquare {
            width: 50px;
            height: 50px;
            background-color: #272727;
            position: absolute;
            top: 1268.2px;
            left: 1148.92px;
            border-radius: 5px;
            border: solid 5px green;
        }

        .item-manager {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 10px;
            width: 300px;
            height: 200px;
            text-align: center;
            font-size: 18px;
            color: #333;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* CSS pour le bouton */
        .item-manager-button {
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            position: absolute;
            height: 70px;
            width: 70px;
            margin: -66em auto;
            left: 160em;
            top: 73em;
        }

        .item-manager-button:hover {
            background-color: #0056b3;
        }

        .item-manager-button img {
            height: 50px;
            width: 50px;
        }

        /* CSS pour le gestionnaire d'items */
        .item-selector {
            display: none;
            position: fixed;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            background-color: #f9f9f9; /* Couleur de fond */
            border: 1px solid #ccc;
            padding: 20px; /* Marge intérieure */
            border-radius: 10px; /* Bord arrondi */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 9999;
            height: 84em;
            width: 30em;
        }

        .item-selector h2 {
            font-size: 20px; /* Taille de police */
            color: #333; /* Couleur du texte */
            margin-bottom: 10px; /* Marge en bas */
        }

        .item-selector ul {
            list-style: none;
            padding: 0;
            margin: 0; /* Enlever la marge */
            display: flex; /* Utiliser Flexbox */
            flex-wrap: wrap; /* Permettre le passage à la ligne */
        }

        .item-selector ul li {
            cursor: pointer;
            border: none;
            margin-right: 17px;
            margin-bottom: 20px;
        }

        .item-selector ul li:hover {
            background-color: #e0e0e0; /* Couleur de fond au survol */
        }

        .item-selector ul li img {
            width: 100px; /* Largeur de l'image */
            height: 100px; /* Hauteur de l'image */
            object-fit: cover; /* Pour couvrir l'espace */
        }
    </style>
</head>
<body>
    <div class="house" id="house">
        <!-- Conteneur pour les items -->
    </div>
    <div id="controlSquare"></div>

    <!-- Fenêtre du gestionnaire d'items -->
    <div class="item-selector" id="itemSelector">
        <h2>Sélectionner un item :</h2>
        <ul>
            <li onclick="addItemToHouse('item1')"><img src="../PNG/Item/Super étoile.png" alt="Image 1"></li>
            <li onclick="addItemToHouse('item2')"><img src="../PNG/Item/Super étoile.png" alt="Image 2"></li>
            <li onclick="addItemToHouse('item3')"><img src="../PNG/Item/Super étoile.png" alt="Image 3"></li>
            <li onclick="addItemToHouse('item4')"><img src="../PNG/Item/Super étoile.png" alt="Image 4"></li>
            <li onclick="addItemToHouse('item5')"><img src="../PNG/Item/Super étoile.png" alt="Image 5"></li>
            <li onclick="addItemToHouse('item6')"><img src="../PNG/Item/Super étoile.png" alt="Image 6"></li>
            <!-- Ajoutez autant d'images que nécessaire -->
        </ul>
    </div>

    <script>
        // Création et gestion du carré de contrôle
        const controlSquare = document.getElementById('controlSquare');
        const speed = 10;
        let moveX = 0;
        let moveY = 0;

        window.addEventListener('keydown', (e) => {
            const controlSquareRect = controlSquare.getBoundingClientRect();
            const houseRect = document.getElementById('house').getBoundingClientRect();

            switch (e.key) {
                case 'ArrowUp':
                    if (controlSquareRect.top - speed > houseRect.top) {
                        moveY = -speed;
                    }
                    break;
                case 'ArrowDown':
                    if (controlSquareRect.bottom + speed < houseRect.bottom) {
                        moveY = speed;
                    }
                    break;
                case 'ArrowLeft':
                    if (controlSquareRect.left - speed > houseRect.left) {
                        moveX = -speed;
                    }
                    break;
                case 'ArrowRight':
                    if (controlSquareRect.right + speed < houseRect.right) {
                        moveX = speed;
                    }
                    break;
            }
        });

        window.addEventListener('keyup', () => {
            moveX = 0;
            moveY = 0;
        });

        function moveControlSquare() {
            const controlSquareRect = controlSquare.getBoundingClientRect();
            const houseRect = document.getElementById('house').getBoundingClientRect();

            if (
                (moveX < 0 && controlSquareRect.left - speed > houseRect.left) || 
                (moveX > 0 && controlSquareRect.right + speed < houseRect.right)
            ) {
                controlSquare.style.left = `${controlSquareRect.left + moveX}px`;
            }

            if (
                (moveY < 0 && controlSquareRect.top - speed > houseRect.top) || 
                (moveY > 0 && controlSquareRect.bottom + speed < houseRect.bottom)
            ) {
                controlSquare.style.top = `${controlSquareRect.top + moveY}px`;
            }
        }

        setInterval(moveControlSquare, 50);

        // Fonction pour ouvrir ou fermer le gestionnaire d'items
        function toggleItemManager() {
            const itemSelector = document.getElementById('itemSelector');
            if (itemSelector.style.display === 'block') {
                itemSelector.style.display = 'none';
            } else {
                itemSelector.style.display = 'block';
            }
        }

        // Fonction pour ajouter un item à la maison
        function addItemToHouse(item) {
            alert("Item ajouté à la maison : " + item);
            // Implémentez ici la logique pour ajouter l'item sélectionné à la maison
            // Vous pouvez ajouter du code ici pour placer l'item dans la maison
            // par exemple, en créant un nouvel élément HTML et en le positionnant
            // à l'emplacement désiré dans la maison
        }
    </script>

    <button class="item-manager-button" onclick="toggleItemManager()"><img src="../PNG/Item/Super étoile.png"></button>

    <button class="item-manager-button" style="margin-top: -59em;" onclick="toggleItemManager()"><img src="../PNG/Item/Super étoile.png"></button>
</body>
</html>