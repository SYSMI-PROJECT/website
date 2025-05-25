const express = require('express');
const router = express.Router();

// Langue / Contact
router.get('/about', (req, res) => {
  res.render('miscellaneous/about', {
    cssFile: '/src/css/contact.css',
    titre: 'Contact'
  });
});

module.exports = router;
