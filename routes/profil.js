const express = require('express');
const router = express.Router();
const UserData = require('../middleware/UsersData'); // <- renommé ici
const db = require('../database');

// Route du profil (public ou personnel)
router.get('/:id?', UserData, async (req, res) => {
  const sessionUserId = req.session.userId;
  const paramUserId = req.params.id;

  // Soit l’ID passé en paramètre, soit l’ID de session (profil personnel)
  const userID = paramUserId || sessionUserId;

  if (!userID) {
    return res.status(401).send("Non autorisé : utilisateur non connecté.");
  }

  try {
    const [userRows] = await db.execute(`
      SELECT u.id, u.nom, u.prenom, u.date_inscription, u.bio, u.email,
             u.lien_tiktok, u.lien_youtube, u.lien_snapchat, u.lien_instagram,
             u.lien_discord, u.lien_twitch, u.verified, u.role, u.statut,
             p.image_content
      FROM utilisateur u
      LEFT JOIN photos_de_profil p ON u.id = p.user_id
      WHERE u.id = ?
    `, [userID]);

    if (userRows.length === 0) {
      return res.status(404).send("Utilisateur non trouvé.");
    }

    const user = userRows[0];

    const imageBase64 = user.image_content
      ? Buffer.from(user.image_content).toString('base64')
      : null;

    res.render('profil', {
      user,
      imageBase64,
      cssFile: '/src/css/profil.css',
      titre: `Profil de ${user.prenom}`,
      isUserLoggedIn: !!req.session.userId,
      userData: req.userData // tu peux utiliser ça dans la vue si besoin
    });

  } catch (err) {
    console.error(err);
    res.status(500).send("Erreur serveur.");
  }
});

module.exports = router;
