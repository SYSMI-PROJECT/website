const express = require('express');
const router = express.Router();
const pool = require('../../database');
const UsersData = require('../../middleware/UsersData');

const languesAutorisees = ['fr', 'en', 'es', 'de'];

// Route GET : affichage de la page de sélection de langue
router.get('/', UsersData, async (req, res) => {
    try {
        if (!req.userData || !req.userData.id) {
            return res.redirect('/login');
        }

        const userId = req.userData.id;
        const [rows] = await pool.execute('SELECT langue FROM utilisateur WHERE id = ?', [userId]);
        
        const langue = rows[0]?.langue || 'fr';
        req.userData.langue = langue;

        res.render('miscellaneous/settings/langue', {
            userData: req.userData,
            cssFile: res.locals.cssFile
        });
    } catch (err) {
        console.error('Erreur lors du chargement de la page de langue:', err);
        return res.status(500).send('Erreur serveur.');
    }
});

// Route POST : mise à jour de la langue
router.post('/', UsersData, async (req, res) => {
    try {
        if (!req.userData || !req.userData.id) {
            return res.status(403).send('Utilisateur non connecté.');
        }

        const langue = req.body.langue;

        if (!languesAutorisees.includes(langue)) {
            return res.status(400).send('Langue invalide.');
        }

        const utilisateur_id = req.userData.id;
        const sql = 'UPDATE utilisateur SET langue = ? WHERE id = ?';
        await pool.execute(sql, [langue, utilisateur_id]);

        req.userData.langue = langue;

        // Redirection après mise à jour
        return res.redirect('/');
    } catch (err) {
        console.error('Erreur lors de la mise à jour de la langue:', err);
        return res.status(500).send('Erreur serveur.');
    }
});

module.exports = router;
