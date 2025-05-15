const express = require('express');
const router = express.Router();
const db = require('../../database');

const requireLogin = (req, res, next) => {
  if (!req.session.userId) {
    return res.status(401).send("You must be logged in to access this page.");
  }
  next();
};

router.post('/visibility', requireLogin, (req, res) => {
  const user_id = req.session.userId;

  const visibility = req.body.visibility || 'private';
  const visibility_posts = Array.isArray(req.body.visibility_posts) 
    ? req.body.visibility_posts.join(',') 
    : '';
  const notifications = req.body.notifications === 'enabled' ? 1 : 0;
  const two_factor_auth = req.body.two_factor_auth === 'enabled' ? 1 : 0;
  const dark_mode = req.body.dark_mode === 'enabled' ? 1 : 0;

  const query = `
    INSERT INTO user_settings 
    (user_id, visibility, visibility_posts, notifications, two_factor_auth, dark_mode)
    VALUES (?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
    visibility = ?, visibility_posts = ?, notifications = ?, 
    two_factor_auth = ?, dark_mode = ?
  `;

  const values = [
    user_id, visibility, visibility_posts, notifications, two_factor_auth, dark_mode,
    visibility, visibility_posts, notifications, two_factor_auth, dark_mode
  ];

  db.query(query, values, (err, result) => {
    if (err) {
      console.error('Error updating user settings :', err);
      return res.status(500).send("Error updating settings.");
    }

    res.redirect('/');
  });
});

module.exports = router;
