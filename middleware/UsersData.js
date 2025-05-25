const db = require('../database');

module.exports = async (req, res, next) => {
    if (!req.session || !req.session.userId) {
        req.userData = null;
        return next();
    }

    const id = req.session.userId;

    if (req.userData && req.userData.id === id) {
        return next();
    }

    let connection;

    try {
        connection = await db.getConnection();

        const [rows] = await connection.execute(`
            SELECT 
                u.id, u.etoile, u.role, u.statut, u.prenom, u.nom, u.theme, 
                u.email, u.consol, u.bio, u.secret_code, u.xp, u.photo_profil,
                us.visibility, 
                u.lien_tiktok, u.lien_youtube, u.lien_snapchat, u.lien_instagram, 
                u.lien_discord, u.lien_twitch
            FROM utilisateur u
            LEFT JOIN user_settings us ON u.id = us.user_id
            WHERE u.id = ?`, 
            [id]
        );

        if (rows.length === 0) {
            req.userData = null;
            return next();
        }

        const user = rows[0];

        if (!user.secret_code) {
            const newSecretCode = Date.now().toString(36) + Math.random().toString(36).substring(2);
            await connection.execute(`UPDATE utilisateur SET secret_code = ? WHERE id = ?`, [newSecretCode, id]);
            user.secret_code = newSecretCode;
        }

        const visibilityStatus = user.visibility === 'private'
            ? 'private-status text-red-500'
            : 'public-status text-green-500';

        const [amisRows] = await connection.execute(`
            SELECT 
              (SELECT COUNT(*) FROM relation WHERE demandeur = ? AND statut = 1) + 
              (SELECT COUNT(*) FROM relation WHERE receveur = ? AND statut = 1) AS nombre_amis
        `, [id, id]);

        const nombreAmis = amisRows[0]?.nombre_amis || 0;

        req.userData = {
            id: user.id,
            etoile: user.etoile,
            role: user.role,
            statut: user.statut || 'actif',
            prenom: user.prenom,
            nom: user.nom,
            bio: user.bio,
            email: user.email,
            secret_code: user.secret_code,
            consol: user.consol,
            xp: user.xp,
            theme: user.theme || 'blanc',
            visibility: user.visibility || 'private',
            visibilityStatus: visibilityStatus,
            tiktok: user.lien_tiktok,
            youtube: user.lien_youtube,
            snapchat: user.lien_snapchat,
            instagram: user.lien_instagram,
            discord: user.lien_discord,
            twitch: user.lien_twitch,
            photo_profil: user.photo_profil,
            nombreAmis: nombreAmis
        };

        if (user.statut === 'banni') {
            return res.redirect('/Import/Error/Account_banned');
        }

        const [produits] = await connection.execute(`SELECT id, nom, description, prix, image FROM produits`);
        req.produitsData = produits;

    } catch (error) {
        console.error('Erreur dans UsersData :', error);
        return res.status(500).send('Erreur serveur');
    } finally {
        if (connection) connection.release();
    }

    next();
};
