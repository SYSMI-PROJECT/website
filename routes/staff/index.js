const express = require('express');
const router = express.Router();

router.get('/', (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.redirect('/');
  }
  
  res.redirect('/staff/dashboard');
});

router.get('/dashboard', (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.redirect('/');
  }

  res.render('miscellaneous/dashboard', {
    user: req.userData,
    cssFile: '/src/css/dashboard.css'
  });
});

router.get('/tasks', (req, res) => {
  res.render('staff/tasks', {
    cssFile: '/src/css/contact.css',
    titre: 'Contact'
  });
});

router.get('/settings', (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.redirect('/');
  }

  res.render('miscellaneous/staff/settings', {
    user: req.userData,
    cssFile: '/src/css/settings.css'
  });
});

router.use('/users', require('./users'));  // /staff/users
router.use('/posts', require('./posts'));  // /staff/posts

module.exports = router;