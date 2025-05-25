const db = require('../database');

module.exports = async function(req, res, next) {
    if (req.userData && req.userData.id) {
        let connection;
        try {
            connection = await db.getConnection();

            const [rows] = await connection.execute(
                'SELECT langue FROM utilisateur WHERE id = ?',
                [req.userData.id]
            );

            if (rows.length > 0 && rows[0].langue) {
                res.locals.userLanguage = rows[0].langue;
                req.userData.langue = rows[0].langue;
            } else {
                res.locals.userLanguage = 'fr';
                req.userData.langue = 'fr';
            }
        } catch (error) {
            console.error('Erreur lors de la récupération de la langue:', error);
            res.locals.userLanguage = 'fr';
            req.userData.langue = 'fr';
        } finally {
            if (connection) connection.release()
        }
    } else {
        res.locals.userLanguage = 'fr';
    }

    next();
};
