const express = require('express');
const router = express.Router();
const bcrypt = require('bcrypt');
const db = require('../../database');

// Route principale de connexion (POST vers /)
router.post('/', async (req, res) => {
  const { email, password, stayConnected } = req.body;

  console.log('Tentative de connexion avec email:', email);

  try {
    const [rows] = await db.execute(
      'SELECT id, nom, prenom, password, statut, verified FROM utilisateur WHERE email = ?',
      [email]
    );

    console.log('Résultat de la requête DB:', rows);

    if (rows.length === 0) {
      console.log('Aucun utilisateur trouvé avec cet email');
      return res.render('errors/unknown_account');
    }

    const user = rows[0];

    if (!password) {
      console.log('Mot de passe manquant');
      return res.render('errors/password_required');
    }

    const passwordMatch = await bcrypt.compare(password, user.password);
    console.log('Comparaison du mot de passe:', passwordMatch);

    if (!passwordMatch) {
      console.log('Mot de passe incorrect');
      return res.render('errors/incorrect_password');
    }

    if (user.statut !== 'actif') {
      console.log('Compte non actif');
      return res.render('errors/account_banned');
    }
    if (user.verified !== 1) {
      console.log('Compte non vérifié');
      return res.render('errors/account_not_verified');
    }

    // Stockage des informations de session
    req.session.userId = user.id;
    console.log('Session créée avec userId:', req.session.userId);
    console.log('Contenu complet de la session après connexion :', req.session);

    if (stayConnected) {
      // À compléter avec la logique de token/cookie
    }

    res.redirect('/');
  } catch (err) {
    console.error('Erreur pendant la connexion :', err);
    res.status(500).send('Erreur serveur pendant la connexion.');
  }
});

module.exports = router;
