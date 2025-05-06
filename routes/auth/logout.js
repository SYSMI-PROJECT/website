const express = require('express');
const router = express.Router();
const db = require('../../database');

// Route de déconnexion
router.get('/', async (req, res) => {
  try {
    const token = req.cookies.stay_connected;

    // Supprimer le token de la base de données
    if (token) {
      await db.execute('DELETE FROM user_tokens WHERE token = ?', [token]);

      // Supprimer le cookie côté client
      res.clearCookie('stay_connected');
    }

    // Sauvegarder temporairement les données importantes avant suppression de la session
    const { VIP, prenom, nom, email, userId } = req.session;

    // Détruire la session
    req.session.destroy(err => {
      if (err) {
        console.error('Erreur lors de la destruction de session :', err);
        return res.status(500).send('Erreur serveur pendant la déconnexion.');
      }

      // Réinitialiser une nouvelle session pour garder quelques infos si besoin
      req.session = null;

      // Recréer une nouvelle session et y mettre les données VIP
      res.cookie('VIP', VIP || false, { maxAge: 3600000, httpOnly: false });

      req.session = {};
      req.session.VIP = VIP;
      req.session.prenom = prenom;
      req.session.nom = nom;
      req.session.email = email;
      req.session.userId = userId;

      // Redirection après déconnexion
      return res.redirect('/publication');
    });

  } catch (err) {
    console.error('Erreur lors de la déconnexion :', err);
    res.status(500).send('Erreur serveur lors de la déconnexion.');
  }
});

module.exports = router;
