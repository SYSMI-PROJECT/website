const express = require('express');
const router = express.Router();
const UsersData = require('../../middleware/UsersData');

// Page de la boutique
router.get('/store', UsersData, async (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: 'Vous devez être connecté pour accéder à la boutique.' });
  }

  try {
    res.render('miscellaneous/store', {
      user: req.userData,
      produits: req.produitsData
    });
  } catch (error) {
    console.error("Erreur lors de l'affichage de la boutique:", error);
    res.status(500).send("Erreur serveur");
  }
});

module.exports = router;
