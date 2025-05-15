const express = require('express');
const router = express.Router();

router.get('/', (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.redirect('/');
  }
  
  res.redirect('/staff/dashboard');
});

// Route pour le dashboard
router.get('/dashboard', (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.redirect('/');
  }

  res.render('miscellaneous/dashboard', {
    user: req.userData,
    cssFile: '/src/css/dashboard.css'
  });
});

router.use('/users', require('./users')); 
router.use('/posts', require('./posts')); 
router.use('/tasks', require('./tasks'));
router.use('/produits', require('./produits'));

module.exports = router;
