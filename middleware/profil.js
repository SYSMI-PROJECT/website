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
    const [result] = await db.execute(
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
    console.error('Error retrieving profile picture :', error);
    req.userImage = null;
  }

  next();
};
