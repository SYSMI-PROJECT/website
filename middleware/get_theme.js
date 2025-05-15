const db = require('../database');

module.exports = async (req, res, next) => {
  const defaultTheme = 'blanc';
  let theme = defaultTheme;
  let conn;

  try {
    if (req.session.userId) {
      conn = await db.getConnection();
      const [rows] = await conn.execute(
        'SELECT theme FROM utilisateur WHERE id = ?',
        [req.session.userId]
      );

      if (rows.length > 0 && rows[0].theme) {
        theme = rows[0].theme;
      }
    }
  } catch (error) {
    console.error('Error retrieving theme:', error);
  } finally {
    if (conn) conn.release();
  }

  res.locals.cssFile = theme === 'noir'
    ? '/src/css/index.css'
    : '/src/css/Theme/white.css';

  res.locals.theme = theme;
  next();
};
