module.exports = (req, res, next) => {
  const isInvite = req.cookies.invite === 'true';

  if (isInvite && !req.session.userId) {
    req.inviteMode = true;
    req.userData = null;
  } else {
    req.inviteMode = false;
  }

  next();
};
