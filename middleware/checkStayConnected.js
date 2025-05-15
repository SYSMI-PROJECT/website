const db = require('../database');

module.exports = async (req, res, next) => {
  if (!req.session.user_id && req.cookies.stay_connected) {
    try {
      const conn = await db.getConnection();
      const [rows] = await conn.execute(
        'SELECT u.* FROM user_tokens t JOIN utilisateur u ON u.id = t.user_id WHERE t.token = ? AND t.expiry_date > NOW()',
        [req.cookies.stay_connected]
      );

      if (rows.length > 0) {
        req.session.user_id = rows[0].id;
        req.session.prenom = rows[0].prenom;
      }
    } catch (err) {
      console.error('Erreur middleware stay_connected:', err);
    }
  }

  next();
};
