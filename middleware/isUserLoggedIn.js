// middleware/isUserLoggedIn.js
module.exports = (req, res, next) => {
    if (req.session && req.session.user_id) {
      next(); // utilisateur connecté, on continue
    } else {
      res.redirect('/login'); // pas connecté, on redirige
    }
  };
  