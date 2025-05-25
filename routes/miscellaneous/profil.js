const express = require('express');
const router = express.Router();
const db = require('../../database');
const UsersData = require('../../middleware/UsersData');

// Page de profil
router.get('/profil/:id?', UsersData, async (req, res) => {
  if (!req.userData) {
    console.log("⚠️ L'utilisateur n'est pas connecté, redirection vers la page d'erreur");
    return res.render('error', { message: 'Vous devez être connecté pour voir cette page.' });
  }

  const userId = req.params.id || req.userData.id;
  console.log("🔍 Récupération du profil de l'utilisateur avec ID:", userId);

  try {
    const [userRes] = await db.execute(`SELECT * FROM utilisateur WHERE id = ?`, [userId]);

    if (userRes.length === 0) {
      return res.status(404).render('error', { message: 'Utilisateur non trouvé.' });
    }

    const user = userRes[0];
    const bio = user.bio || 'Bio non définie';

    // Récupération des amis
    const [amisRes] = await db.execute(`
      SELECT u.id, u.prenom, u.photo_profil
      FROM utilisateur u
      JOIN relation r ON (r.demandeur = u.id OR r.receveur = u.id)
      WHERE (r.demandeur = ? OR r.receveur = ?)
        AND r.statut = 1
        AND u.id != ?
    `, [user.id, user.id, user.id]);

    const nombreAmis = amisRes.length;

    // Si l'utilisateur consulte son propre profil
    let nbDemandes = 0;
    const isMyProfile = user.id === req.userData.id;

    if (isMyProfile) {
      const [resDemandes] = await db.execute(`
        SELECT COUNT(*) AS nbDemandes
        FROM relation
        WHERE receveur = ? AND statut = 0
      `, [req.userData.id]);

      nbDemandes = resDemandes[0]?.nbDemandes || 0;
    }

    // Rendu
    res.render('miscellaneous/profil', {
      titre: isMyProfile ? 'Mon profil' : 'Profil public',
      prenom: user.prenom,
      nom: user.nom,
      bio,
      role: user.role,
      amis: amisRes,
      user,
      nombreAmis,
      nbDemandes: isMyProfile ? nbDemandes : null,
      userData: {
        ...req.userData,
        photo_profil: user.photo_profil
      }
    });

  } catch (err) {
    console.error('❌ Erreur dans /profil/:id :', err);
    res.status(500).send('Erreur serveur');
  }
});

module.exports = router;
