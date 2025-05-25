const express = require('express');
const router = express.Router();
const db = require('../../database');
const UsersData = require('../../middleware/UsersData');

// Page des publications
router.get('/post', UsersData, async (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: 'Vous devez être connecté pour voir cette page.' });
  }

  const user_id = req.userData.id;
  const filterUserId = req.query.id || null;

  try {
    let publications = [];

    const sqlBase = `
      SELECT p.*, u.id AS user_id, u.prenom, u.nom, u.photo_profil, u.statut
      FROM publications p
      JOIN utilisateur u ON p.user_id = u.id
    `;

    if (!filterUserId) {
      [publications] = await db.execute(`${sqlBase} ORDER BY p.date_creation DESC`);
    } else {
      [publications] = await db.execute(`${sqlBase} WHERE p.user_id = ? ORDER BY p.date_creation DESC`, [filterUserId]);
    }

    // Ajout du lien vers le profil
    publications = publications.map(pub => ({
      ...pub,
      profileLink: `/profil/${pub.user_id}`
    }));

    // Fonctions utilitaires pour l'affichage
    const nl2br = str => str.replace(/\n/g, '<br>');
    const convertHashtagsToLinks = text =>
      text.replace(/#(\w+)/g, '<a href="/hashtags/$1" class="hashtag">#$1</a>');

    res.render('miscellaneous/post', {
      publications,
      user_id,
      filterUserId,
      userData: req.userData,
      nl2br,
      convertHashtagsToLinks
    });
  } catch (err) {
    console.error('Erreur dans /post :', err);
    res.status(500).send('Erreur serveur');
  }
});

module.exports = router;
