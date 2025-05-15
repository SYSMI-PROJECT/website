const express = require('express');
const router = express.Router();
const db = require('../database');
const UsersData = require('../middleware/UsersData');

router.get('/profil/:id', UsersData, async (req, res) => {
  if (!req.userData) {
    console.log('⚠️ L\'utilisateur n\'est pas connecté, redirection vers la page d\'erreur');
    return res.render('error', { message: 'Vous devez être connecté pour voir cette page.' });
  }

  const userId = req.params.id || req.userData.id;
  console.log('Récupération du profil de l\'utilisateur avec ID:', userId);

  try {
    const [userRes] = await db.execute(`SELECT * FROM utilisateur WHERE id = ?`, [userId]);

    if (userRes.length === 0) {
      return res.status(404).send('User not found.');
    }

    const user = userRes[0];
    const bio = user.bio || "Bio is empty";

    const [amisRes] = await db.execute(`SELECT u.id, u.prenom, p.image_content
                                       FROM utilisateur u
                                       JOIN relation r ON (r.demandeur = u.id OR r.receveur = u.id)
                                       LEFT JOIN photos_de_profil p ON u.id = p.user_id
                                       WHERE (r.demandeur = ? OR r.receveur = ?)
                                         AND r.statut = 1
                                         AND u.id != ?`,
      [user.id, user.id, user.id]
    );

    const [imgRes] = await db.execute(`SELECT image_content FROM photos_de_profil WHERE user_id = ?`, [user.id]);

    res.render('miscellaneous/profil', {
      titre: user.id === req.userData.id ? 'Mon profil' : 'Profil public',
      prenom: user.prenom,
      nom: user.nom,
      bio: user.bio,
      userData: req.userData,
      role: user.role,
      amis: amisRes,
      userData: user,
      image_content: imgRes[0] ? Buffer.from(imgRes[0].image_content).toString('base64') : null
    });
  } catch (err) {
    console.error('Error in /profil/:id :', err);
    res.status(500).send('Server error.');
  }
});

router.get('/users', UsersData, async (req, res) => {
  const searchKeyword = req.query.search || '';
  const like = `%${searchKeyword}%`;

  if (!req.userData) {
    console.log('⚠️ req.userData is empty on /users');
    return res.render('error', { message: 'Vous devez être connecté pour voir cette page.' });
  }

  const userId = req.userData.id;

  try {
    const [usersRes] = await db.execute(`SELECT u.id, u.prenom, u.nom, p.image_content, u.role, u.bio
                                         FROM utilisateur u
                                         LEFT JOIN photos_de_profil p ON u.id = p.user_id
                                         WHERE u.prenom LIKE ? AND u.id != ?
                                         ORDER BY u.prenom`, [like, userId]
    );

    res.render('miscellaneous/users', {
      cssFile: '/src/css/about.css',
      titre: 'Liste des utilisateurs',
      usersData: usersRes,
      searchKeyword
    });
  } catch (err) {
    console.error('Error in /users :', err);
    res.status(500).send('Server error.');
  }
});

router.get('/settings/securite', UsersData, async (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: 'Vous devez être connecté pour voir cette page.' });
  }

  const userId = req.userData.id;

  try {
    const [settingsRes] = await db.execute(`
      SELECT visibility, visibility_posts, notifications, two_factor_auth, dark_mode
      FROM user_settings WHERE user_id = ?`, [userId]);

    const userSettings = settingsRes.length ? settingsRes[0] : {}; 

    res.render('miscellaneous/user/settings/securite', {
      user_settings: userSettings, 
      cssFile: '/src/css/theme.css',
      titre: 'Sécurité'
    });
  } catch (err) {
    console.error('Erreur dans /settings/securite :', err);
    res.status(500).send('Server error.');
  }
});

router.post('/settings/securite', UsersData, async (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: 'Vous devez être connecté pour voir cette page.' });
  }

  const userId = req.userData.id;
  const { visibility, visibility_posts, notifications, two_factor_auth, dark_mode } = req.body;

  try {
    await updateUserSettings(userId, {
      visibility,
      visibility_posts,
      notifications,
      two_factor_auth,
      dark_mode
    });

    res.redirect('/settings/securite');
  } catch (err) {
    console.error('Erreur lors de la mise à jour des paramètres de sécurité :', err);
    res.status(500).send('Server error.');
  }
});

router.get('/settings/theme', UsersData, (req, res) => {
  if (!req.userData) {
    console.log('⚠️ req.userData is empty on /theme');
    return res.redirect('/login');
  }

  res.render('miscellaneous/settings/theme', {
    cssFile: '/src/css/theme.css',
    titre: 'Thème'
  });
});

router.get('/minigames', async (req, res) => {
  try {
    const gamesRes = [
      { nom: 'Ping Pong', description: 'Un jeu classique de ping-pong.', image: 'https://s3.amazonaws.com/gs.apps.screenshots/00000138-c4eb-cab1-9637-f6fcc971d29c.png', category: 'rétro' },
      { nom: 'Mario Kart', description: 'Course folle avec Mario et ses amis.', image: 'https://some-url.com/mario-kart.png', category: 'racing' },
      { nom: 'Tetris', description: 'Le jeu classique de puzzle.', image: 'https://some-url.com/tetris.png', category: 'puzzle' }
    ];    

    res.render('miscellaneous/minigames', {
      cssFile: '/src/css/politique.css',
      titre: 'Mini-jeux',
      games: gamesRes
    });
  } catch (err) {
    console.error('Error in /minigames :', err);
    res.status(500).send('Server error.');
  }
});

router.get('/games/:category/:game', UsersData, (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: 'Vous devez être connecté pour jouer à ce jeu.' });
  }

  const { category, game } = req.params;

  const cheminTemplate = `miscellaneous/games/${category}/${game}`;

  res.render(cheminTemplate, {
    cssFile: `/src/css/games/${category}/${game}.css`,
    titre: `${game.charAt(0).toUpperCase() + game.slice(1)} (${category})`
  });
});

router.get('/settings', (req, res) => {
  res.render('miscellaneous/settings', {
    cssFile: '/src/css/contact.css',
    titre: 'Contact'
  });
});

router.get('/boutique', UsersData, async (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: 'Vous devez être connecté pour accéder à la boutique.' });
  }

  try {
    res.render('miscellaneous/boutique', {
      user: req.userData,
      produits: req.produitsData
    });
  } catch (error) {
    console.error("Erreur lors de l'affichage de la boutique:", error);
    res.status(500).send("Server error.");
  }
});

router.get('/dashboard', UsersData, (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: 'Vous devez être connecté pour accéder au tableau de bord.' });
  }

  res.render('miscellaneous/dashboard', {
    titre: "Tableau de bord",
    cssFile: "/src/css/staff.css",
    user: req.userData
  });
});

router.get('/post', UsersData, async (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: 'Vous devez être connecté pour voir cette page.' });
  }

  const user_id = req.userData.id;
  const filterUserId = req.query.id || null;
  const publicationId = req.query.publication_id || null;

  try {
    let publications = [];

    if (!filterUserId) {
      [publications] = await db.execute(`
        SELECT 
          p.*, 
          u.prenom, u.nom, p.avatar, u.statut 
        FROM publications p
        JOIN utilisateur u ON p.user_id = u.id
        ORDER BY p.date_creation DESC
      `);
    } else {
      [publications] = await db.execute(`
        SELECT 
          p.*, 
          u.prenom, u.nom, u.avatar, u.statut 
        FROM publications p
        JOIN utilisateur u ON p.user_id = u.id
        WHERE p.user_id = ?
        ORDER BY p.date_creation DESC
      `, [filterUserId]);
    }

    res.render('miscellaneous/post', {
      publications,
      user_id,
      userData: req.userData
    });
  } catch (err) {
    console.error('Error in /post :', err);
    res.status(500).send('Server error');
  }
});

router.get('/add_pdp', (req, res) => {
  res.render('miscellaneous/forms/add_pdp', {
    cssFile: '/src/css/contact.css',
    titre: 'Contact'
  });
});

router.get('/pdp_editor', (req, res) => {
  res.render('miscellaneous/forms/pdp_editor', {
    cssFile: '/src/css/contact.css',
    titre: 'Contact'
  });
});

module.exports = router;