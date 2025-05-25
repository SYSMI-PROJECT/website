const express = require('express');
const router = express.Router();
const db = require('../../database');
const UsersData = require('../../middleware/UsersData');

router.get('/', UsersData, async (req, res) => {
  const hashtag = req.query.hashtag;

  if (!hashtag) {
    return res.status(400).render('error', { message: 'Aucun hashtag spécifié.' });
  }

  try {
    const [publications] = await db.execute(`
      SELECT p.*, u.id AS user_id, u.prenom, u.nom, u.statut, u.photo_profil
      FROM publications p
      JOIN utilisateur u ON p.user_id = u.id
      WHERE p.hashtags LIKE ?
      ORDER BY p.date_creation DESC
    `, [`%#${hashtag}%`]);

    // Log debug
    console.log("Hashtag recherché :", hashtag);
    console.log("Publications trouvées :", publications.length);

    res.render('search/hashtag', {
      hashtag,
      publications,
      userData: req.userData,
      cssFile: '/src/css/pages/hashtag.css',
      titre: `#${hashtag}`
    });

  } catch (err) {
    console.error('Erreur dans /search :', err);
    res.status(500).render('error', { message: 'Erreur lors de la recherche.' });
  }
});

module.exports = router;
