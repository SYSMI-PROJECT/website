const express = require('express');
const path = require('path');
const fs = require('fs');
const pool = require('../../database');

const router = express.Router();

router.post('/', async (req, res) => {
  try {
    if (!req.session.userId) return res.redirect('/error?type=non_connecte');

    const connection = await pool.getConnection();
    const [rows] = await connection.query('SELECT photo_profil FROM utilisateur WHERE id = ?', [req.session.userId]);

    if (rows[0].photo_profil) {
      const filePath = path.join(__dirname, '../../public', rows[0].photo_profil);
      if (fs.existsSync(filePath)) fs.unlinkSync(filePath); // Supprime le fichier
    }

    await connection.query('UPDATE utilisateur SET photo_profil = NULL WHERE id = ?', [req.session.userId]);
    connection.release();

    res.redirect(`/profil/${req.session.userId}`);
  } catch (error) {
    console.error('Erreur suppression photo :', error);
    res.redirect('/error?type=delete_pdp_exception');
  }
});

module.exports = router;
