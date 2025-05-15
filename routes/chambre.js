const express = require('express');
const router = express.Router();
const db = require('../database');

// GET : affiche la chambre
router.get('/', async (req, res) => {
  const userId = req.userData?.id;
  if (!userId) return res.redirect('/login');

  const conn = await db.getConnection();
  const [rows] = await conn.execute(
    'SELECT data FROM chambres WHERE user_id = ?', [userId]
  );
  const chambreData = rows[0]?.data ? JSON.parse(rows[0].data) : {};

  res.render('chambre', { chambreData });
});

// POST : sauvegarde la position des objets
router.post('/save', async (req, res) => {
  const userId = req.userData?.id;
  if (!userId) return res.status(401).json({ error: 'Non autorisé' });

  const data = JSON.stringify(req.body);

  const conn = await db.getConnection();
  await conn.execute(`
    INSERT INTO chambres (user_id, data)
    VALUES (?, ?)
    ON DUPLICATE KEY UPDATE data = VALUES(data)
  `, [userId, data]);

  res.json({ success: true });
});

module.exports = router;
