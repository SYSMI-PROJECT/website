const express = require('express');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const pool = require('../../database');

const router = express.Router();

const uploadFolder = path.join(__dirname, '../../public/uploads/avatar');
if (!fs.existsSync(uploadFolder)) {
  fs.mkdirSync(uploadFolder, { recursive: true });
}

// Configuration Multer
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, uploadFolder);
  },
  filename: async (req, file, cb) => {
    const userId = req.session?.userId;
    const ext = path.extname(file.originalname);
    const baseName = `pdp_${userId}`;

    // Supprimer ancien fichier si présent (jpeg, png, gif)
    const possibleExts = ['.png', '.jpg', '.jpeg', '.gif'];
    possibleExts.forEach(ext => {
      const fullPath = path.join(uploadFolder, `${baseName}${ext}`);
      if (fs.existsSync(fullPath)) {
        fs.unlinkSync(fullPath);
      }
    });

    cb(null, `${baseName}${ext}`);
  }
});
const upload = multer({ storage });

// Route de remplacement
router.post('/', upload.single('photo'), async (req, res) => {
  const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
  const maxSize = 5 * 1024 * 1024;

  try {
    if (!req.session.userId) return res.redirect('/error?type=non_connecte');
    if (!req.file) return res.redirect('/error?type=upload_error');

    const { mimetype, size, filename } = req.file;
    const filePath = `/uploads/avatar/${filename}`;

    if (!allowedTypes.includes(mimetype)) return res.redirect('/error?type=type');
    if (size > maxSize) return res.redirect('/error?type=taille');

    const connection = await pool.getConnection();
    await connection.query(
      'UPDATE utilisateur SET photo_profil = ? WHERE id = ?',
      [filePath, req.session.userId]
    );
    connection.release();

    console.log("PDP remplacée :", filePath);
    res.redirect(`/profil/${req.session.userId}`);
  } catch (error) {
    console.error("Erreur remplacement PDP :", error);
    res.redirect('/error?type=upload_exception');
  }
});

module.exports = router;
