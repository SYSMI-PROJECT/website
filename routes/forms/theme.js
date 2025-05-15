const express = require('express');
const router = express.Router();
const pool = require('../../database');
const UsersData = require('../../middleware/UsersData');

const themesAutorises = ['blanc', 'noir', 'bleu', 'rouge', 'vert'];

router.post('/', UsersData, async (req, res) => {
    try {
        if (!req.userData || !req.userData.id) {
            return res.status(403).send('User not connected.');
        }

        const theme = req.body.theme;

        if (!themesAutorises.includes(theme)) {
            return res.status(400).send('Invalid theme.');
        }

        const utilisateur_id = req.userData.id;

        const sql = 'UPDATE utilisateur SET theme = ? WHERE id = ?';
        await pool.execute(sql, [theme, utilisateur_id]);

        return res.redirect('/');
    } catch (err) {
        console.error('Error updating theme:', err);
        return res.status(500).send('Server Error.');
    }
});

module.exports = router;
