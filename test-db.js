const db = require('./database');

(async () => {
  try {
    const conn = await db.getConnection();

    // Sélectionner 5 utilisateurs au hasard
    const [rows] = await conn.query('SELECT * FROM utilisateur ORDER BY RAND() LIMIT 5');

    if (rows.length > 0) {
      console.log(`Connexion réussie ✅\nExemples de lignes dans la table utilisateur :`);
      rows.forEach((row, index) => {
        console.log(`\nUtilisateur ${index + 1}:`);
        console.log(row);
      });
    } else {
      console.log('Aucun utilisateur trouvé dans la table.');
    }

    conn.release();
  } catch (err) {
    console.error('Erreur de connexion ❌', err.message);
  }
})();