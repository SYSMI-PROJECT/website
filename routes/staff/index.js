const express = require('express');
const router = express.Router();
const isStaffMiddleware = require('../../middleware/isStaffMiddleware');


router.get('/tasks', (req, res) => {
  res.render('staff/tasks', {
    cssFile: '/src/css/contact.css',
    titre: 'Contact'
  });
});


router.get('/reset-password/:userId', isStaffMiddleware, async (req, res) => {
  const { userId } = req.params;
  const user = await getUserById(userId);
  if (!user) return res.status(404).send('Utilisateur introuvable');
  res.render('staff/reset-password', { user });
});



router.use('/users', require('./users'));  // /staff/users
router.use('/posts', require('./posts'));  // /staff/posts

module.exports = router;