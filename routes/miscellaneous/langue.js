const express = require('express');
const router = express.Router();

// Langue / Contact
router.get('/langue', (req, res) => {
  res.render('miscellaneous/settings/langue', {
    cssFile: '/src/css/contact.css',
    titre: 'Contact'
  });
});

module.exports = router;
