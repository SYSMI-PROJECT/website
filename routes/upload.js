const express = require('express');
const path = require('path');
const fs = require('fs');
const router = express.Router();

// Fonction pour vérifier si le fichier existe
function fileExists(filePath) {
  return fs.existsSync(filePath);
}

// Route pour accéder aux vidéos
router.get('/videos/:filename', (req, res) => {
  const { filename } = req.params;
  const filePath = path.join(__dirname, '..', 'public', 'uploads', 'vidéos', filename);

  if (fileExists(filePath)) {
    res.sendFile(filePath);
  } else {
    res.status(404).send('Vidéo non trouvée.');
  }
});

// Route pour accéder aux images
router.get('/images/:filename', (req, res) => {
  const { filename } = req.params;
  const filePath = path.join(__dirname, '..', 'public', 'uploads', 'images', filename);

  if (fileExists(filePath)) {
    res.sendFile(filePath);
  } else {
    res.status(404).send('Image non trouvée.');
  }
});

// Route pour accéder aux audios
router.get('/audios/:filename', (req, res) => {
  const { filename } = req.params;
  const filePath = path.join(__dirname, '..', 'public', 'uploads', 'audios', filename);

  if (fileExists(filePath)) {
    res.sendFile(filePath);
  } else {
    res.status(404).send('Audio non trouvé.');
  }
});

module.exports = router;
