const express = require('express');
const path = require('path');
const router = express.Router();

// Fonction pour charger les fichiers avec gestion des erreurs
function safeRequire(routePath) {
    try {
        return require(routePath);
    } catch (error) {
        console.error(`Erreur lors du chargement de la route ${routePath}: ${error.stack}`);
        const emptyRouter = express.Router();
        emptyRouter.use((req, res) => {
            res.status(500).send('Cette fonctionnalité est temporairement indisponible.');
        });
        return emptyRouter;
    }
}

// Chargement des routes
const deletePdp = safeRequire('./delete_pdp');
const uploadPdp = safeRequire('./upload_pdp');
const uploadPost = safeRequire('./upload_post');
const editPdp = safeRequire('./edit_pdp');
const visibility = safeRequire('./visibility');
const theme = safeRequire('./theme');
const langue = safeRequire('./langue');
const feedback = safeRequire('./feedback');

// Utilisation de chemins différents pour chaque sous-route
router.use('/delete', deletePdp);
router.use('/upload/pdp', uploadPdp);
router.use('/upload/post', uploadPost);
router.use('/editor', editPdp);
router.use('/visibility', visibility);
router.use('/theme', theme);
router.use('/langue', langue);
router.use('/feedback', feedback);

module.exports = router;
