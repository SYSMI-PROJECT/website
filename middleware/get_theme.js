const db = require('../database');

module.exports = async (req, res, next) => {
  const defaultTheme = 'noir';
  let theme = defaultTheme;
  let conn;

  // Mapping thème → chemin du fichier CSS
  const themeToCssFile = {
    noir: '/src/css/index.css',
    blanc: '/src/css/Theme/white.css',
    sombre: '/src/css/Theme/dark.css',
    galaxie: '/src/css/Theme/galaxy.css',
    fuchsia: '/src/css/Theme/fuchsia.css'
    // Ajoute d'autres thèmes ici si nécessaire
  };

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
    console.error('Erreur lors de la récupération du thème :', error);
  } finally {
    if (conn) conn.release();
  }

  // Si le thème n'existe pas dans le mapping, utilise celui par défaut
  res.locals.cssFile = themeToCssFile[theme] || themeToCssFile[defaultTheme];
  res.locals.theme = theme;

  next();
};
