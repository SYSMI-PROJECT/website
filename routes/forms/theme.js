const express = require('express');
const router = express.Router();
const pool = require('../../database');
const UsersData = require('../../middleware/UsersData');

// Liste des thèmes autorisés
const themesAutorises = ['blanc', 'noir', 'sombre', 'bleu', 'rouge', 'vert'];

// Route POST pour modifier le thème
router.post('/', UsersData, async (req, res) => {
    try {
        if (!req.userData || !req.userData.id) {
            return res.status(403).send('Utilisateur non connecté.');
        }

        const theme = req.body.theme;

        // Vérifie que le thème fait partie de la liste autorisée
        if (!themesAutorises.includes(theme)) {
            return res.status(400).send('Thème invalide.');
        }

        const utilisateur_id = req.userData.id;

        const sql = 'UPDATE utilisateur SET theme = ? WHERE id = ?';
        await pool.execute(sql, [theme, utilisateur_id]);

        // Redirection directe vers la page d'accueil
        return res.redirect('/');
    } catch (err) {
        console.error('Erreur lors de la mise à jour du thème:', err);
        return res.status(500).send('Erreur serveur.');
    }
});

module.exports = router;
