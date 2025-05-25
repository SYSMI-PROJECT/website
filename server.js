const express = require('express');
const session = require('express-session');
const mysql = require('mysql2/promise');
const path = require('path');
require('dotenv').config();
const cookieParser = require('cookie-parser');
const logger = require('./logger');
const methodOverride = require('method-override');


const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(cookieParser());
app.use(methodOverride('_method'));

app.use(session({
  secret: process.env.SESSION_SECRET || 'secretKey',
  resave: false,
  saveUninitialized: false,
}));

app.use(express.static(path.join(__dirname, 'public')));
app.use('/uploads', express.static(path.join(__dirname, 'public/uploads')));
app.use('/effects', express.static(path.join(__dirname, 'effects')));
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

const db = require('./database');
const checkStayConnected = require('./middleware/checkStayConnected');
const sessionUser = require('./middleware/UsersData');
const getUserProfileImage = require('./middleware/profil');
const getTheme = require('./middleware/get_theme');

app.use(checkStayConnected);
app.use(sessionUser);
app.use(getUserProfileImage);
app.use(getTheme);

const authRoutes = require('./routes/auth');
const miscRoutes = require('./routes/miscellaneous');
const uploadRoutes = require('./routes/forms/upload_post');
const staffRoutes = require('./routes/staff');
const formsRoutes = require('./routes/forms');
const serversRoutes = require('./routes/discord');
const api = require('./routes/api');


app.use('/auth', authRoutes);
app.use('/upload', uploadRoutes);
app.use('/staff', staffRoutes);
app.use('/settings', formsRoutes);
app.use('/discord', serversRoutes);
app.use('/api', api);

app.get('/invite', (req, res) => {
  req.session.invite = true;
  res.redirect('/');
});

app.get('/logout-invite', (req, res) => {
  delete req.session.invite;
  res.redirect('/');
});

app.get('/logout', async (req, res) => {
  try {
    if (req.session.invite) {
      delete req.session.invite;
      return res.redirect('/');
    }

    const token = req.cookies.stay_connected;
    if (token) {
      await db.execute('DELETE FROM user_tokens WHERE token = ?', [token]);
      res.clearCookie('stay_connected');
    }

    const { VIP } = req.session;

    req.session.destroy(err => {
      if (err) {
        console.error('Erreur lors de la destruction de session :', err);
        return res.status(500).send('Erreur serveur pendant la déconnexion.');
      }

      if (VIP) {
        res.cookie('VIP', VIP, { maxAge: 3600000, httpOnly: false });
      }

      res.redirect('/');
    });

  } catch (err) {
    console.error('Erreur lors de la déconnexion :', err);
    res.status(500).send('Erreur serveur lors de la déconnexion.');
  }
});

app.get('/login', (req, res) => {
  const stayConnected = req.cookies.stay_connected ? true : false;
  res.render('auth/login', { stayConnected });
});

app.get('/register', (req, res) => {
  res.render('auth/register');
});

app.get('/', async (req, res) => {
  let conn;
  try {
    conn = await db.getConnection();

    const isInvite = req.session.invite === true;
    const user = req.userData;

    let amis = [], nbDemandes = 0, image_content = null, prenom = '', role = '', etoile = '';

    const [feedbacks] = await db.execute(`
      SELECT u.prenom, u.nom, f.feedback, f.rating, u.photo_profil
      FROM feedback f
      JOIN utilisateur u ON f.user_id = u.id
      ORDER BY f.id DESC
      LIMIT 3
    `);

    if (user && !isInvite) {
      const userId = user.id;

      const [rows] = await conn.execute(
        `SELECT u.id, u.prenom, u.photo_profil
         FROM utilisateur u
         JOIN relation r ON (r.demandeur = u.id OR r.receveur = u.id)
         WHERE (r.demandeur = ? OR r.receveur = ?)
         AND r.statut = 1
         AND u.id != ?`,
        [userId, userId, userId]
      );

      amis = rows.map(ami => ({
        id: ami.id,
        prenom: ami.prenom,
        photo_profil: ami.photo_profil || null
      }));

      prenom = user.prenom;
      role = user.role;
      etoile = user.etoile ?? 0;
      image_content = user.photo_profil || null;

      const [notifRes] = await conn.execute(
        'SELECT COUNT(*) as count FROM relation WHERE receveur = ? AND statut = 0',
        [userId]
      );
      nbDemandes = notifRes[0].count;
    }

    res.render('index', {
      isUserLoggedIn: !!user && !isInvite,
      isInvite,
      amis,
      nbDemandes,
      image_content,
      prenom,
      feedbacks,
      etoile,
      role,
      cssFile: res.locals.cssFile,
      user_id: user ? user.id : null,
      user,
      userData: {
        photo_profil: image_content
      }
    });

  } catch (error) {
    console.error("Erreur dans la route / :", error);
    res.status(500).send("Erreur serveur");
  } finally {
    if (conn) conn.release();
  }
});

app.use('/', miscRoutes);

app.listen(PORT, () => {
  logger.success(`✅ Serveur started : http://localhost:${PORT}`);
});
