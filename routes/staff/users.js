const express = require('express');
const router = express.Router();
const db = require('../../database');

router.get('/', async (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.redirect('/');
  }

  const conn = await db.getConnection();

  const [[{ total }]] = await conn.execute("SELECT COUNT(id) AS total FROM utilisateur");
  const [[{ banned }]] = await conn.execute("SELECT COUNT(id) AS banned FROM utilisateur WHERE statut = 'banni'");
  const [[{ active }]] = await conn.execute("SELECT COUNT(id) AS active FROM utilisateur WHERE statut = 'actif'");

  const [users] = await conn.execute("SELECT * FROM utilisateur");

  res.render('staff/user_gestion', {
    user: req.userData,
    users,
    total,
    active,
    banned,
    cssFile: '/src/css/userManagement.css',
  });
});

module.exports = router;
