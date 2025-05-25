const path = require('path');
const fs = require('fs');
const db = require('../database');

module.exports = async function getUserProfileImage(req, res, next) {
  if (!req.session || !req.session.user_id) {
    req.userImage = null;
    return next();
  }

  const userId = req.session.user_id;
  let connection;

  try {
    connection = await db.getConnection();

    // Récupérer le nom du fichier image de la colonne 'photo_profil'
    const [result] = await connection.execute(
      'SELECT photo_profil FROM utilisateur WHERE id = ?',
      [userId]
    );

    if (result.length > 0 && result[0].photo_profil) {
      const imagePath = path.join(__dirname, '../public/uploads', result[0].photo_profil);

      if (fs.existsSync(imagePath)) {
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
  } finally {
    if (connection) connection.release(); // ✅ Libération sécurisée de la connexion
  }

  next();
};
