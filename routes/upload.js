const express = require('express');
const path = require('path');
const fs = require('fs');
const router = express.Router();

function fileExists(filePath) {
  return fs.existsSync(filePath);
}

router.get('/videos/:filename', (req, res) => {
  const { filename } = req.params;
  const filePath = path.join(__dirname, '..', 'public', 'uploads', 'videos', filename);

  if (fileExists(filePath)) {
    res.sendFile(filePath);
  } else {
    res.status(404).send('Video not found.');
  }
});

router.get('/images/:filename', (req, res) => {
  const { filename } = req.params;
  const filePath = path.join(__dirname, '..', 'public', 'uploads', 'images', filename);

  if (fileExists(filePath)) {
    res.sendFile(filePath);
  } else {
    res.status(404).send('Image not found.');
  }
});

router.get('/audios/:filename', (req, res) => {
  const { filename } = req.params;
  const filePath = path.join(__dirname, '..', 'public', 'uploads', 'audios', filename);

  if (fileExists(filePath)) {
    res.sendFile(filePath);
  } else {
    res.status(404).send('Audio not found.');
  }
});

module.exports = router;
