const express = require('express');
const router = express.Router();
const bcrypt = require('bcrypt');
const db = require('../../database');

router.post('/', async (req, res) => {
  const { email, password, stayConnected } = req.body;

  // console.log('Tentative de connexion avec email:', email); Maybe a log for debug?

  try {
    const [rows] = await db.execute(
      'SELECT id, nom, prenom, password, statut, verified FROM utilisateur WHERE email = ?',
      [email]
    );

    console.log('DB query result:', rows);

    if (rows.length === 0) {
      console.log('No users found with this email');
      return res.render('errors/unknown_account');
    }

    const user = rows[0];

    if (!password) {
      console.log('Missing password');
      return res.render('errors/required_password');
    }

    const passwordMatch = await bcrypt.compare(password, user.password);
    console.log('Password comparison:', passwordMatch);

    if (!passwordMatch) {
      console.log('Incorrect password');
      return res.render('errors/incorrect_password');
    }

    if (user.statut !== 'actif') {
      console.log('Account is banned');
      return res.render('errors/account_banned');
    }
    if (user.verified !== 1) {
      console.log('Unverified account');
      return res.render('errors/not_verified_account');
    }

    req.session.userId = user.id;
    console.log('Session créée avec userId:', req.session.userId);
    console.log('Contenu complet de la session après connexion :', req.session);

    if (stayConnected) {
      // To be completed with token/cookie logic
    }

    res.redirect('/');
  } catch (err) {
    console.error('Error during connection :', err);
    res.status(500).send('Server error during connection.');
  }
});

module.exports = router;
