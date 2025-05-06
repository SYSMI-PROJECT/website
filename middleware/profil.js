const path = require('path');
const fs = require('fs');
const db = require('../database');

module.exports = async function getUserProfileImage(req, res, next) {
  if (!req.session || !req.session.user_id) {
    req.userImage = null;
    return next();
  }

  const userId = req.session.user_id;

  try {
    // Récupérer le nom du fichier image de la colonne 'photo_profil' dans la table 'utilisateur'
    const [result] = await db.execute(
      'SELECT photo_profil FROM utilisateur WHERE id = ?',
      [userId]
    );

    if (result.length > 0 && result[0].photo_profil) {
      // Construire le chemin absolu du fichier d'image dans le dossier /uploads
      const imagePath = path.join(__dirname, '../public/uploads', result[0].photo_profil);

      // Vérifier si le fichier image existe dans le dossier
      if (fs.existsSync(imagePath)) {
        // Si l'image existe, on passe le chemin relatif pour l'affichage
        req.userImage = `/uploads/${result[0].photo_profil}`;
      } else {
        req.userImage = null;
      }
    } else {
      req.userImage = null;
    }
  } catch (error) {
    console.error('Erreur lors de la récupération de l’image de profil :', error);
    req.userImage = null;
  }

  next();
};
