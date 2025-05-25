const express = require('express');
const router = express.Router();
const db = require('../../database');
const UsersData = require('../../middleware/UsersData');

// 🔹 Afficher la conversation avec un utilisateur
router.get('/message/:id', UsersData, async (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: "Vous devez être connecté pour accéder à cette page." });
  }

  const receiverID = parseInt(req.params.id, 10);
  const loggedInUserID = req.userData.id;

  if (!receiverID || receiverID === loggedInUserID) {
    return res.render('error', { message: "Destinataire invalide ou identique à l'expéditeur." });
  }

  try {
    const [receiverRes] = await db.execute(`
      SELECT nom, prenom, p.image_content 
      FROM utilisateur u 
      LEFT JOIN photos_de_profil p ON u.id = p.user_id 
      WHERE u.id = ?
    `, [receiverID]);

    if (receiverRes.length === 0) {
      return res.status(404).render('error', { message: "Utilisateur introuvable." });
    }

    const receiver = receiverRes[0];

    const [messagesRes] = await db.execute(`
SELECT m.id, m.contenu, m.date_envoi, 
       u.nom AS expediteur_nom, u.prenom AS expediteur_prenom, 
       u.photo_profil AS expediteur_avatar,
       m.expediteur_id
FROM messages_prives m
INNER JOIN utilisateur u ON m.expediteur_id = u.id
WHERE (m.expediteur_id = ? AND m.destinataire_id = ?) 
   OR (m.expediteur_id = ? AND m.destinataire_id = ?)
ORDER BY m.date_envoi ASC
    `, [loggedInUserID, receiverID, receiverID, loggedInUserID]);

    res.render('miscellaneous/message', {
      receiver,
      messages: messagesRes,
      receiverID,
      loggedInUserID,
      userData: req.userData
    });

  } catch (err) {
    console.error('❌ Erreur GET /message/:id :', err);
    res.status(500).render('error', { message: 'Erreur serveur.' });
  }
});

// 🔹 Envoyer un message
router.post('/message/:id', UsersData, async (req, res) => {
  const receiverID = parseInt(req.params.id, 10);
  const senderID = req.userData.id;
  const { contenu } = req.body;

  if (!contenu || contenu.trim() === '') {
    return res.status(400).json({ success: false, message: "Le message est vide." });
  }

  try {
    await db.execute(`
      INSERT INTO messages_prives (expediteur_id, destinataire_id, contenu, date_envoi)
      VALUES (?, ?, ?, NOW())
    `, [senderID, receiverID, contenu]);

    res.redirect(`/message/${receiverID}`);
  } catch (err) {
    console.error("❌ Erreur POST /message/:id :", err);
    res.status(500).render('error', { message: "Erreur lors de l'envoi du message." });
  }
});

// 🔹 Supprimer un message
router.delete('/message/:id/delete/:messageId', UsersData, async (req, res) => {
  const userID = req.userData.id;
  const messageId = parseInt(req.params.messageId, 10);

  try {
    const [check] = await db.execute(`
      SELECT * FROM messages_prives 
      WHERE id = ? AND expediteur_id = ?
    `, [messageId, userID]);

    if (check.length === 0) {
      return res.status(403).render('error', { message: "Suppression non autorisée." });
    }

    await db.execute(`DELETE FROM messages_prives WHERE id = ?`, [messageId]);
    res.redirect(`/message/${req.params.id}`);
  } catch (err) {
    console.error("❌ Erreur DELETE /message/:id/delete/:messageId :", err);
    res.status(500).render('error', { message: "Erreur lors de la suppression." });
  }
});

module.exports = router;
