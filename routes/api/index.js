const express = require('express');
const router = express.Router();

// Importe toutes les sous-routes API
router.use('/like', require('./like'));      // POST /api/like
router.use('/coin', require('./coin'));      // POST /api/coin/update

module.exports = router;
