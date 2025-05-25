module.exports = function(req, res, next) {
  if (req.user && req.user.role === 'staff') {
    return next();
  }
  return res.status(403).send('Accès réservé au staff');
};
