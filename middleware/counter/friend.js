const db = require('../../database');

// Fonction pour obtenir le nombre de demandes
async function getNbDemandes(userId) {
  let nbDemandes = 0;

  if (!userId) {
    return 0; // Utilisateur non connecté
  }

  try {
    const [rows] = await db.execute(`
      SELECT COUNT(*) AS nb_demandes
      FROM relation r
      INNER JOIN utilisateur u ON r.demandeur = u.id
      WHERE r.receveur = ? AND r.statut = 0
    `, [userId]);

    if (rows.length > 0) {
      nbDemandes = rows[0].nb_demandes;
    }

  } catch (err) {
    console.error("Erreur lors du comptage des demandes d'amis :", err);
    return 0;
  }

  return nbDemandes;
}

module.exports = async function(req, res, next) {
  if (req.userData && req.userData.id) {
    const nbDemandes = await getNbDemandes(req.userData.id);
    req.nbDemandes = nbDemandes;
  } else {
    req.nbDemandes = 0;
  }
  
  next();
};
