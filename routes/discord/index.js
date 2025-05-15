const express = require('express');
const router = express.Router();
const pool = require('../../database'); // Connexion MySQL via pool

// Route GET : page SYSMI-ZONE avec rendu EJS
router.get('/SYSMI-ZONE', async (req, res) => {
    const isUserLoggedIn = req.session?.user_id != null;

    try {
        const [cards] = await pool.execute(
            'SELECT category, cardName, description, link, image FROM cartes'
        );

        res.render('discord/SYSMI-ZONE', {
            isUserLoggedIn,
            cards,
            error: null
        });
    } catch (err) {
        console.error('Erreur SQL :', err);
        res.render('discord/SYSMI-ZONE', {
            isUserLoggedIn,
            cards: [],
            error: 'Erreur lors du chargement des cartes.'
        });
    }
});

// Route GET : API JSON pour récupérer les données des cartes
router.get('/SYSMI-ZONE/data', async (req, res) => {
    try {
        const [rows] = await pool.execute(
            'SELECT category, cardName, description, link, image FROM cartes'
        );

        res.setHeader('Content-Type', 'application/json');
        res.status(200).json({
            success: true,
            cards: rows
        });
    } catch (error) {
        console.error('Erreur API JSON :', error);
        res.setHeader('Content-Type', 'application/json');
        res.status(500).json({
            success: false,
            message: error.message
        });
    }
});

// Route GET : afficher le formulaire d'ajout de carte
router.get('/SYSMI-ZONE/add', (req, res) => {
    res.render('discord/add'); // Affiche la vue de formulaire d'ajout de carte
});

// Route POST : Ajouter une nouvelle carte dans la base de données
router.post('/SYSMI-ZONE/add', async (req, res) => {
    const { cardName, category, description, link, image } = req.body;

    console.log('Données reçues:', req.body); // Vérifie les données reçues

    // Validation des champs
    if (!cardName || !category || !description || !link || !image) {
        console.log('Erreur validation: Tous les champs doivent être remplis.');
        return res.status(400).send("Tous les champs sont requis.");
    }

    try {
        console.log('Insertion dans la base de données');
        // Insertion dans la base de données
        await pool.execute(
            'INSERT INTO cartes (cardName, category, description, link, image) VALUES (?, ?, ?, ?, ?)',
            [cardName, category, description, link, image]
        );

        // Redirection après succès
        console.log('Carte ajoutée, redirection');
        res.redirect('/discord/SYSMI-ZONE'); // Redirige vers la page principale après l'ajout
    } catch (err) {
        console.error('Erreur lors de l\'ajout de la carte :', err);
        res.status(500).send('Erreur lors de l\'ajout de la carte.');
    }
});

module.exports = router;
