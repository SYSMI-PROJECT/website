<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gartic Phone</title>
<style>
    /* CSS pour l'amélioration de l'interface utilisateur */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        background: linear-gradient(to bottom, #4CAF50, #45a049);
        color: #fff;
    }

    #container {
        background-color: rgba(255, 255, 255, 0.8);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
        max-width: 800px;
        text-align: center;
    }

    h1 {
        color: #333333;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        margin-bottom: 20px;
    }

    #drawingArea {
        margin-bottom: 20px;
    }

    #canvas {
        border: 2px solid black;
        cursor: crosshair;
        background-color: #ffffff;
        transition: all 0.3s ease;
    }

    #wordArea {
        text-align: center;
    }

    #wordInput {
        padding: 8px;
        margin-bottom: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    #submitWord, #clearCanvas, .shapeBtn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 10px;
    }

    #submitWord {
        background-color: #4CAF50;
        color: white;
    }

    #submitWord:hover {
        background-color: #45a049;
    }

    #clearCanvas {
        background-color: #f44336;
        color: white;
    }

    #clearCanvas:hover {
        background-color: #d32f2f;
    }

    .shapeBtn {
        background-color: #008CBA;
        color: white;
    }

    .shapeBtn:hover {
        background-color: #005A6E;
    }

    #colorPicker {
        margin-top: 10px;
        transition: all 0.3s ease;
    }

    /* Animation pour le conteneur */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    /* Styles pour les icônes */
    .icon {
        width: 30px;
        height: 30px;
        margin: 0 5px;
        cursor: pointer;
    }
</style>
</head>
<body>
<div id="container">
    <h1>Gartic Phone</h1>
    <div id="drawingArea">
        <canvas id="canvas" width="800" height="600"></canvas>
        <input type="color" id="colorPicker" value="#000000">
        <img src="pencil.png" class="icon" onclick="selectedShape=''">
        <img src="rectangle.png" class="icon" onclick="selectedShape='rectangle'">
        <img src="circle.png" class="icon" onclick="selectedShape='circle'">
        <img src="triangle.png" class="icon" onclick="selectedShape='triangle'">
        <button id="clearCanvas" onclick="clearCanvas()">Effacer</button>
    </div>
    <div id="wordArea">
        <input type="text" id="wordInput" placeholder="Entrez votre mot">
        <button id="submitWord">Soumettre</button>
    </div>
</div>
<script>
    // Drawing variables
    let canvas = document.getElementById('canvas');
    let context = canvas.getContext('2d');
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;
    let selectedShape = '';
    let startX = 0;
    let startY = 0;

    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    function startDrawing(e) {
        isDrawing = true;
        [lastX, lastY] = [e.offsetX, e.offsetY];
        startX = e.offsetX;
        startY = e.offsetY;
    }

    function draw(e) {
        if (!isDrawing) return;
        context.lineWidth = 5;
        context.lineCap = 'round';
        context.strokeStyle = document.getElementById('colorPicker').value;

        if (selectedShape === '') {
            context.beginPath();
            context.moveTo(lastX, lastY);
            context.lineTo(e.offsetX, e.offsetY);
            context.stroke();
            [lastX, lastY] = [e.offsetX, e.offsetY];
        } else {
            let width = e.offsetX - startX;
            let height = e.offsetY - startY;

            if (selectedShape === 'rectangle') {
                context.beginPath();
                context.rect(startX, startY, width, height);
                context.stroke();
            } else if (selectedShape === 'circle') {
                let radius = Math.sqrt(width * width + height * height) / 2;
                context.beginPath();
                context.arc(startX + width / 2, startY + height / 2, radius, 0, Math.PI * 2);
                context.stroke();
            } else if (selectedShape === 'triangle') {
                context.beginPath();
                context.moveTo(startX + width / 2, startY);
                context.lineTo(startX, startY + height);
                context.lineTo(startX + width, startY + height);
                context.closePath();
                context.stroke();
            }
        }
    }

    function stopDrawing() {
        isDrawing = false;
    }

    function clearCanvas() {
        context.clearRect(0, 0, canvas.width, canvas.height);
    }

    // Word submission
    let wordInput = document.getElementById('wordInput');
    let submitWordBtn = document.getElementById('submitWord');

    submitWordBtn.addEventListener('click', submitWord);

    function submitWord() {
        let word = wordInput.value.trim();
        if (word !== '') {
            // Code to send the word to other players
            console.log('Submitted word:', word);
            wordInput.value = '';
        }
    }

    // Animation de fadeIn pour le conteneur
    document.getElementById('container').style.animation = 'fadeIn 0.5s ease';
</script>
</body>
</html>
