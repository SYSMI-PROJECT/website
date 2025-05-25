// routes/api/coin.js
const express = require('express');
const router = express.Router();
const db = require('../../database');
const UsersData = require('../../middleware/UsersData');

router.post('/update', UsersData, async (req, res) => {
  const userId = req.userData?.id;
  let { amount } = req.body;

  if (!userId) return res.status(401).json({ error: 'Non connecté' });

  amount = Number(amount);
  if (!Number.isFinite(amount)) {
    return res.status(400).json({ error: '"amount" doit être un nombre valide' });
  }

  amount = Math.round(amount);

  try {
    const [[user]] = await db.query('SELECT etoile FROM utilisateur WHERE id = ?', [userId]);
    if (!user) return res.status(404).json({ error: 'Utilisateur introuvable' });

    const newEtoile = user.etoile + amount;
    if (newEtoile < 0) {
      return res.status(400).json({ error: 'Solde insuffisant' });
    }

    await db.query('UPDATE utilisateur SET etoile = ? WHERE id = ?', [newEtoile, userId]);

    console.log(`[SCOIN] Utilisateur ${userId} : ${amount > 0 ? '+' : ''}${amount} ➜ nouveau solde : ${newEtoile}`);

    res.json({ success: true, newEtoile });
  } catch (err) {
    console.error('[API] Erreur update S-Coin :', err);
    res.status(500).json({ error: 'Erreur serveur' });
  }
});

module.exports = router;
