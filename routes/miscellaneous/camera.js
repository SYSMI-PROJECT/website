const express = require('express');
const router = express.Router();
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const db = require('../../database');
const UserData = require('../../middleware/UsersData');
const { v4: uuidv4 } = require('uuid');

// Middleware : session + user data
router.use((req, res, next) => {
  if (!req.session.userId) {
    return res.redirect('/Import/Error/No_connected');
  }
  next();
});
router.use(UserData);

// Multer pour le traitement des fichiers uploadés
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    let dest = 'public/uploads/media/autres/';
    if (file.mimetype.startsWith('image/')) dest = 'public/uploads/images/';
    else if (file.mimetype.startsWith('video/')) dest = 'public/uploads/videos/';
    else if (file.mimetype === 'image/gif') dest = 'public/uploads/gifs/';
    fs.mkdirSync(dest, { recursive: true });
    cb(null, dest);
  },
  filename: (req, file, cb) => {
    const safeName = file.originalname.replace(/[^a-zA-Z0-9.\-_]/g, '');
    cb(null, `${Date.now()}_${safeName}`);
  }
});
const upload = multer({ storage });

// GET : Formulaire de publication
router.get('/', (req, res) => {
  res.render('miscellaneous/camera', {
    csrfToken: req.session.csrf_token || (req.session.csrf_token = uuidv4()),
    prenom: req.userData.prenom,
    avatar: req.userData.avatar,
    cssFile: '/src/css/publication.css',
    titre: 'Nouvelle publication'
  });
});

// POST : Envoi d’une publication
router.post('/submit', upload.single('media'), async (req, res) => {
  const { contenu, hashtags, media_mode, recorded_media, csrf_token } = req.body;
  const user_id = req.session.userId;

  if (!csrf_token || csrf_token !== req.session.csrf_token) {
    return res.status(403).send('CSRF token invalide.');
  }

  const { prenom, avatar } = req.userData;

  let mediaPath = null;
  let via_webcam = 0;

  if (media_mode === 'record' && recorded_media) {
    via_webcam = 1;
    const matches = recorded_media.match(/^data:(.*?);base64,(.*)$/);
    if (!matches) return res.status(400).send('Format média incorrect.');
    const mime = matches[1];
    const data = matches[2];
    const ext = mime.includes('video') ? 'webm' : 'png';

    let folder = 'public/uploads/media/';
    folder += mime.includes('image') ? 'images/' : mime.includes('video') ? 'videos/' : 'autres/';
    fs.mkdirSync(folder, { recursive: true });

    const filename = `recorded_${Date.now()}_${user_id}.${ext}`;
    const filePath = path.join(folder, filename);
    fs.writeFileSync(filePath, Buffer.from(data, 'base64'));
    mediaPath = filePath.replace('public', '');
  } else if (req.file) {
    mediaPath = req.file.path.replace('public', '');
  }

  await db.query(`
    INSERT INTO publications (auteur, contenu, avatar, media_path, user_id, hashtags, via_webcam)
    VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [prenom, contenu, avatar, mediaPath, user_id, hashtags || '', via_webcam]);

  res.redirect('/post');
});

// GET : toutes les publications
router.get('/all', async (req, res) => {
  const [rows] = await db.query('SELECT * FROM publications ORDER BY id DESC');
  res.json(rows);
});

module.exports = router;
