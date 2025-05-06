const db = require('../database');

module.exports = async function getUserProfileData(req, res, next) {
  if (!req.session || !req.session.user_id) {
    req.userData = null;
    return next();
  }

  const loggedInUserID = req.session.user_id;

  try {
    // Récupérer les informations de l'utilisateur connecté
    const [userRows] = await db.execute(`
      SELECT u.id, u.nom, u.prenom, u.date_inscription, u.bio, u.email,
             u.lien_tiktok, u.lien_youtube, u.lien_snapchat, u.lien_instagram,
             u.lien_discord, u.lien_twitch, u.verified, u.role, u.statut,
             p.image_content
      FROM utilisateur u
      LEFT JOIN photos_de_profil p ON u.id = p.user_id
      WHERE u.id = ?
    `, [loggedInUserID]);

    if (userRows.length === 0) {
      req.userData = null;
      return next();
    }

    const user = userRows[0];

    // Récupérer le nombre d'amis de l'utilisateur connecté
    const [friendsRows] = await db.execute(`
      SELECT 
        (SELECT COUNT(*) FROM relation WHERE demandeur = ? AND statut = 1) + 
        (SELECT COUNT(*) FROM relation WHERE receveur = ? AND statut = 1) AS nombre_amis
    `, [loggedInUserID, loggedInUserID]);

    const nombreAmis = friendsRows[0].nombre_amis;

    // Récupérer le statut de la relation avec l'utilisateur connecté
    const [relationRows] = await db.execute(`
      SELECT statut FROM relation
      WHERE (demandeur = ? AND receveur = ?) OR (demandeur = ? AND receveur = ?)
    `, [loggedInUserID, loggedInUserID, loggedInUserID, loggedInUserID]);

    let relationStatus = null;
    if (relationRows.length > 0) {
      relationStatus = relationRows[0].statut;
    }

    // Mettre les données dans req.userData
    req.userData = {
      ...user,
      nombreAmis,
      relationStatus
    };

    next();
  } catch (error) {
    console.error('Erreur lors de la récupération des données de l\'utilisateur connecté:', error);
    req.userData = null;
    next();
  }
};
