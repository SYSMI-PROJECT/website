const express = require('express');
const router = express.Router();
const UsersData = require('../../middleware/UsersData');

// Dashboard du staff
router.get('/dashboard', UsersData, (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: 'Vous devez être connecté pour accéder au tableau de bord.' });
  }

  res.render('miscellaneous/dashboard', {
    titre: "Tableau de bord",
    cssFile: "/src/css/staff.css",
    user: req.userData
  });
});

module.exports = router;
