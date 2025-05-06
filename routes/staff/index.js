const express = require('express');
const router = express.Router();

// Route principale /staff
router.get('/', (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.redirect('/');
  }
  
  // Redirige vers le dashboard situé dans miscellaneous
  res.redirect('/staff/dashboard');
});

// Route pour le dashboard
router.get('/dashboard', (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.redirect('/');
  }

  // Rendu de la vue du dashboard (exemple : dashboard.ejs)
  res.render('miscellaneous/dashboard', {
    user: req.userData,
    cssFile: '/src/css/dashboard.css'
  });
});

// Route pour la page des tâches
router.get('/tasks', (req, res) => {
  res.render('staff/tasks', {
    cssFile: '/src/css/contact.css',
    titre: 'Contact'
  });
});

// Route pour les paramètres du staff
router.get('/settings', (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.redirect('/');
  }

  // Rendu de la vue des paramètres (exemple : settings.ejs)
  res.render('miscellaneous/staff/settings', {
    user: req.userData,
    cssFile: '/src/css/settings.css'
  });
});

// Sous-routes : gestion des utilisateurs et des posts
router.use('/users', require('./users'));  // /staff/users
router.use('/posts', require('./posts'));  // /staff/posts

module.exports = router;
