const express = require('express');
const db = require('../../database');
const UsersData = require('../../middleware/UsersData');
const router = express.Router();

router.get('/settings', (req, res) => {
  res.render('miscellaneous/settings', {
    cssFile: '/src/css/contact.css',
    titre: 'Contact'
  });
});


router.get('/settings/securite', UsersData, async (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: 'Vous devez être connecté pour voir cette page.' });
  }
  const userId = req.userData.id;

  try {
    const [settingsRes] = await db.execute(`
      SELECT visibility, visibility_posts, notifications, two_factor_auth, dark_mode
      FROM user_settings WHERE user_id = ?
    `, [userId]);

    const userSettings = settingsRes.length ? settingsRes[0] : {
      visibility: 'public',
      visibility_posts: 'public',
      notifications: 'on',
      two_factor_auth: 0,
      dark_mode: 0
    };

    res.render('miscellaneous/settings/securite', {
      user_settings: userSettings,
      cssFile: '/src/css/theme.css',
      titre: 'Sécurité'
    });
  } catch (err) {
    console.error('❌ Erreur dans /settings/securite :', err);
    res.status(500).send('Erreur serveur');
  }
});

router.post('/settings/securite', UsersData, async (req, res) => {
  if (!req.userData) {
    return res.render('error', { message: 'Vous devez être connecté pour voir cette page.' });
  }

  const userId = req.userData.id;
  const {
    visibility = 'public',
    visibility_posts = 'public',
    notifications = 'on',
    two_factor_auth = 0,
    dark_mode = 0
  } = req.body;

  try {
    await updateUserSettings(userId, {
      visibility,
      visibility_posts,
      notifications,
      two_factor_auth: !!Number(two_factor_auth),
      dark_mode: !!Number(dark_mode)
    });

    res.redirect('/settings/securite');
  } catch (err) {
    console.error('❌ Erreur lors de la mise à jour des paramètres de sécurité :', err);
    res.status(500).send('Erreur serveur');
  }
});

router.get('/settings/theme', UsersData, (req, res) => {
  if (!req.userData) {
    console.warn('⚠️ Accès non autorisé à /settings/theme – utilisateur non connecté');
    return res.redirect('/login');
  }

  res.render('miscellaneous/settings/theme', {
    cssFile: '/src/css/theme.css',
    titre: 'Thème'
  });
});

module.exports = router;