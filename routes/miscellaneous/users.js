const express = require('express');
const router = express.Router();
const db = require('../../database');
const UsersData = require('../../middleware/UsersData');

// Page de liste des utilisateurs
router.get('/users', UsersData, async (req, res) => {
  if (!req.userData) {
    console.warn('⚠️ Utilisateur non connecté - /users');
    return res.render('error', { message: 'Vous devez être connecté pour voir cette page.' });
  }

  const userId = req.userData.id;
  const searchKeyword = req.query.search?.trim() || '';
  const like = `%${searchKeyword}%`;

  try {
    const [usersRes] = await db.execute(
      `SELECT u.id, u.prenom, u.nom, p.image_content, u.role, u.bio
       FROM utilisateur u
       LEFT JOIN photos_de_profil p ON u.id = p.user_id
       WHERE u.prenom LIKE ? AND u.id != ?
       ORDER BY u.prenom`,
      [like, userId]
    );

    res.render('miscellaneous/users', {
      cssFile: '/src/css/about.css',
      titre: 'Liste des utilisateurs',
      usersData: usersRes,
      searchKeyword
    });
  } catch (err) {
    console.error('❌ Erreur dans /users :', err);
    res.status(500).send('Erreur serveur');
  }
});

module.exports = router;
