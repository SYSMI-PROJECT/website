const express = require('express');
const router = express.Router();
const db = require('../../database');
const UsersData = require('../../middleware/UsersData');

router.post('/', UsersData, async (req, res) => {
  const userId = req.userData?.id;
  const { publication_id, action } = req.body;

  if (!userId) {
    return res.status(401).json({ success: false, message: 'Non connecté' });
  }

  if (!publication_id || !['like', 'unlike'].includes(action)) {
    return res.status(400).json({ success: false, message: 'Données manquantes ou action invalide' });
  }

  try {
    if (action === 'like') {
      const [[{ count }]] = await db.query(
        'SELECT COUNT(*) AS count FROM likes WHERE publication_id = ? AND user_id = ?',
        [publication_id, userId]
      );
      if (count === 0) {
        await db.query(
          'INSERT INTO likes (publication_id, user_id) VALUES (?, ?)',
          [publication_id, userId]
        );
      }
    } else if (action === 'unlike') {
      await db.query(
        'DELETE FROM likes WHERE publication_id = ? AND user_id = ?',
        [publication_id, userId]
      );
    }

    const [[{ totalLikes }]] = await db.query(
      'SELECT COUNT(*) AS totalLikes FROM likes WHERE publication_id = ?',
      [publication_id]
    );

    res.json({ success: true, likes: totalLikes });
  } catch (err) {
    console.error('[LIKE API] Erreur :', err);
    res.status(500).json({ success: false, message: 'Erreur serveur' });
  }
});

module.exports = router;
