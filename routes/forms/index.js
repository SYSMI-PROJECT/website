const express = require('express');
const router = express.Router();

const deletePdp = require('./delete_pdp');
const uploadPdp = require('./upload_pdp');
const editPdp = require('./edit_pdp');
const visibility = require('./visibility');
const theme = require('./theme');


// Utilisation de chemins différents pour chaque route
router.use('/delete', deletePdp);
router.use('/upload', uploadPdp);
router.use('/editor', editPdp);
router.use('/visibility', visibility);
router.use('/theme', theme);

module.exports = router;
