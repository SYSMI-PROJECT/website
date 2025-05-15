const express = require('express');
const router = express.Router();
const db = require('../../database');

router.get('/', async (req, res) => {
  try {
    const token = req.cookies.stay_connected;

    if (token) {
      await db.execute('DELETE FROM user_tokens WHERE token = ?', [token]);

      res.clearCookie('stay_connected');
    }

    const { VIP, prenom, nom, email, userId } = req.session;

    req.session.destroy(err => {
      if (err) {
        console.error('Error destroying session:', err);
        return res.status(500).send('Server error while disconnecting');
      }

      req.session = null;

      res.cookie('VIP', VIP || false, { maxAge: 3600000, httpOnly: false });

      req.session = {};
      req.session.VIP = VIP;
      req.session.prenom = prenom;
      req.session.nom = nom;
      req.session.email = email;
      req.session.userId = userId;

      return res.redirect('/publication');
    });

  } catch (err) {
    console.error('Error while logging out:', err);
    res.status(500).send('Server error while disconnecting.');
  }
});

module.exports = router;
