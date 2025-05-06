const express = require('express');
const router = express.Router();
const db = require('../../database');

// Middleware pour vérifier si l'utilisateur est connecté
const requireLogin = (req, res, next) => {
  if (!req.session.userId) {
    return res.status(401).send("Vous devez être connecté pour accéder à cette page.");
  }
  next();
};

router.post('/visibility', requireLogin, (req, res) => {
  // Utilisation correcte du nom de la variable de session
  const user_id = req.session.userId;

  // Validation des entrées
  const visibility = req.body.visibility || 'private';
  const visibility_posts = Array.isArray(req.body.visibility_posts) 
    ? req.body.visibility_posts.join(',') 
    : ''; // S'assurer que visibility_posts est une chaîne correcte
  const notifications = req.body.notifications === 'enabled' ? 1 : 0;
  const two_factor_auth = req.body.two_factor_auth === 'enabled' ? 1 : 0;
  const dark_mode = req.body.dark_mode === 'enabled' ? 1 : 0;

  const query = `
    INSERT INTO user_settings 
    (user_id, visibility, visibility_posts, notifications, two_factor_auth, dark_mode)
    VALUES (?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
    visibility = ?, visibility_posts = ?, notifications = ?, 
    two_factor_auth = ?, dark_mode = ?
  `;

  const values = [
    user_id, visibility, visibility_posts, notifications, two_factor_auth, dark_mode,
    visibility, visibility_posts, notifications, two_factor_auth, dark_mode
  ];

  db.query(query, values, (err, result) => {
    if (err) {
      console.error('Erreur lors de la mise à jour des paramètres utilisateur :', err);
      return res.status(500).send("Erreur lors de la mise à jour des paramètres.");
    }

    // Rediriger vers le profil utilisateur après la mise à jour
    res.redirect('/');
  });
});

module.exports = router;
