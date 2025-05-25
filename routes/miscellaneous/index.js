const express = require('express');
const router = express.Router();

router.use('/', require('./profil'));
router.use('/', require('./users'));
router.use('/', require('./settings'));
router.use('/', require('./langue'));
router.use('/', require('./games'));
router.use('/', require('./store'));
router.use('/', require('./dashboard'));
router.use('/', require('./post'));
router.use('/', require('./forms'));
router.use('/camera', require('./camera'));
router.use('/', require('./about'));
router.use('/', require('./message'));

module.exports = router;
