const express = require('express');
const router = express.Router();
const db = require('../../database');
const UsersData = require('../../middleware/UsersData');

// POST /forms/feedback — Envoi du feedback
router.post('/', UsersData, async (req, res) => {
  const user = req.userData;
  if (!user) return res.status(401).send('Accès refusé : utilisateur non authentifié.');

  const userId = user.id;
  const feedback = (req.body.feedback || '').trim();
  const rating = parseInt(req.body.rating, 10);

  if (!feedback || isNaN(rating) || rating < 1 || rating > 5) {
    return res.status(400).send('Veuillez remplir tous les champs correctement.');
  }

  try {
    const [result] = await db.execute(
      'INSERT INTO feedback (user_id, feedback, rating) VALUES (?, ?, ?)',
      [userId, feedback, rating]
    );

    if (result.affectedRows > 0) {
      return res.render('errors/thanks-feedback');
    } else {
      return res.status(500).send("Erreur lors de l'envoi de votre avis.");
    }
  } catch (err) {
    console.error('Erreur lors de l\'insertion du feedback :', err);
    return res.status(500).send('Erreur serveur');
  }
});

// GET /forms/feedback — Affichage des témoignages
router.get('/', async (req, res) => {
  try {
    const [rows] = await db.execute(`
      SELECT u.prenom, u.nom, f.feedback, f.rating, p.image_content
      FROM feedback f
      JOIN utilisateur u ON f.user_id = u.id
      LEFT JOIN photos_de_profil p ON u.id = p.user_id
      ORDER BY f.id DESC
    `);

return res.redirect('/');
  } catch (err) {
    console.error('Erreur lors de la récupération des feedbacks :', err);
    res.status(500).send('Erreur serveur');
  }
});

module.exports = router;
