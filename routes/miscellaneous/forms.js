const express = require('express');
const router = express.Router();

// Formulaires PDP
router.get('/add_pdp', (req, res) => {
  res.render('miscellaneous/forms/add_pdp', {
    cssFile: '/src/css/contact.css',
    titre: 'Ajouter PDP'
  });
});

router.get('/pdp_editor', (req, res) => {
  res.render('miscellaneous/forms/pdp_editor', {
    cssFile: '/src/css/contact.css',
    titre: 'Éditeur PDP'
  });
});

module.exports = router;
