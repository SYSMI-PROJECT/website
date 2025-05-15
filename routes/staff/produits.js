const express = require('express');
const router = express.Router();
const multer = require('multer');
const db = require('../../database');

// Middleware de sécurité pour vérifier le rôle staff
router.use((req, res, next) => {
    if (!req.userData || req.userData.role !== 'staff') {
        return res.redirect('/');
    }
    next();
});

// Configuration de multer (stockage en mémoire)
const storage = multer.memoryStorage();
const upload = multer({ storage: storage });

/**
 * GET /staff/produits
 * Affiche la liste des produits
 */
router.get('/', async (req, res) => {
    try {
        const [produits] = await db.query("SELECT * FROM produits");

        const produitsFormatés = produits.map(p => ({
            ...p,
            prix: parseFloat(p.prix)
        }));

        res.render('staff/gestion_produits', { produits: produitsFormatés });
    } catch (err) {
        console.error(err);
        res.status(500).send("Erreur lors du chargement des produits.");
    }
});

/**
 * GET /staff/produits/ajouter
 * Affiche le formulaire d’ajout
 */
router.get('/ajouter', (req, res) => {
    res.render('staff/ajouter_produit', { message: '' });
});

/**
 * POST /staff/produits/ajouter
 * Enregistre un nouveau produit
 */
router.post('/ajouter', upload.fields([
    { name: 'image', maxCount: 1 },
    { name: 'script', maxCount: 1 }
]), async (req, res) => {
    const { nom, description, prix } = req.body;
    const imageFile = req.files?.image?.[0];
    const scriptFile = req.files?.script?.[0];
    let message = '';

    if (!nom || !description || !prix || !imageFile || !scriptFile) {
        return res.render('staff/ajouter_produit', {
            message: 'Veuillez remplir tous les champs correctement.'
        });
    }

    const prixFloat = parseFloat(prix);
    if (isNaN(prixFloat)) {
        return res.render('staff/ajouter_produit', {
            message: 'Le prix doit être un nombre valide.'
        });
    }

    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(imageFile.mimetype)) {
        return res.render('staff/ajouter_produit', {
            message: 'Format d’image non supporté (JPEG, PNG, GIF uniquement).'
        });
    }

    try {
        await db.query(
            `INSERT INTO produits (nom, description, prix, image, script_content)
             VALUES (?, ?, ?, ?, ?)`,
            [nom, description, prixFloat, imageFile.buffer, scriptFile.buffer]
        );
        message = 'Produit ajouté avec succès !';
    } catch (err) {
        console.error(err);
        message = "Erreur lors de l'enregistrement du produit.";
    }

    res.render('staff/ajouter_produit', { message });
});

/**
 * GET /staff/produits/remove/:id
 * Supprime un produit
 */
router.get('/remove/:id', async (req, res) => {
    const produitId = parseInt(req.params.id);
    if (isNaN(produitId)) return res.redirect('/staff/produits');

    try {
        await db.query("DELETE FROM produits WHERE id = ?", [produitId]);
        res.redirect('/staff/produits');
    } catch (err) {
        console.error(err);
        res.status(500).send("Erreur lors de la suppression du produit.");
    }
});

module.exports = router;
